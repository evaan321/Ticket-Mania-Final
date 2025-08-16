<!DOCTYPE html>
<html>
<head>
    <title>E-Ticket</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; }
        .ticket-info { max-width: 600px; margin: 0 auto; }
        .ticket-info p { font-size: 16px; margin: 10px 0; }
        .ticket-code { font-weight: bold; font-size: 18px; }
        .qr-code { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
<h1>E-Ticket</h1>
<div class="ticket-info">
    <p><strong>Event:</strong> {{ $event->name }}</p>
    <p><strong>Ticket Type:</strong> {{ $ticket->ticket_type }}</p>
    <p><strong>Quantity:</strong> {{ $purchasedTicket->quantity }}</p>
    <p><strong>Total Price:</strong> ${{ $purchasedTicket->total_price }}</p>
    <p><strong>Ticket Code:</strong> <span class="ticket-code">{{ $purchasedTicket->ticket_code }}</span></p>
    <p><strong>Date:</strong> {{ $event->event_date->format('Y-m-d H:i') }}</p>
    <p><strong>Location:</strong> {{ $event->location }}</p>
    @if($qrCode)
        <div class="qr-code">
            <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" width="150" height="150">
        </div>
    @endif
</div>
</body>
</html>
