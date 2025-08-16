<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Picture') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Update or remove your profile picture.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update-picture') }}" enctype="multipart/form-data" id="updatePictureForm">
        @csrf
        @method('patch')

        <!-- Profile Picture Preview -->
        <div class="mt-2">
            <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('img/avatar-placeholder.jpg') }}" alt="Profile Picture" class="w-32 h-32 rounded-full object-cover mb-4">
        </div>

        <!-- File Input -->
        <div>
            <x-input-label for="profile_image" :value="__('New Profile Picture')" />
            <x-text-input id="profile_image" name="profile_image" type="file" class="mt-1 block w-full" accept="image/*" />
            <x-input-error class="mt-2" :messages="$errors->get('profile_image')" />
        </div>

        <div class="flex items-center gap-4 mt-4">
            <x-primary-button type="submit">{{ __('Save') }}</x-primary-button>

            @if ($user->profile_image)
                <form action="{{ route('profile.remove-picture') }}" method="post" class="inline">
                    @csrf
                    @method('delete')
                    <x-secondary-button type="submit">{{ __('Remove Picture') }}</x-secondary-button>
                </form>
            @endif
        </div>
    </form>
</section>
