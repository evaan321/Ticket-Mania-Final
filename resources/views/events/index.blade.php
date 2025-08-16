<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Events') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($events->isEmpty())
                        <p class="text-gray-600">No events available.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($events as $event)
                                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                                    <a href="{{ route('events.show', $event) }}">
                                        <img class="rounded-t-lg w-full h-48 object-cover" src="{{ $event->image ? asset('storage/' . $event->image) : '/img/placeholder.jpg' }}" alt="{{ $event->name }}" />
                                    </a>
                                    <div class="p-5">
                                        <a href="{{ route('events.show', $event) }}">
                                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-900">{{ $event->name }}</h5>
                                        </a>
                                        <p class="mb-3 font-normal text-gray-700 dark:text-gray-600 truncate">{{ Str::limit($event->description, 100) }}</p>
                                        <div class="flex items-center text-sm text-gray-600 mt-2">
                                            <img src="https://img.icons8.com/ios-filled/50/000000/marker.png" alt="Location Icon" class="w-5 h-5 mr-2">
                                            <span>{{ $event->location }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-5">
                                        <div class="flex items-center">
                                            <img class="w-10 h-10 rounded-full mr-4" src="{{ $event->user->profile_image ? asset('storage/' . $event->user->profile_image) : '/img/avatar-placeholder.jpg' }}" alt="Avatar of {{ $event->user->name }}">
                                            <div class="text-sm">
                                                <p class="text-gray-900 leading-none">{{ $event->user->name }}</p>
                                                <p class="text-gray-600">{{ $event->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                            {{ __('Details') }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
