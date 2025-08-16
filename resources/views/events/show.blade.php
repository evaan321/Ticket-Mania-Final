<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $event->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Event Details -->
                    <div class="lg:flex">
                        <div class="lg:w-1/2">
                            <img src="{{ $event->image ? asset('storage/' . $event->image) : '/img/placeholder.jpg' }}" alt="{{ $event->name }}" class="w-full h-64 object-cover rounded-lg">
                        </div>
                        <div class="lg:w-1/2 lg:pl-6 mt-4 lg:mt-0">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $event->name }}</h3>
                            <p class="text-gray-700 mt-2 break-words">{{ $event->description }}</p>
                            <p class="text-sm text-gray-600 mt-2">Date: {{ $event->event_date->format('M d, Y H:i') }}</p>
                            <p class="text-sm text-gray-600 mt-1">Location: {{ $event->location }}</p>
                            <p class="text-sm text-gray-600 mt-1">Coordinates: {{ $event->latitude ?? 'N/A' }}, {{ $event->longitude ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600 mt-1">Organized by: {{ $event->user->name }}</p>
                            <div class="mt-4 flex space-x-4">
                                <a href="{{ route('events.edit', $event) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    {{ __('Edit Event') }}
                                </a>
                                <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirm('Are you sure you want to cancel this event?')">
                                        {{ __('Cancel Event') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket Types -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">Ticket Types</h3>
                        @if ($event->tickets->isEmpty())
                            <p class="text-gray-600 mt-2">No ticket types available.</p>
                        @else
                            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($event->tickets as $ticket)
                                    <div class="border border-gray-300 rounded-lg p-4">
                                        <p class="text-gray-900 font-semibold">{{ $ticket->ticket_type }}</p>
                                        <p class="text-gray-600">Price: {{ number_format($ticket->price, 2) }}</p>
                                        <p class="text-gray-600">Total Tickets: {{ $ticket->quantity }}</p>
                                        <p class="text-gray-600">Sold: {{ $ticket->purchasedTickets->sum('quantity') }}</p>

                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Event Statistics -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">Event Statistics</h3>
                        @if ($event->tickets->isEmpty())
                            <p class="text-gray-600 mt-2">No statistics available.</p>
                        @else
                            <div class="mt-4">
                                <canvas id="chart-{{ $event->id }}" class="max-w-full"></canvas>
                            </div>
                        @endif
                    </div>

                    <!-- Location Map -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">Location Map</h3>
                        <div id="map" style="height: 400px; width: 100%;" class="mt-4 rounded-lg"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    <script>
        // Initialize Google Map
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
        window.onload = initMap;

        // Initialize Chart.js
        new Chart(document.getElementById('chart-{{ $event->id }}'), {
            type: 'bar',
            data: {
                labels: [
                    @foreach ($event->tickets as $ticket)
                        '{{ $ticket->ticket_type }}',
                    @endforeach
                ],
                datasets: [
                    {
                        label: 'Tickets Sold',
                        data: [
                            @foreach ($event->tickets as $ticket)
                                {{ $ticket->purchasedTickets->sum('quantity') }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Remaining Capacity',
                        data: [
                            @foreach ($event->tickets as $ticket)
                                {{ $ticket->quantity - $ticket->purchasedTickets->sum('quantity') }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Revenue ($)',
                        data: [
                            @foreach ($event->tickets as $ticket)
                                {{ $ticket->purchasedTickets->sum('total_price') }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    </script>
</x-app-layout>
