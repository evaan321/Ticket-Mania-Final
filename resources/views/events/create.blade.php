<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Event Name</label>
                            <input id="name" name="name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('name') }}" required autofocus />
                            @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select id="category" name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="" disabled selected>Select a category</option>
                                <option value="Concert" {{ old('category') == 'Concert' ? 'selected' : '' }}>Concert</option>
                                <option value="Conference" {{ old('category') == 'Conference' ? 'selected' : '' }}>Conference</option>
                                <option value="Sports" {{ old('category') == 'Sports' ? 'selected' : '' }}>Sports</option>
                                <option value="Workshop" {{ old('category') == 'Workshop' ? 'selected' : '' }}>Workshop</option>
                                <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('description') }}</textarea>
                            @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="event_date" class="block text-sm font-medium text-gray-700">Date and Time</label>
                            <input id="event_date" name="event_date" type="datetime-local" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('event_date') }}" required />
                            @error('event_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                            <input id="location" name="location" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('location') }}" required />
                            @error('location') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                            <input id="latitude" name="latitude" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('latitude') }}" readonly />
                            @error('latitude') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                            <input id="longitude" name="longitude" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('longitude') }}" readonly />
                            @error('longitude') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div id="map" style="height: 300px; width: 100%;" class="mt-4"></div>

                        <div class="mt-4">
                            <label for="image" class="block text-sm font-medium text-gray-700">Event Image</label>
                            <input id="image" name="image" type="file" class="mt-1 block w-full" accept="image/*" required />
                            @error('image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900">Ticket Types</h3>
                            <div id="ticket-types">
                                <div class="ticket-type mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    <div>
                                        <label for="ticket_types[0][ticket_type]" class="block text-sm font-medium text-gray-700">Ticket Type</label>
                                        <input id="ticket_types[0][ticket_type]" name="ticket_types[0][ticket_type]" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('ticket_types.0.ticket_type') }}" required />
                                        @error('ticket_types.0.ticket_type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="ticket_types[0][quantity]" class="block text-sm font-medium text-gray-700">Quantity</label>
                                        <input id="ticket_types[0][quantity]" name="ticket_types[0][quantity]" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('ticket_types.0.quantity') }}" required />
                                        @error('ticket_types.0.quantity') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="ticket_types[0][price]" class="block text-sm font-medium text-gray-700">Price (BDT)</label>
                                        <input id="ticket_types[0][price]" name="ticket_types[0][price]" type="number" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('ticket_types.0.price') }}" required />
                                        @error('ticket_types.0.price') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-ticket-type" class="mt-4 text-blue-600">Add Another Ticket Type</button>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Create Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function initMap() {
            const location = { lat: 23.8758547, lng:90.3795438 };
            const map = new google.maps.Map(document.getElementById('map'), {
                center: location,
                zoom: 13
            });
            const marker = new google.maps.Marker({
                position: location,
                map: map,
                draggable: true
            });
            const autocomplete = new google.maps.places.Autocomplete(document.getElementById('location'), {
                types: ['geocode'],
            });
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    marker.setPosition(place.geometry.location);
                    document.getElementById('latitude').value = place.geometry.location.lat();
                    document.getElementById('longitude').value = place.geometry.location.lng();
                    document.getElementById('location').value = place.formatted_address;
                    map.setCenter(place.geometry.location);
                }
            });
            marker.addListener('dragend', function() {
                document.getElementById('latitude').value = marker.getPosition().lat();
                document.getElementById('longitude').value = marker.getPosition().lng();
            });
        }

        document.getElementById('add-ticket-type').addEventListener('click', () => {

            const container = document.getElementById('ticket-types');
            const index = container.children.length;
            const newTicketType = `
                <div class="ticket-type mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="ticket_types[${index}][ticket_type]" class="block text-sm font-medium text-gray-700">Ticket Type</label>
                        <input id="ticket_types[${index}][ticket_type]" name="ticket_types[${index}][ticket_type]" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required />
                    </div>
                    <div>
                        <label for="ticket_types[${index}][quantity]" class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input id="ticket_types[${index}][quantity]" name="ticket_types[${index}][quantity]" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required />
                    </div>
                    <div>
                        <label for="ticket_types[${index}][price]" class="block text-sm font-medium text-gray-700">Price (USD)</label>
                        <input id="ticket_types[${index}][price]" name="ticket_types[${index}][price]" type="number" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required />
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', newTicketType);
        });
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async></script>
</x-app-layout>
