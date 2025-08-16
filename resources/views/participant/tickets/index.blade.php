<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($purchasedTickets->isEmpty())
                        <p class="text-gray-600">You have not purchased any tickets yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($purchasedTickets as $purchasedTicket)
                                <div class="border border-gray-300 rounded-md p-4">
                                    <h3 class="text-lg font-bold">{{ $purchasedTicket->event->name }}</h3>
                                    <p class="text-gray-600">Ticket Type: {{ $purchasedTicket->ticket->ticket_type }}</p>
                                    <p class="text-gray-600">Quantity: {{ $purchasedTicket->quantity }}</p>
                                    <p class="text-gray-600">Total Price: ${{ number_format($purchasedTicket->total_price, 2) }}</p>
                                    <p class="text-gray-600">Ticket Code: {{ $purchasedTicket->ticket_code }}</p>
                                    <p class="text-gray-600">Purchase Date: {{ $purchasedTicket->created_at->format('M d, Y H:i') }}</p>
                                    <a href="{{ route('participant.tickets.download', $purchasedTicket) }}" class="mt-2 inline-block px-4 py-2 bg-blue-600 text-white rounded-md">Download E-ticket</a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if (session('status'))
                        <p class="mt-4 text-green-600">{{ session('status') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
