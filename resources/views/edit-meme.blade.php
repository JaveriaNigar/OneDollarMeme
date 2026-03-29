<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Edit Meme - {{ config('app.name', 'OneDollarMeme') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/jpeg" href="{{ asset('image/my-logo.jpg') }}">
        <link rel="shortcut icon" type="image/jpeg" href="{{ asset('image/my-logo.jpg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- Emoji Picker -->
        <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Navigation -->
            <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('memes.index') }}">
                                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <x-nav-link :href="route('memes.index')" :active="request()->routeIs('memes.index')">
                                    {{ __('Trending') }}
                                </x-nav-link>

                                <x-nav-link :href="route('upload-meme.create')" :active="request()->routeIs('upload-meme.create')">
                                    {{ __('Upload Meme') }}
                                </x-nav-link>
                            </div>
                        </div>

                        <!-- Settings Dropdown -->
                        @auth
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <x-dropdown placement="bottom-end">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>{{ Auth::user()->name }}</div>

                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                        @endauth
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <x-responsive-nav-link :href="route('memes.index')" :active="request()->routeIs('memes.index')">
                            {{ __('Trending') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('upload-meme.create')" :active="request()->routeIs('upload-meme.create')">
                            {{ __('Upload Meme') }}
                        </x-responsive-nav-link>
                    </div>

                    <!-- Responsive Settings Options -->
                    @auth
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <x-responsive-nav-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-responsive-nav-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-responsive-nav-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-responsive-nav-link>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                <div class="py-12">
                    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h2 class="text-2xl font-bold mb-6">Edit Meme</h2>

                            @if(session('success'))
                                <div class="alert alert-success bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('memes.update', $meme) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-4 relative">
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                    <div class="flex">
                                        <button type="button" id="emojiToggleBtn" class="px-3 border border-r-0 border-gray-300 rounded-l-md bg-gray-50 text-gray-500 hover:bg-gray-100 focus:outline-none">
                                            😀
                                        </button>
                                        <input type="text" name="title" id="title" value="{{ old('title', $meme->title) }}" class="flex-1 min-w-0 px-3 py-2 border border-gray-300 rounded-r-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div id="emojiPickerContainer" class="hidden absolute left-0 z-50 mt-1 shadow-lg">
                                        <emoji-picker id="editEmojiPicker"></emoji-picker>
                                    </div>
                                    @error('title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                                    @if($meme->image_path)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $meme->image_path) }}" alt="Current meme image" class="w-32 h-32 object-cover rounded">
                                        </div>
                                    @endif
                                    <input type="file" name="image" id="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="mt-1 text-sm text-gray-500">Leave blank to keep the current image. Max size: 2MB. Formats: jpeg, png, webp</p>
                                    @error('image')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-center justify-end mt-6 space-x-3">
                                    <a href="{{ route('home') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Cancel
                                    </a>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Update Meme
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const titleInput = document.getElementById('title');
            const emojiToggleBtn = document.getElementById('emojiToggleBtn');
            const emojiPickerContainer = document.getElementById('emojiPickerContainer');
            const editEmojiPicker = document.getElementById('editEmojiPicker');

            // Emoji Toggle
            if (emojiToggleBtn) {
                emojiToggleBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    emojiPickerContainer.classList.toggle('hidden');
                });
            }

            // Close picker on outside click
            document.addEventListener('click', (e) => {
                if (emojiPickerContainer && !emojiPickerContainer.contains(e.target) && e.target !== emojiToggleBtn) {
                    emojiPickerContainer.classList.add('hidden');
                }
            });

            // Insert Emoji
            if (editEmojiPicker) {
                editEmojiPicker.addEventListener('emoji-click', event => {
                    const emoji = event.detail.unicode;
                    const start = titleInput.selectionStart;
                    const end = titleInput.selectionEnd;
                    const text = titleInput.value;
                    titleInput.value = text.substring(0, start) + emoji + text.substring(end);
                    titleInput.focus();
                    titleInput.setSelectionRange(start + emoji.length, start + emoji.length);
                });
            }
        });
        </script>
    </body>
</html>