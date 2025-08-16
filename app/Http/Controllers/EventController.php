<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('user')->where('user_id', Auth::id())->get();
        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Concert,Conference,Sports,Workshop,Other',
            'description' => 'required|string',
            'event_date' => 'required|date|after:now',
            'location' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ticket_types' => 'required|array|min:1',
            'ticket_types.*.ticket_type' => 'required|string|max:100',
            'ticket_types.*.quantity' => 'required|integer|min:1',
            'ticket_types.*.price' => 'required|numeric|min:0',
        ]);

        $imagePath = $request->file('image')->store('event_images', 'public');

        $event = Event::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'image' => $imagePath,
            'user_id' => Auth::id(),
        ]);

        foreach ($request->ticket_types as $ticketType) {
            $event->tickets()->create([
                'ticket_type' => $ticketType['ticket_type'],
                'quantity' => $ticketType['quantity'],
                'price' => $ticketType['price'],
            ]);
        }

        return redirect()->route('events.show', $event)->with('status', 'Event created!');
    }

    public function show(Event $event)
    {
        $event->load(['tickets.purchasedTickets', 'user']);
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $event->load('tickets');
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Concert,Conference,Sports,Workshop,Other',
            'description' => 'required|string',
            'event_date' => 'required|date|after:now',
            'location' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ticket_types' => 'required|array|min:1',
            'ticket_types.*.ticket_type' => 'required|string|max:100',
            'ticket_types.*.quantity' => 'required|integer|min:1',
            'ticket_types.*.price' => 'required|numeric|min:0',
        ]);

        $data = $request->only(['name', 'category', 'description', 'event_date', 'location', 'latitude', 'longitude']);
        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $imagePath = $request->file('image')->store('event_images', 'public');
            $data['image'] = $imagePath;
        } else {
            $data['image'] = $event->image; // Corrected syntax to retain existing image
        }

        try {
            $event->update($data);
            Log::info('Event updated successfully', ['event_id' => $event->id, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Event update failed: ' . $e->getMessage(), ['event_id' => $event->id, 'data' => $data]);
            return back()->withErrors(['update' => 'Failed to update event. Check logs for details.']);
        }

        $event->tickets()->delete();
        foreach ($request->ticket_types as $ticketType) {
            $event->tickets()->create([
                'ticket_type' => $ticketType['ticket_type'],
                'quantity' => $ticketType['quantity'],
                'price' => $ticketType['price'],
            ]);
        }

        return redirect()->route('events.show', $event)->with('status', 'Event updated!')->with('success', true);
    }

    public function destroy(Event $event)
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        $event->delete();
        return redirect()->route('events.index')->with('status', 'Event cancelled!');
    }
}
