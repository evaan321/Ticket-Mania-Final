<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Carousel Section -->
                    <div class="relative w-full overflow-hidden">
                        <div id="event-carousel" class="flex transition-transform duration-500 ease-in-out" style="width: 100%;">
                            @foreach ($events as $event)
                                <div class="min-w-full">
                                    <img src="{{ $event->image ? asset('storage/' . $event->image) : '/img/placeholder.jpg' }}" alt="{{ $event->name }}" class="w-full h-64 object-cover">
                                </div>
                            @endforeach
                        </div>
                        <button id="prev-slide" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white p-2 rounded-full hover:bg-gray-600">
                            ‹
                        </button>
                        <button id="next-slide" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white p-2 rounded-full hover:bg-gray-600">
                            ›
                        </button>
                    </div>

                    <!-- Events Grid -->
                    <h3 class="text-lg font-medium text-gray-900 mb-4 mt-6">All Events</h3>
                    @if ($events->isEmpty())
                        <p class="text-gray-600">No events available.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($events as $event)
                                <div class="max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm">
                                    <img class="rounded-t-lg w-full h-48 object-cover" src="{{ $event->image ? asset('storage/' . $event->image) : '/img/placeholder.jpg' }}" alt="{{ $event->name }}">
                                    <div class="p-5">
                                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">{{ $event->name }}</h5>
                                        <p class="mb-3 font-normal text-gray-700 truncate">{{ Str::limit($event->description, 100) }}</p>
                                        <div class="flex items-center text-sm text-gray-600 mt-2">
                                            <img src="https://img.icons8.com/ios-filled/50/000000/marker.png" alt="Location Icon" class="w-5 h-5 mr-2">
                                            <span>{{ $event->location }}</span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 mt-1">
                                            <img src="{{ $event->user->profile_image ? asset('storage/' . $event->user->profile_image) : '/img/avatar-placeholder.jpg' }}" alt="Organizer Avatar" class="w-5 h-5 rounded-full mr-2">
                                            <span>{{ $event->user->name }}</span>
                                        </div>
                                    </div>
                                    <div class="p-5">
                                        <!-- Details button intentionally hidden -->
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


        <script>
            const carousel = document.getElementById('event-carousel');
            const prevButton = document.getElementById('prev-slide');
            const nextButton = document.getElementById('next-slide');
            let currentIndex = 0;
            const totalSlides = carousel.children.length;

            function updateCarousel() {
                carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
            }

            nextButton.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % totalSlides;
                updateCarousel();
            });

            prevButton.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                updateCarousel();
            });

            // Auto-slide every 5 seconds
            setInterval(() => {
                currentIndex = (currentIndex + 1) % totalSlides;
                updateCarousel();
            }, 5000);
        </script>

</x-app-layout>
