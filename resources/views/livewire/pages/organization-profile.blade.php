<div class="container px-4 mx-auto my-8 space-y-8 lg:px-8">

    <x-slide id="slide-id" size="full">
        <div class="max-w-4xl p-8 mx-auto bg-white rounded-lg shadow-lg">
            <h2 class="mb-8 text-4xl font-bold text-center text -gray-800">{{ __('Edit Organization') }}</h2>
            <form wire:submit.prevent="updateOrganization" class="space-y-8">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <x-input label="Organization Name *" hint="Enter your organization name"
                        wire:model="organization_name" required />
                    <x-input label="Organization Handle *" hint="Unique identifier (e.g., @org)"
                        wire:model="organization_slug" required />
                </div>

                <div class="mt-4">
                    <x-textarea label="Organization Bio/Description *"
                        hint="Describe your organization (max 250 characters)" maxlength="250" count
                        wire:model="organization_bio" required />
                </div>

                <div class="mt-4">
                    <x-input label="Organization Email *" hint="Contact email for your organization"
                        wire:model="organization_email" type="email" required />
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <x-upload label="Profile Image" hint="Upload a profile image"
                        tip="Drag and drop your profile image here or click to select" wire:model="img_url_profile" />
                    <x-upload label="Background Image" hint="Upload a background image"
                        tip="Drag and drop your background image here or click to select"
                        wire:model="img_url_background" />
                </div>

                <div class="flex items-center justify-between mt-8">
                    <x-link href="{{ route('profile') }}" text="Back to Dashboard"
                        class="text-sm text-gray-600 hover:text-gray-800" />
                    <x-button wire:click="updateOrganization" loading
                        class="px-4 py-2 font-semibold text-white transition duration-200 bg-green-600 rounded hover:bg-green-700">
                        Update Organization
                    </x-button>
                </div>
            </form>
        </div>
    </x-slide>

    <div class="relative">
        @if ($organization->banner_img_url)
            <img src="{{ $organization->banner_img_url }}" alt="Banner"
                class="object-cover w-full h-60 lg:h-80 rounded-xl">
        @else
            <div class="flex items-center justify-center w-full h-60 lg:h-80 bg-slate-200 rounded-xl">
                <x-avatar color="slate" class="w-20 h-20" />
            </div>
        @endif

        <div class="absolute bottom-[-30px] left-6 lg:left-10">
            @if ($organization->profile_img_url)
                <x-avatar image="{{ $organization->profile_img_url }}"
                    class="w-24 h-24 border-4 border-white rounded-full" />
            @else
                <x-avatar color="slate" class="w-24 h-24 border-4 border-white rounded-full" />
            @endif
        </div>
    </div>

    <div class="pl-6 mt-16 space-y-4 lg:pl-10">
        <h1 class="text-4xl font-bold">{{ $organization->organization_name }}</h1>
        <p class="text-lg text-gray-600">@ {{ $organization->organization_slug }}</p>
        <p class="mt-2 text-gray-800">{{ $organization->organization_bio }}</p>

        <div class="mt-4">
            <x-clipboard text="{{ url('/organization/' . $organization->organization_slug) }}" icon="clipboard"
                class="px-4 py-2 text-base rounded-lg shadow-sm bg-slate-100" />
        </div>

        @if (Auth::check() && Auth::user()->id === $organization->user_id)
            <div class="mt-4">
                <x-button x-on:click="$slideOpen('slide-id')">
                    Edit
                </x-button>
            </div>
        @endif
    </div>

    <x-slide id="create-event-slide" size="full" x-on:open="initializeMap">
        <div class="max-w-4xl p-8 mx-auto bg-white rounded-lg shadow-lg">
            <h2 class="mb-8 text-4xl font-bold text-center text-gray-800">{{ __('Create Event') }}</h2>
            <form wire:submit.prevent="createEvent" class="space-y-8">
                <x-errors />
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <x-input label="Event Name *" hint="Enter event name" wire:model="event_name" required />
                    <x-input label="Event Slug *" hint="Unique identifier for the event" wire:model="event_slug"
                        required />
                </div>
                <div class="mt-4">
                    <x-input label="Address *" hint="Address" wire:model="event_address_string" disabled required />
                    <div id="map" style="width: 100%; height: 600px; position: relative; outline-style: none;"
                        wire:ignore></div>
                </div>
                <div class="mt-4">
                    <x-textarea label="Event Description *" hint="Describe the event (max 500 characters)"
                        maxlength="500" count wire:model="event_description" required />
                </div>
                <div class="mt-4">
                    <x-date label="Event Date Range *" hint="Select start and end dates" range wire:model="event_date"
                        required />
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <x-time label="Event Start Time *" hint="Select the start time" wire:model="event_time_start"
                        required />
                    <x-time label="Event End Time *" hint="Select the end time" wire:model="event_time_end" required />
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <x-upload label="Event Image" hint="Upload an image for the event"
                        tip="Drag and drop or click to select" wire:model="event_img_url" />
                    <x-upload label="Event Banner Image" hint="Upload a banner image"
                        tip="Drag and drop or click to select" wire:model="event_img_banner_url" />
                </div>
                <div class="flex items-center justify-between mt-8">
                    <x-button wire:click="createEvent" loading
                        class="px-4 py-2 font-semibold text-white bg-blue-600 rounded hover:bg-blue-700">
                        Create Event
                    </x-button>
                </div>
            </form>
        </div>
    </x-slide>

    <div class="pt-6 mt-10 border-t">
        <x-tab selected="Your Events">
            @if (Auth::check() && Auth::user()->id === $organization->user_id)

                <x-tab.items tab="Your Events">
                    <x-slot:left>
                        <x-icon name="calendar" class="w-6 h-6" />
                    </x-slot:left>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Events</h2>
                        <x-button text="Create Event" x-on:click="$slideOpen('create-event-slide')" />
                    </div>
                    <x-table :headers="$eventHeaders" :rows="$eventRows" filter loading
                        link="/organization/{organization.organization_slug}/{event_slug}" />
                </x-tab.items>
                <x-tab.items tab="Participants">
                    <x-slot:left>
                        <x-icon name="calendar" class="w-6 h-6" />
                    </x-slot:left>
                    <div class="flex items-center justify-between mb-4">
                        <x-modal id="add-user">
                             <div>
                                        <x-input wire:model.live="searchQuery"
                                                            placeholder="Search..." />
                            </div>
                            @if ($searchQuery && $results->isEmpty())
                                <p class="text-center text-gray-500">No users found matching
                                    "{{ $searchQuery }}".</p>
                            @elseif (!$searchQuery)
                                <p class="text-center text-gray-500">Type in the search box to search for users</p>
                            @else
                                @forelse ($results as $user)

                                    <div class="flex items-center p-4 bg-white rounded-lg shadow-md">
                                    <x-avatar :image="asset('/storage/'.$user->img_profile_url)" class="w-16 h-16 mr-4" />
                                    <div class="flex-1">
                                        <h1 class="text-xl font-semibold text-gray-800">{{ $user->username }}</h1>
                                        <h2 class="text-sm text-gray-600">{{ $user->email }}</h2>
                                    </div>
                                    <x-button wire:click="inviteUser('{{ $user->email }}')" class="btn-primary" loading>Invite</x-button>
                                    </div>

                                @empty
                                    <p class="text-center text-gray-500">No users found matching
                                        "{{ $searchQuery }}".</p>
                                @endforelse
                            @endif
                        </x-modal>
                        <h2 class="text-lg font-semibold">Collaberation</h2>
                        <x-button text="Create Event" x-on:click="$modalOpen('add-user')" />
                    </div>
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2 px-4 text-left">Avatar</th>
                                <th class="py-2 px-4 text-left">Name</th>
                                <th class="py-2 px-4 text-left">Email</th>
                                <th class="py-2 px-4 text-left">Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignedUsers as $user)
                                <tr>
                                    <td class="py-2 px-4 text-left"><img
                                            src="{{ asset($user->user->profile_img_url) }}" class=""
                                            alt=""></td>
                                    <td class="py-2 px-4 text-left"><img
                                            src="{{ $user->user->first_name . $user->user->middle_name . $user->user->last_name }}"
                                            class="" alt=""></td>
                                    <td class="py-2 px-4 text-left"><img src="{{ $user->user->email }}"
                                            class="" alt=""></td>
                                    <td>
                                        <x-select.styled :options="[1, 2, 3]" />
                                        <x-button>Update</x-button>
                                        <x-button>Remove</x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="text-center w-full">
                                    <td>
                                        <h1>No users invited in this organization</h1>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-tab.items>
            @else
                <x-tab.items tab="Your Events">
                    <x-slot:left>
                        <x-icon name="calendar" class="w-6 h-6" />
                    </x-slot:left>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Events</h2>
                    </div>
                    <x-table :headers="$eventHeaders" :rows="$eventRows" filter loading link="/events/{event_slug}" />

                </x-tab.items>
            @endif
        </x-tab>
    </div>
    <script>
        const dumagueteCenter = [9.30697, 123.30877];
        const radius = 5000;
        let map;
        let marker;

        function initializeMap() {
            setTimeout(() => {
                if (!map) {
                    map = L.map('map').setView(dumagueteCenter, 15);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 24,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    L.circle(dumagueteCenter, {
                        color: 'blue',
                        fillColor: '#30f',
                        fillOpacity: 0.2,
                        radius: radius
                    }).addTo(map);

                    map.on('click', function(e) {
                        const {
                            lat,
                            lng
                        } = e.latlng;

                        if (isWithinRadius(e.latlng)) {
                            if (marker) {
                                marker.setLatLng(e.latlng);
                            } else {
                                marker = L.marker([lat, lng]).addTo(map);
                            }

                            // Set the latitude and longitude in Livewire
                            @this.set('event_latitude', lat);
                            @this.set('event_longitude', lng);

                            // Get and set the address from coordinates
                            getAddressFromCoordinates(lat, lng).then(address => {
                                @this.set('event_address_string', address);
                            });
                        } else {
                            // Optional: Notify the user if the location is out of radius
                            alert('The selected location is outside the allowed radius.');
                        }
                    });
                } else {
                    map.invalidateSize();
                }
            }, 1000);
        }

        function isWithinRadius(latlng) {
            const distance = map.distance(dumagueteCenter, latlng);
            return distance <= radius;
        }

        async function getAddressFromCoordinates(lat, lng) {
            try {
                // Use OpenStreetMap's Nominatim API for reverse geocoding
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                const data = await response.json();

                if (data && data.display_name) {
                    return data.display_name; // Return the formatted address
                } else {
                    return 'Address not found';
                }
            } catch (error) {
                console.error('Error fetching address:', error);
                return 'Unable to retrieve address';
            }
        }
    </script>

</div>
