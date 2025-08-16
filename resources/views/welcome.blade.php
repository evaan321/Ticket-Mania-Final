<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TicketMania</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        .carousel-item {
            transition: transform 0.5s ease-in-out;
        }
        .highlight-card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
        .footer-bg {
            background: linear-gradient(135deg, #1a2a6c 0%, #2a5298 50%, #1e3c72 100%);
        }
        .section-bg {
            background: linear-gradient(135deg, #f0f4f8 0%, #e0e7ff 100%);
        }
    </style>
</head>
<body class="bg-gray-50 font-poppins text-gray-800">
<!-- Header -->
<header class="bg-white shadow-md py-6" x-data="{ open: false }">
    <div class="container mx-auto flex justify-between items-center px-6">
        <h1 class="text-3xl font-bold text-indigo-700">TicketMania</h1>
        <nav class="space-x-6">
            @auth
                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" x-data="{ showConfirm: false }" x-ref="logoutForm" @submit.prevent="showConfirm ? $refs.logoutForm.submit() : (showConfirm = true)">
                    @csrf
                    <button type="button" @click="showConfirm = true" class="text-red-600 hover:text-red-800 font-semibold px-4 py-2 rounded transition-colors" x-show="!showConfirm">
                        Logout
                    </button>
                    <span x-show="showConfirm" class="flex items-center space-x-2">
                        <span class="text-gray-700">Are you sure?</span>
                        <button type="submit" class="text-red-600 hover:text-red-800 px-2">Yes</button>
                        <button type="button" @click="showConfirm = false" class="text-indigo-600 hover:text-indigo-800 px-2">No</button>
                    </span>
                </form>

                <!-- Dashboard Button -->
                @if(Auth::check())
                    <a href="{{ Auth::user()->role === 'organizer' ? route('events.index') : route('participant.events.index') }}" class="text-indigo-700 hover:text-indigo-900 font-semibold px-4 py-2 rounded transition-colors">
                        Dashboard
                    </a>
                @else
                    <a href="#" class="text-indigo-700 hover:text-indigo-900 font-semibold px-4 py-2 rounded transition-colors" disabled>
                        Dashboard (Error)
                    </a>
                @endif
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="text-indigo-700 hover:text-indigo-900 font-semibold px-4 py-2 rounded transition-colors">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-white bg-indigo-600 hover:bg-indigo-700 font-semibold px-4 py-2 rounded transition-colors">Register</a>
                    @endif
                @endif
            @endauth
        </nav>
    </div>
</header>

<!-- Hero Section with Carousel -->
<section class="py-16 bg-indigo-50">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-bold text-center mb-10 text-gray-800">Discover Exciting Events</h2>
        <div class="relative w-full overflow-hidden rounded-xl shadow-2xl">
            <div id="event-carousel" class="flex transition-transform duration-500 ease-in-out" style="width: 100%;">
                @foreach ($events as $event)
                    <div class="min-w-full carousel-item">
                        <img src="{{ $event->image ? asset('storage/' . $event->image) : '/img/placeholder.jpg' }}" alt="{{ $event->name }}" class="w-full h-80 object-cover rounded-lg">
                        <div class="absolute inset-0 flex items-center justify-center">
                        </div>
                    </div>
                @endforeach
            </div>
            <button id="prev-slide" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 text-indigo-700 p-3 rounded-full hover:bg-opacity-100">
                ‹
            </button>
            <button id="next-slide" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 text-indigo-700 p-3 rounded-full hover:bg-opacity-100">
                ›
            </button>
        </div>
    </div>
</section>

<!-- Events Grid -->
<section class="py-16">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-bold text-center mb-10 text-gray-800">Explore All Events</h2>
        @if ($events->isEmpty())
            <p class="text-center text-gray-600 text-xl">No events available at the moment.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($events as $event)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <img src="{{ $event->image ? asset('storage/' . $event->image) : '/img/placeholder.jpg' }}" alt="{{ $event->name }}" class="w-full h-56 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">{{ $event->name }}</h3>
                            <p class="text-gray-600 mb-4 truncate">{{ Str::limit($event->description, 120) }}</p>
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm0 18a8 8 0 100-16 8 8 0 000 16z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $event->location }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <img src="{{ $event->user->profile_image ? asset('storage/' . $event->user->profile_image) : '/img/avatar-placeholder.jpg' }}" alt="Organizer Avatar" class="w-6 h-6 rounded-full mr-2">
                                <span>{{ $event->user->name }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- Featured Categories Section -->
<section class="py-16 section-bg">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-bold text-center mb-10 text-gray-800">Featured Categories</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 text-center">
                <svg class="w-12 h-12 mx-auto text-indigo-600 mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                    <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm0 18a8 8 0 100-16 8 8 0 000 16z" clip-rule="evenodd"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800">Music & Concerts</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 text-center">
                <svg class="w-12 h-12 mx-auto text-indigo-600 mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                    <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm0 18a8 8 0 100-16 8 8 0 000 16z" clip-rule="evenodd"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800">Workshops</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 text-center">
                <svg class="w-12 h-12 mx-auto text-indigo-600 mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                    <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm0 18a8 8 0 100-16 8 8 0 000 16z" clip-rule="evenodd"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800">Sports</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 text-center">
                <svg class="w-12 h-12 mx-auto text-indigo-600 mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                    <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm0 18a8 8 0 100-16 8 8 0 000 16z" clip-rule="evenodd"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800">Festivals</h3>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-bold text-center mb-10 text-gray-800">What Our Users Say</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-xl shadow-md">
                <p class="text-gray-600 italic mb-4">"Amazing platform to find local events! The interface is so user-friendly."</p>
                <p class="font-semibold text-indigo-600">- Jane Doe</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md">
                <p class="text-gray-600 italic mb-4">"Secured my tickets in seconds. Highly recommend TicketMania!"</p>
                <p class="font-semibold text-indigo-600">- John Smith</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer-bg text-white py-12">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <h3 class="text-xl font-bold mb-4">TicketMania</h3>
                <p class="text-gray-300">Your ultimate destination for discovering, exploring, and booking tickets to the most exciting events worldwide. Join us today!</p>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Home</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Events</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Categories</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Contact Us</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-4">Support</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">FAQ</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Help Center</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Terms of Service</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Privacy Policy</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-4">Connect With Us</h3>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-300 hover:text-white transition-colors"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <p class="mt-4 text-gray-300">Email: support@ticketmania.com</p>
                <p class="text-gray-300">Phone: +1-800-555-1234</p>
            </div>
        </div>
        <div class="text-center border-t border-gray-700 pt-6">
            <p class="text-gray-400">&copy; 2025 TicketMania. All rights reserved. | Designed with ❤️</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
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

    setInterval(() => {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateCarousel();
    }, 5000);
</script>
</body>
</html>



