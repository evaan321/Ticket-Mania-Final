<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Event Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold">{{ $event->name }}</h3>
                    <p class="mt-2 text-gray-600">Category: {{ $event->category ?? 'General' }}</p>
                    <p class="mt-2 text-gray-600">Date: {{ $event->event_date->format('M d, Y H:i') }}</p>
                    <p class="mt-2 text-gray-600">Location: {{ $event->location }}</p>
                    <p class="mt-2 text-gray-600">Organized by: {{ $event->user->name }}</p>
                    <p class="mt-4 text-gray-700">{{ $event->description }}</p>

                    <div class="mt-4">
                        <img src="{{ $event->image ? asset('storage/' . $event->image) : '/img/placeholder.jpg' }}" alt="{{ $event->name }}" class="w-full h-64 object-cover rounded">
                    </div>

                    <div class="mt-4">
                        <div id="map" style="height: 300px; width: 100%;"></div>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-lg font-medium text-gray-900">Available Tickets</h4>
                        @if ($event->tickets->isEmpty())
                            <p class="text-gray-600">No tickets available.</p>
                        @else
                            <ul class="mt-2 space-y-2">
                                @foreach ($event->tickets as $ticket)
                                    <li class="flex justify-between">
                                        <span>{{ $ticket->ticket_type }} - ${{ number_format($ticket->price, 2) }}</span>
                                        <span>Available: {{ $ticket->quantity - $ticket->purchasedTickets->sum('quantity') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    @if ($event->tickets->isNotEmpty())
                        <div class="mt-6">
                            <h4 class="text-lg font-medium text-gray-900">Purchase Tickets</h4>
                            <form method="POST" action="{{ route('participant.events.purchase', $event) }}">
                                @csrf
                                <div class="mt-4">
                                    <label for="ticket_id" class="block text-sm font-medium text-gray-700">Ticket Type</label>
                                    <select id="ticket_id" name="ticket_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                        <option value="" disabled selected>Select a ticket type</option>
                                        @foreach ($event->tickets as $ticket)
                                            @if ($ticket->quantity - $ticket->purchasedTickets->sum('quantity') > 0)
                                                <option value="{{ $ticket->id }}">{{ $ticket->ticket_type }} - ${{ number_format($ticket->price, 2) }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('ticket_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="mt-4">
                                    <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                                    <input id="quantity" name="quantity" type="number" min="1" max="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required />
                                    @error('quantity') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Pay with Stripe</button>
                                </div>
                            </form>
                        </div>
                    @endif

                    @if (session('status'))
                        <p class="mt-4 text-green-600">{{ session('status') }}</p>
                    @endif
                    @if ($errors->has('payment'))
                        <p class="mt-4 text-red-600">{{ $errors->first('payment') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function initMap() {
            const location = { lat: {{ $event->latitude ?? 40.7128 }}, lng: {{ $event->longitude ?? -74.0060 }} };
            const map = new google.maps.Map(document.getElementById('map'), {
                center: location,
                zoom: 13
            });
            new google.maps.Marker({
                position: location,
                map: map
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async></script>
</x-app-layout>
