<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\PurchasedTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\PDF as DomPDF;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use GuzzleHttp\Client;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('user');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($date = $request->query('date')) {
            if ($date === 'upcoming') {
                $query->where('event_date', '>=', now());
            } elseif ($date === 'past') {
                $query->where('event_date', '<', now());
            }
        }

        if ($price_min = $request->query('price_min')) {
            $query->whereHas('tickets', function ($q) use ($price_min) {
                $q->where('price', '>=', $price_min);
            });
        }
        if ($price_max = $request->query('price_max')) {
            $query->whereHas('tickets', function ($q) use ($price_max) {
                $q->where('price', '<=', $price_max);
            });
        }

        if ($location = $request->query('location')) {
            $query->where('location', 'like', "%{$location}%");
        }

        $events = $query->get();
        return view('participant.events.index', compact('events'));
    }

    public function show(Event $event)
    {
        $event->load('tickets', 'user');
        return view('participant.events.show', compact('event'));
    }

    public function purchase(Request $request, Event $event)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id,event_id,' . $event->id,
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);
        $purchasedQuantity = $ticket->purchasedTickets()->sum('quantity');
        $availableQuantity = $ticket->quantity - $purchasedQuantity;

        if ($request->quantity > $availableQuantity) {
            return back()->withErrors(['quantity' => 'Not enough tickets available.']);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $totalPrice = $ticket->price * $request->quantity * 100; // Stripe expects cents

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $event->name . ' - ' . $ticket->ticket_type,
                        ],
                        'unit_amount' => $ticket->price * 100,
                    ],
                    'quantity' => $request->quantity,
                ]],
                'mode' => 'payment',
                'success_url' => route('participant.payment.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('participant.payment.cancel', [], true),
                'metadata' => [
                    'event_id' => $event->id,
                    'ticket_id' => $ticket->id,
                    'quantity' => $request->quantity,
                    'user_id' => Auth::id(),
                ],
            ]);

            return redirect()->away($session->url);
        } catch (\Exception $e) {
            return back()->withErrors(['payment' => 'Payment initiation failed: ' . $e->getMessage()]);
        }
    }

    public function paymentSuccess(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::retrieve($request->query('session_id'));

        if ($session->payment_status !== 'paid') {
            return redirect()->route('participant.events.index')->withErrors(['payment' => 'Payment not completed.']);
        }

        $metadata = $session->metadata;

        $ticket = Ticket::findOrFail($metadata->ticket_id);
        $event = Event::findOrFail($metadata->event_id);
        $quantity = $metadata->quantity;
        $userId = $metadata->user_id;

        $purchasedQuantity = $ticket->purchasedTickets()->sum('quantity');
        $availableQuantity = $ticket->quantity - $purchasedQuantity;

        if ($quantity > $availableQuantity) {
            return redirect()->route('participant.events.index')->withErrors(['quantity' => 'Not enough tickets available.']);
        }

        $ticketCode = Str::uuid()->toString();
        $totalPrice = $ticket->price * $quantity;

        $purchasedTicket = PurchasedTicket::create([
            'event_id' => $event->id,
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'quantity' => $quantity,
            'total_price' => $totalPrice,
            'ticket_code' => $ticketCode,
        ]);

        // Fetch QR code from goQR.me API with ticket code and user name
        $user = Auth::user();
        $qrData = "ticket code: {$ticketCode}\nbuyer: {$user->name}";
        $client = new Client();
        try {
            $response = $client->get('https://api.qrserver.com/v1/create-qr-code/', [
                'query' => [
                    'size' => '150x150',
                    'data' => $qrData,
                ]
            ]);
            $qrCode = base64_encode($response->getBody()->getContents());
        } catch (\Exception $e) {
            \Log::error('QR code generation failed: ' . $e->getMessage());
            $qrCode = null; // Fallback to no QR code if API fails
        }

        // Generate E-ticket PDF
        $pdf = app('dompdf.wrapper')->loadView('participant.tickets.pdf', [
            'event' => $event,
            'ticket' => $ticket,
            'purchasedTicket' => $purchasedTicket,
            'qrCode' => $qrCode,
        ]);

        $pdfPath = 'tickets/ticket_' . $purchasedTicket->id . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());
        $purchasedTicket->update(['ticket_code' => $ticketCode]);

        return redirect()->route('participant.tickets.index')->with('status', 'Payment successful! E-ticket generated.');
    }

    public function paymentCancel()
    {
        return redirect()->route('participant.events.index')->withErrors(['payment' => 'Payment cancelled.']);
    }

    public function tickets()
    {
        $purchasedTickets = PurchasedTicket::where('user_id', Auth::id())->with('event', 'ticket')->get();
        return view('participant.tickets.index', compact('purchasedTickets'));
    }

    public function download(PurchasedTicket $purchasedTicket)
    {
        if ($purchasedTicket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $pdfPath = 'tickets/ticket_' . $purchasedTicket->id . '.pdf';
        if (!Storage::disk('public')->exists($pdfPath)) {
            abort(404, 'E-ticket not found');
        }

        return Storage::disk('public')->download($pdfPath, 'e-ticket-' . $purchasedTicket->ticket_code . '.pdf');
    }
}
