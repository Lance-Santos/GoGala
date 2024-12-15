<div x-data="{ viewType: @entangle('viewType'), search: @entangle('search') }" class="container px-4 mx-auto my-8 space-y-8 lg:px-8">
    <!-- Search and Filter Row -->
    <div class="flex flex-col w-full space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0">
        <div class="flex items-center w-full space-x-4 lg:w-1/2">
            <!-- Search Input with Full Width -->
            <div class="flex-1">
                <x-input wire:model.debounce.500ms="search" label="Search Favorites" hint="Search by event name" class="w-full" />
            </div>
            <x-button wire:click="loadFavorites" class="flex-shrink-0">
                Search
            </x-button>
            <x-button @click="$wire.loadFavorites()" class="flex-shrink-0 text-white bg-green-600 hover:bg-green-700">
                Refresh
            </x-button>
        </div>
        <div class="flex w-full mt-4 space-x-4 lg:w-auto lg:mt-0">
            <x-button @click="viewType = 'list'" class="flex-1 text-gray-800 border border-gray-300 hover:bg-gray-200">
                List View
            </x-button>
            <x-button @click="viewType = 'grid'" class="flex-1 text-gray-800 border border-gray-300 hover:bg-gray-200">
                Grid View
            </x-button>
        </div>
    </div>

    <!-- Scrollable Cards Display -->
    <div class="overflow-y-scroll h-[80vh] relative min-h-4 overflow-x-clip border rounded">
        <x-loading loading="loadFavorites" />

        <div :class="viewType === 'list' ? 'space-y-6' : 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 p-5'">
            @foreach ($favorites as $favorite)
                @php
                    $event = $favorite->event; // Access the event related to the favorite
                @endphp

                <!-- List View Card -->
                <div x-show="viewType === 'list'" class="relative p-4 m-5 overflow-visible transition-all transform bg-white rounded-lg shadow-lg hover:shadow-xl group">
                    <div class="flex flex-col md:flex-row md:items-start">
                        <img src="{{ $event->event_img_url }}" alt="{{ $event->event_name }}" class="object-cover w-full h-48 rounded-md md:w-48 md:h-32">
                        <div class="flex-1 p-4">
                            <h3 class="text-xl font-bold text-gray-800 transition-colors hover:text-blue-600">{{ $event->event_name }}</h3>
                            <p class="mt-2 text-gray-600">{{ Str::limit($event->event_description, 100) }}</p>

                            <div class="flex items-center mt-4 space-x-3">
                                <img src="{{ $event->organization->img_url_profile }}" alt="{{ $event->organization->organization_name }}" class="w-10 h-10 rounded-full">
                                <a href="{{ url('organization/'.$event->organization->organization_slug) }}" class="font-medium text-gray-800 hover:underline">{{ $event->organization->organization_name }}</a>
                            </div>

                            <div class="flex justify-between mt-4 text-sm text-gray-500">
                                <span>
                                    <i class="mr-1 fas fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($event->event_date_start)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($event->event_date_end)->format('M d, Y') }}
                                </span>
                                <span>
                                    <i class="mr-1 fas fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($event->event_time_start)->format('h:i A') }} - {{ \Carbon\Carbon::parse($event->event_time_end)->format('h:i A') }}
                                </span>
                            </div>

                            <x-button href="{{route('event.view',['eventSlug' => $event->event_slug])}}" class="w-full mt-6 text-white bg-blue-600 hover:bg-blue-700">
                                View Details
                            </x-button>
                        </div>
                    </div>
                </div>

                <!-- Grid View Card -->
                <div x-show="viewType === 'grid'" class="relative p-4 overflow-hidden transition-all transform bg-white rounded-lg shadow-xl">
                    <img src="{{ $event->event_img_url }}" alt="{{ $event->event_name }}" class="object-cover w-full h-48">

                    <div class="p-6">
                        <h3 class="text-2xl font-semibold text-gray-800">{{ $event->event_name }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ Str::limit($event->event_description, 100) }}</p>

                        <div class="flex items-center mt-4 space-x-3">
                            <img src="{{ $event->organization->img_url_profile }}" alt="{{ $event->organization->organization_name }}" class="w-10 h-10 rounded-full">
                            <a href="{{ url('organization/'.$event->organization->organization_slug) }}" class="font-medium text-gray-800 hover:underline">{{ $event->organization->organization_name }}</a>
                        </div>

                        <div class="flex justify-between mt-4 text-sm text-gray-500">
                            <span>
                                {{ \Carbon\Carbon::parse($event->event_date_start)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($event->event_date_end)->format('M d, Y') }}
                            </span>
                            <span>
                                {{ \Carbon\Carbon::parse($event->event_time_start)->format('h:i A') }} - {{ \Carbon\Carbon::parse($event->event_time_end)->format('h:i A') }}
                            </span>
                        </div>
                        <x-button href="{{route('event.view',['eventSlug' => $event->event_slug])}}" class="w-full mt-6 text-white bg-blue-600 hover:bg-blue-700">
                            View Details
                        </x-button>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($favorites->isEmpty())
            <p class="mt-6 text-center text-gray-500">You don't have any favorites yet.</p>
        @endif
    </div>
</div>
