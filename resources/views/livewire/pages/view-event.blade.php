<div class="container mx-auto px-6 my-12 space-y-12 lg:px-8">
    @if ($event->event_type === 'seating' || $event->event_type === 'ticket')
        <div class="fixed z-50 bottom-8 right-8">
            <x-slide id="cart-slide" size="2xl">
                <div class="p-6 rounded-lg bg-gray-50">
                    <h2 class="mb-6 text-2xl font-bold text-gray-900">🛒 Your Cart</h2>

                    @forelse ($cart as $index => $item)
                        <div class="flex items-center justify-between p-4 mb-4 bg-white border rounded-lg">
                            <!-- Ticket Information -->
                            <div>
                                <p class="text-lg font-semibold text-gray-800">{{ $item['ticket_type'] }}</p>
                                <p class="text-gray-600">Price: ${{ number_format($item['ticket_price'], 2) }}</p>

                                <!-- Show Seat ID if exists -->
                                @if (!empty($item['seat_id']))
                                    <p class="text-gray-600">Seat: {{ $item['seat_id'] }}</p>
                                @endif
                            </div>

                            <!-- Quantity Input -->
                            <div class="flex items-center space-x-4">
                                <x-number wire:model="cart.{{ $index }}.quantity"
                                    class="w-20 text-center bg-gray-100 border-gray-300 rounded-md" min="1"
                                    max="{{ $item['max_quantity'] }}" wire:change="updateTotalPrice"
                                    :disabled="!empty($item['seat_id'])" />
                                <p class="text-lg font-medium text-gray-700">
                                    Total: ${{ number_format($item['ticket_price'] * $item['quantity'], 2) }}
                                </p>
                            </div>
                            <x-button.circle icon="trash"
                                wire:click="removeFromCart({{ $item['ticket_id'] }}, '{{ $item['seat_id'] ?? null }}')"
                                class="text-white bg-red-600 hover:bg-red-700" />
                        </div>
                    @empty
                        <p class="text-center text-gray-600">Your cart is empty. Add tickets to see them here!</p>
                    @endforelse
                </div>
                <x-slot:footer end>
                    <div class="flex items-center justify-between mx-5">
                        <p class="text-lg font-medium text-gray-400">Total Price:</p>
                        <h3 class="text-xl font-bold text-gray-900">₱ {{ number_format($totalPrice, 2) }}</h3>
                    </div>
                    <div>
                        <x-button wire:click="createCheckoutSession">Checkout</x-button>
                    </div>
                </x-slot:footer>
            </x-slide>
            <x-button.circle icon="shopping-cart" lg x-on:click="$slideOpen('cart-slide')" />
        </div>
    @endif
    <!-- Event Banner Section -->
    <div class="relative shadow-sm bg-gray-50 rounded-xl">
         @if ($event->event_img_banner_url)
            <img src="{{asset('/storage/'. $event->event_img_banner_url) }}" alt="Event Banner"
                class="object-cover w-full h-72 lg:h-44 rounded-xl">
        @else
            <div class="flex items-center justify-center w-full h-32 lg:h-44 bg-slate-200 rounded-xl">
                <x-avatar color="slate" class="w-20 h-20" />
            </div>
        @endif
        <div class="absolute bottom-[-30px] left-6 lg:left-10">
            @if ($event->event_img_url)
                <x-avatar image="{{ asset('/storage/'. $event->event_img_url) }}" class="w-24 h-24 border-4 border-white rounded-full" />
            @else
                <x-avatar color="slate" class="w-24 h-24 border-4 border-white rounded-full" />
            @endif
        </div>
    </div>

    <div class="p-8 space-y-8 bg-white shadow-md lg:p-12 rounded-xl">
        <!-- Event Title, Description, and Organizer Info -->
        <div class="space-y-6">
            <!-- Event Name with Clipboard and Favorite Button -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <h1 class="text-3xl font-extrabold text-gray-900 lg:text-4xl">{{ $event->event_name }}</h1>

                <div class="flex items-center space-x-4">
                    <!-- Clipboard Button -->
                    <x-clipboard text="{{ url()->current() }}" icon solid />
                    @if ($isFavorited)
                        <x-button.circle icon="heart" lg wire:click="toggleFavorite" flat />
                    @else
                        <x-button.circle lg wire:click="toggleFavorite" flat>
                            <x-icon name="heart" outline class="w-6 h-6" />
                        </x-button.circle>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-4 pt-4 border-t border-gray-200">
                @php
                    $profileImage = $event->organization->img_url_profile;
                    $organizationName = $event->organization->organization_name;
                    $initial = strtoupper(substr($organizationName, 0, 1));
                @endphp

                @if ($profileImage)
                    <img src="{{ asset('/storage/' . $profileImage) }}" alt="{{ $organizationName }} Profile Picture"
                        class="w-12 h-12 rounded-full shadow-sm object-cover">
                @else
                    <div
                        class="w-12 h-12 rounded-full bg-gray-300 text-white flex items-center justify-center shadow-sm">
                        <span class="font-semibold text-xl">{{ $initial }}</span>
                    </div>
                @endif
                <div>
                    <p class="text-sm text-gray-700">
                        Organized by
                        <span class="font-semibold text-indigo-600">@ {{ $organizationName }}</span>
                    </p>
                </div>
            </div>
            <!-- Event Description -->
            <p class="text-base text-gray-600 lg:text-lg">{{ $event->event_description }}</p>
        </div>

        <!-- Event Details Section -->
        <div class="mt-6 text-gray-700 space-y-6">
            <!-- Event Dates -->
            <div class="flex items-start space-x-4">
                <x-icon name="calendar" outline class="w-6 h-6 text-indigo-600" />
                <div>
                    <p class="text-lg font-semibold text-gray-900">Dates</p>
                    <p class="text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($event->event_date_start)->format('F j, Y') }} -
                        {{ \Carbon\Carbon::parse($event->event_date_end)->format('F j, Y') }}
                    </p>
                </div>
            </div>
            <div class="flex items-start space-x-4">
                <x-icon name="clock" outline class="w-6 h-6 text-indigo-600" />
                <div>
                    <p class="text-lg font-semibold text-gray-900">Times</p>
                    <p class="text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($event->event_time_start)->format('h:i A') }} -
                        {{ \Carbon\Carbon::parse($event->event_time_end)->format('h:i A') }}
                    </p>
                </div>
            </div>

            <!-- Event Location -->
            <div class="flex items-start space-x-4">
                <x-icon name="map-pin" outline class="w-6 h-6 text-indigo-600" />
                <div>
                    <p class="text-lg font-semibold text-gray-900">Location</p>
                    <p class="text-sm text-gray-600">{{ $event->event_address_string }}</p>
                </div>
            </div>
        </div>
    </div>

    @if (!$hasEnded)
        <div>
            <div class="p-6 bg-white shadow-sm lg:p-10 rounded-xl">
                @switch($eventType)
                    @case('seating')
                        <h2 class="mb-6 text-2xl font-semibold text-gray-800">Seating Layout</h2>
                        <livewire:layout-purchase @saved="$addToCartSeat" :layout="$elements" :popup="true" />
                    @break

                    @case('ticket')
                        <div>
                            <h2 class="mb-6 text-2xl font-semibold text-gray-800">Available Tickets</h2>
                            <div class="overflow-x-auto flex space-x-6 py-4">
                                <div class="flex items-center justify-start space-x-6 min-w-[280px] max-w-full">
                                    @foreach ($event->tickets as $ticket)
                                        <div
                                            class="flex-none bg-white rounded-lg shadow-md w-72 transform transition-all hover:scale-105 max-w-[280px]">
                                            <div
                                                class="bg-indigo-600 text-white rounded-t-lg py-2 text-center font-semibold text-lg">
                                                {{ $ticket->type }}
                                            </div>

                                            <div class="p-4">
                                                <div class="flex justify-between items-center text-lg font-medium">
                                                    <span
                                                        class="text-2xl font-semibold text-gray-900">${{ $ticket->price }}</span>
                                                    <span class="bg-gray-100 text-gray-800 py-1 px-3 rounded-full text-sm">
                                                        Available: {{ $ticket->quantity }}
                                                    </span>
                                                </div>

                                                <div class="mt-3 text-gray-600 text-sm">
                                                    <p>Get your tickets now and join the event!</p>
                                                </div>
                                            </div>

                                            <div class="px-4 pb-4">
                                                <x-button text="Add to Cart"
                                                    wire:click="addToCart({{ $ticket->id }}, '{{ $ticket->type }}', {{ $ticket->price }},{{ $ticket->quantity }})" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @break

                    @case('free')
                        <h2 class="mb-6 text-2xl font-semibold text-center text-gray-800">Join Event</h2>
                        <div class="flex justify-center">
                            <x-button wire:click="joinEvent" :disabled="$existingPurchase">
                                🚀 Join Event Now
                            </x-button>
                        </div>
                    @break
                @endswitch
            </div>
            <div class="mt-6 z-40 relative">
                <div id="map" class="w-full h-80 rounded-lg shadow-lg overflow-hidden z-10" style="height: 400px;"
                    wire:ignore>
                </div>
                <div class="absolute top-4 right-4 z-20">
                    <x-button flat onclick="navigateToEvent()">
                        Navigate to Event
                    </x-button>
                </div>
            </div>
        </div>
    @else
        <div class="max-w-full mx-auto p-8 bg-white shadow-lg rounded-xl">
            <h2 class="text-3xl font-semibold text-gray-800 mb-6">Rate this Event</h2>

            @if ($existingRating)
                <div class="mb-6">
                <div class="flex items-center space-x-2 mb-2">
                    <p class="text-lg font-semibold text-gray-700">Your Rating:</p>
                    <x-rating :rate="$existingRating->score" color="yellow" readonly static class="text-yellow-500" />
                </div>
                <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                    <p class="text-gray-800">{{ $existingRating->comment }}</p>
                </div>
            </div>

            @else
                <form wire:submit.prevent="submitRating">
                    <div class="mb-6">
                        <label for="score" class="block text-sm font-medium text-gray-700">Rating</label>
                        <x-rating :rate="5" wire:model='score' color="yellow" />
                    </div>

                    <div class="mb-6">
                        <label for="comment" class="block text-sm font-medium text-gray-700">Comment</label>
                        <x-textarea wire:model="comment" id="comment"
                            class="mt-2 block w-full px-4 py-2 border border-gray-300 rounded-lg" rows="4"
                            placeholder="Leave a comment (optional)"></x-textarea>
                    </div>

                    <div class="flex justify-center mt-4">
                        <button type="submit"
                            class="bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700">
                            Submit Rating
                        </button>
                    </div>
                </form>
            @endif
        </div>
    @endif
    <div class="max-w-full mx-auto p-8 bg-white shadow-lg rounded-xl">
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">Submit A Question?</h2>
        <form wire:submit.prevent="submitQuestion">
            @csrf
            <div class="mb-6">
                <x-textarea label="Your Question" name="question" id="question"
                    hint="Describe your question in detail"
                    class="border border-gray-300 p-4 rounded-lg w-full focus:ring-2 focus:ring-indigo-500"
                    placeholder="Type your question here" required wire:model="question" />
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center mt-4">
                <x-button text="Submit Question" />
            </div>
        </form>

        <div class="mt-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Recent Questions</h3>
            <div class="max-h-64 overflow-y-auto mt-4">
                @foreach ($questions as $question)
                    <div class="bg-gray-100 p-4 mb-4 rounded-lg flex items-start">
                        <div class="flex-shrink-0">
                            <!-- Display user avatar or fallback -->
                            <x-avatar :model="auth()->user()->firstname" color="fff" class="w-12 h-12" />
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center space-x-2">
                                <p class="font-semibold text-gray-700">{{ $question->user->username }}</p>
                                <span class="text-sm text-gray-500">{{ $question->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-2 text-gray-600">{{ $question->question }}</p>
                        </div>
                    </div>

                    <!-- Loop through answers for each question -->
                    @foreach ($question->answers as $answer)
                        <div class="bg-gray-50 p-4 mb-4 rounded-lg flex items-start ml-16">
                            <div class="flex-shrink-0">
                                <!-- Display user avatar for answer -->
                                <x-avatar :model="$answer->user->firstname" color="fff" class="w-10 h-10" />
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center space-x-2">
                                    <p class="font-semibold text-gray-700">{{ $answer->user->username }}</p>
                                    <span class="text-sm text-gray-500">{{ $answer->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-2 text-gray-600">{{ $answer->answer_text }}</p>
                            </div>
                        </div>
                    @endforeach
        @endforeach
    </div>
</div>

    </div>
</div>

<script>
    let map;

    document.addEventListener('DOMContentLoaded', () => {
        initializeMap();
    });

    function initializeMap() {
        setTimeout(() => {
            if (!map) {
                const eventLatitude = @json($this->event->event_latitude);
                const eventLongitude = @json($this->event->event_longitude);
                const mapCenter = (eventLatitude && eventLongitude) ? [eventLatitude, eventLongitude] : [
                    9.30697, 123.30877
                ];
                map = L.map('map').setView(mapCenter, 18);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 24,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);
                if (eventLatitude && eventLongitude) {
                    const marker = L.marker([eventLatitude, eventLongitude]).addTo(map);
                    L.circle([eventLatitude, eventLongitude], {
                        color: 'blue',
                        fillColor: '#30f',
                        fillOpacity: 0.3,
                        radius: 100
                    }).addTo(map);
                    getAddressFromCoordinates(eventLatitude, eventLongitude).then(address => {
                        marker.bindPopup(`<b>${address}</b>`).openPopup();
                    });
                    map.setView([eventLatitude, eventLongitude], 16);
                }
            } else {
                map.invalidateSize();
            }
        }, 1000);
    }
    async function getAddressFromCoordinates(lat, lng) {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`
            );
            const data = await response.json();

            if (data && data.display_name) {
                return data.display_name;
            } else {
                return 'Address not found';
            }
        } catch (error) {
            console.error('Error fetching address:', error);
            return 'Unable to retrieve address';
        }
    }

    function navigateToEvent() {
        console.log("Working");
        const eventLatitude = @json($this->event->event_latitude);
        const eventLongitude = @json($this->event->event_longitude);
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const userLatitude = position.coords.latitude;
                const userLongitude = position.coords.longitude;

                const googleMapsUrl =
                    `https://www.google.com/maps/dir/?api=1&origin=${userLatitude},${userLongitude}&destination=${eventLatitude},${eventLongitude}&travelmode=driving`;
                const appleMapsUrl = `https://maps.apple.com/?daddr=${eventLatitude},${eventLongitude}`;
                if (isMobileDevice()) {
                    if (isAndroid()) {
                        window.location.href = googleMapsUrl;
                    } else if (isIOS()) {
                        window.location.href = appleMapsUrl;
                    }
                } else {
                    window.open(googleMapsUrl, '_blank');
                }
            }, function(error) {
                alert("Unable to retrieve your location.");
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function isMobileDevice() {
        return /Mobi|Android/i.test(navigator.userAgent);
    }

    function isAndroid() {
        return /Android/i.test(navigator.userAgent);
    }

    function isIOS() {
        return /iPhone|iPad|iPod/i.test(navigator.userAgent);
    }
</script>
