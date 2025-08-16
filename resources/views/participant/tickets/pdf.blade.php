<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1 {
            background-color: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 20px;
            margin: 0;
        }
        .ticket-container {
            width: 100%;
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .ticket-info {
            display: flex;
            flex-direction: column;
            gap: 15px;
            font-size: 16px;
        }
        .ticket-info p {
            margin: 0;
            line-height: 1.6;
        }
        .ticket-info p strong {
            color: #2c3e50;
        }
        .ticket-code {
            font-size: 18px;
            font-weight: bold;
            background-color: #eaf0f4;
            padding: 5px;
            border-radius: 4px;
            display: inline-block;
            color: #2980b9;
        }
        .qr-code {
            text-align: center;
            margin-top: 30px;
        }
        .qr-code img {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .ticket-footer {
            background-color: #ecf0f1;
            padding: 15px;
            text-align: center;
            font-size: 14px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .ticket-footer p {
            margin: 5px 0;
            color: #7f8c8d;
        }
    </style>
</head>
<body>

<h1>E-Ticket</h1>

<div class="ticket-container">
    <div class="ticket-info">
        <p><strong>Event:</strong> {{ $event->name }}</p>
        <p><strong>Ticket Type:</strong> {{ $ticket->ticket_type }}</p>
        <p><strong>Quantity:</strong> {{ $purchasedTicket->quantity }}</p>
        <p><strong>Total Price:</strong> ${{ $purchasedTicket->total_price }}</p>
        <p><strong>Ticket Code:</strong> <span class="ticket-code">{{ $purchasedTicket->ticket_code }}</span></p>
        <p><strong>Date:</strong> {{ $event->event_date->format('Y-m-d H:i') }}</p>
        <p><strong>Location:</strong> {{ $event->location }}</p>
    </div>

    @if($qrCode)
        <div class="qr-code">
            <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" width="150" height="150">
        </div>
    @endif
</div>

<div class="ticket-footer">
    <p>Thank you for your purchase! Enjoy the event.</p>
    <p>If you have any questions, contact support at support@event.com</p>
</div>

</body>
</html>
