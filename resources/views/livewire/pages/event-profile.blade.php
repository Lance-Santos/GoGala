<div class="container px-4 mx-auto my-8 space-y-8 lg:px-8 border rounded">
    <!-- Slide for Editing Event -->
    <x-slide id="edit-event-slide" size="full" x-on:open="initializeMap" x-on:close="closeEditSlide()">
        <div class="max-w-4xl p-8 mx-auto bg-white rounded-lg shadow-lg">
            <h2 class="mb-8 text-4xl font-bold text-center text-gray-800">{{ __('Edit Event') }}</h2>
            <form wire:submit.prevent="updateEvent" class="space-y-8">
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
                    <div>
                        <form>
                            <!-- Select Categories -->
                            <x-select.styled :options="$categories->pluck('name', 'id')->toArray()" wire:model="selectedCategories" multiple
                                wire:change="updatedSelectedCategories($event.target.value)"
                                label="Select Categories" />
                        </form>
                    </div>

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
                    <x-button wire:click="updateEvent" loading
                        class="px-4 py-2 font-semibold text-white bg-blue-600 rounded hover:bg-blue-700">
                        Update Event
                    </x-button>
                </div>
            </form>
        </div>
    </x-slide>
    <div class="relative">
        @if ($event->event_img_banner_url)
            <img src="{{ asset('/storage/' . $event->event_img_banner_url) }}" alt="Event Banner"
                class="object-cover w-full h-32 lg:h-44 rounded-xl">
        @else
            <div class="flex items-center justify-center w-full h-32 lg:h-44 bg-slate-200 rounded-xl">
                <x-avatar color="slate" class="w-20 h-20" />
            </div>
        @endif
        <div class="absolute bottom-[-30px] left-6 lg:left-10">
            @if ($event->event_img_url)
                <x-avatar image="{{ asset('/storage/' . $event->event_img_url) }}"
                    class="w-24 h-24 border-4 border-white rounded-full" />
            @else
                <x-avatar color="slate" class="w-24 h-24 border-4 border-white rounded-full" />
            @endif
        </div>
    </div>
    <div class="pl-6 mt-16 space-y-4 lg:pl-10">
        <h1 class="text-4xl font-bold">{{ $event_name }}</h1>
        <p class="text-lg text-slate-500">@ {{ $event_slug }}</p>
        <p class="mt-2 text-gray-800">{{ $event_description }}</p>

        <div class="flex mt-4 space-y-2 text-gray-500">
            <p>
                {{ \Carbon\Carbon::parse($event_date[0])->format('M d, Y') }} to
                {{ \Carbon\Carbon::parse($event_date[1])->format('M d, Y') }} |
                {{ \Carbon\Carbon::parse($event_time_start)->format('h:i A') }} to
                {{ \Carbon\Carbon::parse($event_time_end)->format('h:i A') }}
            </p>
        </div>
        <div class="mt-2 text-gray-500">
            <p>{{ $event_address_string }}</p>
        </div>

        <!-- Edit Button -->
        <div class="mt-4">
            <x-button x-on:click="$slideOpen('edit-event-slide');"
                class="px-4 py-2 font-semibold text-white bg-blue-600 rounded hover:bg-blue-700">
                Edit Event
            </x-button>
        </div>
    </div>

    <!-- Tabs for Event Details -->
    <div class="pt-6 mt-10 border-t">
        <x-tab selected="Information">
            <x-tab.items tab="Information">
                <x-slot:left>
                    <x-icon name="home" class="w-6 h-6" />
                </x-slot:left>
                <div class="flex flex-wrap gap-6 mb-6">
                    <div class="border rounded flex-1 min-w-[250px]">
                        <x-stats title="Participants" :number="$ticketCount" />
                    </div>
                    <div class="border rounded flex-1 min-w-[250px]">
                        <x-stats title="Average Rating" :number="$averageRating" />
                    </div>
                    <div class="border rounded flex-1 min-w-[250px]">
                        <x-stats title="Total Revenue" :number="'₱' . number_format($totalRevenue, 2)" />
                    </div>
                </div>
            </x-tab.items>
            <x-tab.items tab="Tickets">
                <x-slot:left>
                    <x-icon name="ticket" class="w-6 h-6" />
                </x-slot:left>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Tickets</h2>
                    <x-modal id="modal-ticket" center size="5xl" sm blur>
                        <x-slot:title>
                            Create Ticket
                        </x-slot:title>
                        <form wire:submit.prevent="addTicket">
                            <x-input label="Ticket Type *" icon="ticket" wire:model="newTicketType" />
                            <x-number centralized prefix="₱" min="0" label="Price *"
                                wire:model="newTicketPrice" />
                            <x-number centralized selectable min="0" label="Quantity *"
                                wire:model="newTicketQuantity" />
                            <x-slot:footer>
                                <x-button wire:click='addTicket' type="submit" text="Create Ticket" />
                                <x-button text="Close" color="red" x-on:click="$modalClose('modal-ticket')" />
                            </x-slot:footer>
                        </form>
                    </x-modal>
                    <x-modal id="modal-edit-ticket" center size="5xl" sm blur>
                        <x-slot:title>
                            Update Ticket
                        </x-slot:title>
                        <form wire:submit.prevent="addTicket">
                            <x-input label="Ticket Type *" icon="ticket" wire:model="newTicketType" />
                            <x-number centralized prefix="₱" min="0" label="Price *"
                                wire:model="newTicketPrice" />
                            <x-number centralized selectable min="0" label="Quantity *"
                                wire:model="newTicketQuantity" />
                            <x-slot:footer>
                                <x-button wire:click='updateTicket' type="submit" text="Update" />
                                <x-button text="Close" color="red"
                                    x-on:click="$modalClose('modal-edit-ticket')" />
                            </x-slot:footer>
                        </form>
                    </x-modal>
                    <x-button text="Create Ticket" x-on:click="$modalOpen('modal-ticket')" />
                </div>
                <div>
                    <div class="mb-6">
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex-1 min-w-[200px]">
                                <x-input wire:model="ticketSearch" placeholder="Search by Ticket Type"
                                    class="w-full" />
                            </div>
                            <div>
                                <x-button wire:click="showTickets" text="Search" class="bg-blue-500 text-white" />
                            </div>
                            <div class="min-w-[180px]">
                                <x-select.styled wire:model="ticketQuantity" :options="[10, 25, 50, 100]"
                                    placeholder="Tickets per page" />
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2 px-4 text-left cursor-pointer" wire:click="sortBy('type')">Type
                                    </th>
                                    <th class="py-2 px-4 text-left">Price</th>
                                    <th class="py-2 px-4 text-left">Quantity</th>
                                    <th class="py-2 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ticketsThing as $ticket)
                                    <tr wire:key="ticket-{{ $ticket->id }}" class="border-b hover:bg-gray-50">
                                        <td class="py-2 px-4">{{ $ticket->type }}</td>
                                        <td class="py-2 px-4">₱ {{ number_format($ticket->price, 2) }}</td>
                                        <td class="py-2 px-4">{{ $ticket->quantity }}</td>
                                        <td class="py-2 px-4 flex space-x-2">
                                            <x-button wire:click="editTicket({{ $ticket->id }})" color="blue"
                                                class="text-white rounded-full p-2 hover:bg-blue-500"
                                                x-on:click="$modalOpen('modal-edit-ticket')">
                                                <x-icon name="pencil" class="w-5 h-5" />
                                            </x-button>
                                            <x-button wire:click="deleteTicket({{ $ticket->id }})" color="red"
                                                class="text-white rounded-full p-2 hover:bg-red-500">
                                                <x-icon name="trash" class="w-5 h-5" />
                                            </x-button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-2 px-4">No tickets found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $ticketsThing->links() }}
                    </div>
                </div>


            </x-tab.items>
            <x-tab.items tab="Questions">
                <x-slot:left>
                    <x-icon name="question-mark-circle" class="w-6 h-6" />
                </x-slot:left>
                <div class="container mx-auto p-6">
                    <h2 class="text-2xl font-semibold mb-6">Questions</h2>

                    @foreach ($questions as $question)
                        <div class="border p-4 rounded-lg mb-4">
                            <div class="flex items-start space-x-4 border p-5">
                                <!-- User Avatar and Handle for Question -->
                                <div class="flex-shrink-0">
                                    <x-avatar color="fff" />
                                </div>
                                <div class="flex-grow">
                                    <div class="flex justify-between items-center">
                                        <p class="text-lg font-semibold">{{ $question->question }}</p>

                                        <!-- Check if the user has already replied to this question -->
                                        @php
                                            $hasReplied = $question->answers->contains('user_id', auth()->id());
                                        @endphp

                                        @if (!$hasReplied)
                                            <button wire:click="showReplyField({{ $question->id }})"
                                                class="text-indigo-600 hover:text-indigo-800">Reply</button>
                                        @else
                                            <span class="text-sm text-gray-500">You already replied</span>
                                        @endif
                                    </div>

                                    <!-- Display User Handle for Question -->
                                    <p class="text-sm text-gray-600">{{ $question->user->username }}</p>

                                    <!-- Display the reply textarea if this question is selected -->
                                    @if ($selectedQuestionId === $question->id)
                                        <form wire:submit.prevent="submitReply">
                                            <div class="mt-4">
                                                <x-textarea wire:model="replyText"
                                                    class="border p-2 w-full rounded-lg"
                                                    placeholder="Type your reply..." required></x-textarea>
                                            </div>
                                            <button type="submit" wire:click='submitReply'
                                                class="mt-2 bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700">Submit
                                                Reply
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Display replies for this question -->
                            @foreach ($question->answers as $answer)
                                <div class="mt-4 flex items-start space-x-4 pl-8">
                                    <!-- Added padding for indentation -->
                                    <!-- User Avatar and Handle for Reply -->
                                    <div class="flex-shrink-0">
                                        <x-avatar color="fff" />
                                    </div>
                                    <div class="flex-grow">
                                        <p class="font-semibold">{{ $answer->user->username }}</p>
                                        <p class="text-gray-800 mt-2">{{ $answer->answer_text }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach


                </div>


            </x-tab.items>
            <x-tab.items tab="Ratings">
                <x-slot:left>
                    <x-icon name="star" class="w-6 h-6" />
                </x-slot:left>
                <div class="mt-6">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-6">Event Ratings</h3>

                    <!-- Loop through all ratings -->
                    <div class="space-y-4">
                        @foreach ($ratings as $rating)
                            <div class="bg-gray-100 p-6 rounded-lg shadow-md">
                                <div class="flex items-center space-x-4">
                                    <!-- User Avatar -->
                                    <div class="flex-shrink-0">
                                        <x-avatar :model="$rating->user->firstname" color="fff" class="w-12 h-12" />
                                    </div>
                                    <div class="flex-grow">
                                        <!-- Rating Info -->
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-semibold text-gray-700">{{ $rating->user->username }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $rating->created_at->diffForHumans() }}</p>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <!-- Display rating score -->
                                                <x-rating :rate="$rating->score" color="yellow" readonly static />
                                            </div>
                                        </div>

                                        <!-- Comment Section -->
                                        <p class="mt-4 text-gray-800">{{ $rating->comment ?? 'No comment provided' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </x-tab.items>
            <x-tab.items tab="Settings">
                <x-slot:left>
                    <x-icon name="cog" class="w-6 h-6" />
                </x-slot:left>
                <div class="space-y-4">
                    <x-toggle wire:model="hasEnded" wire:change='updateEvent'>
                        <x-slot:label>
                            End Event
                        </x-slot:label>
                    </x-toggle>
                    <x-select.styled :options="['Public' => 'Public', 'Private' => 'Private', 'Unlisted' => 'Unlisted']" wire:model="event_status" wire:change='updateEvent' />

                    <x-select.styled label="Event Type" wire:model="event_type" :options="['seating' => 'seating', 'ticket' => 'ticket', 'free' => 'free']"
                        wire:change='updateEvent' />
                    <div class="mt-4">
                        <x-button color="red" wire:click="deleteEvent"
                            class="px-4 py-2 font-semibold text-white bg-red-600 rounded hover:bg-red-700">
                            Delete Event
                        </x-button>
                    </div>
                </div>
            </x-tab.items>
            <x-tab.items tab="Users">
                <x-slot:left>
                    <x-icon name="user-group" class="w-6 h-6" />
                </x-slot:left>

                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Participating Users</h2>
                        <x-modal id="scanner-ticket" center size="5xl" sm blur x-on:close="stopScanner()">
                            <x-slot:title>
                                Scan QR
                            </x-slot:title>
                            <div>
                                <video id="scanner" class="w-full h-64 border rounded"></video>
                                <p id="scannedData" class="mt-4 text-gray-700"></p>
                            </div>
                            <x-slot:footer>
                                <x-button data="switchCamera">Switch Camera</x-button>

                                <x-button text="Close" color="red"
                                    x-on:click="$modalClose('scanner-ticket'); stopScanner()" />
                            </x-slot:footer>
                        </x-modal>
                        <x-button text="Scan QR" x-on:click="$modalOpen('scanner-ticket'); startScanner()" />
                    </div>
                </div>

                <script src="https://unpkg.com/jsqr/dist/jsQR.js"></script>
                <script>
                    let videoStream = null; // Track the video stream
                    let currentDevice = null; // To keep track of the current camera device

                    async function startScanner() {
                        const video = document.getElementById("scanner");

                        // Get the list of available video devices (cameras)
                        const devices = await navigator.mediaDevices.enumerateDevices();
                        const videoDevices = devices.filter(device => device.kind === 'videoinput');

                        if (videoDevices.length === 0) {
                            alert("No cameras found!");
                            return;
                        }

                        // Check if the device is mobile
                        const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
                        let defaultCamera = null;

                        if (isMobile) {
                            // Select the rear camera if the device is mobile
                            const rearCamera = videoDevices.find(device => !device.label.toLowerCase().includes("front"));
                            defaultCamera = rearCamera || videoDevices[0];
                        } else {
                            // Default to the first available camera or the front camera if available
                            const frontCamera = videoDevices.find(device => device.label.toLowerCase().includes("front"));
                            defaultCamera = frontCamera || videoDevices[0];
                        }

                        currentDevice = defaultCamera;

                        // Access the selected camera
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                deviceId: currentDevice.deviceId
                            }
                        });
                        video.srcObject = stream;
                        video.play();

                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');

                        function scanFrame() {
                            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                                canvas.height = video.videoHeight;
                                canvas.width = video.videoWidth;
                                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                                const qrCode = jsQR(imageData.data, canvas.width, canvas.height);
                                if (qrCode) {
                                    console.log("QR Code Found: ", qrCode.data);

                                    // Stop scanning and close the modal
                                    stopScanner();
                                    @this.call("checkQr", qrCode.data);
                                }
                            }
                            requestAnimationFrame(scanFrame);
                        }

                        scanFrame();
                    }

                    function stopScanner() {
                        if (videoStream) {
                            videoStream.getTracks().forEach((track) => track.stop());
                            videoStream = null;
                        }
                    }

                    // Function to switch to rear camera (if available)
                    async function switchToRearCamera() {
                        const video = document.getElementById("scanner");
                        const devices = await navigator.mediaDevices.enumerateDevices();
                        const videoDevices = devices.filter(device => device.kind === 'videoinput');

                        const rearCamera = videoDevices.find(device => !device.label.toLowerCase().includes("front"));
                        if (rearCamera) {
                            currentDevice = rearCamera;
                            const stream = await navigator.mediaDevices.getUserMedia({
                                video: {
                                    deviceId: currentDevice.deviceId
                                }
                            });
                            video.srcObject = stream;
                            video.play();
                        } else {
                            alert("No rear camera found!");
                        }
                    }

                    // Add an event listener to allow switching between cameras
                    document.querySelector('[data="switchCamera"]').addEventListener("click", switchToRearCamera);
                </script>


                <div>

                    {{-- <x-table :headers="$headers" :rows="$rows" paginate filter loading>
                        @interact('column_action', $row)
                            <div class="flex justify-end gap-2">
                                <!-- Button to toggle blacklist status -->
                                <x-button
                                    text="{{ $row['is_blacklisted'] === 'Yes' ? 'Unblacklist User' : 'Blacklist User' }}"
                                    color="{{ $row['is_blacklisted'] === 'Yes' ? 'yellow' : 'red' }}"
                                    wire:click="blacklistUser('{{ $row['id'] }}')"
                                    :key="uniqid()"/>
                                <x-button text="View Profile" color="primary" wire:click="viewProfile('{{ $row['id'] }}')"/>
                            </div>
                        @endinteract
                    </x-table> --}}
                    <div>
                        <!-- Search and Filters -->
                        <div class="mb-6">
                            <!-- Search and Filters Section -->
                            <div class="flex flex-wrap items-center gap-4">
                                <!-- Search Input -->
                                <div class="flex-1 min-w-[200px]">
                                    <x-input wire:model="search" placeholder="Search by Name or Event"
                                        class="w-full" />
                                </div>

                                <!-- Search Button -->
                                <div>
                                    <x-button wire:click="showPurchasedTickets" text="Search"
                                        class="bg-blue-500 text-white" />
                                </div>

                                <!-- Verified Filter -->
                                <div class="min-w-[180px]">
                                    <x-select.styled wire:model="verifiedFilter" :options="['All', 'Verified', 'Not Verified']"
                                        placeholder="Filter by Verification" />
                                </div>

                                <!-- Blacklisted Filter -->
                                <div class="min-w-[180px]">
                                    <x-select.styled wire:model="blacklistedFilter" :options="['All', 'Whitelisted', 'Blacklisted']"
                                        placeholder="Filter by Blacklist Status" />
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto border-collapse">
                                <thead>
                                    <tr class="border-b">
                                        <th class="py-2 px-4 text-left cursor-pointer" wire:click="sortBy('user')">
                                            User</th>
                                        <th class="py-2 px-4 text-left cursor-pointer"
                                            wire:click="sortBy('created_at')">Date</th>
                                        <th class="py-2 px-4 text-left">Verified</th>
                                        <th class="py-2 px-4 text-left">Blacklist Status</th>
                                        <th class="py-2 px-4 text-left">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($purchasedTicketPagination as $ticket)
                                        <tr wire:key='{{ $ticket->id }}' class="border-b hover:bg-gray-50">
                                            <td class="py-2 px-4">{{ $ticket->user->first_name }}
                                                {{ $ticket->user->middle_name }} {{ $ticket->user->last_name }}</td>
                                            <td class="py-2 px-4">{{ $ticket->created_at->diffForHumans() }}</td>
                                            <td class="py-2 px-4">
                                                {{ $ticket->is_verified ? 'Verified' : 'Unverified' }}</td>
                                            <td class="py-2 px-4">
                                                {{ $ticket->is_blacklisted ? 'Blacklisted' : 'Not Blacklisted' }}</td>
                                            <td class="py-2 px-4">
                                                <x-button wire:key='{{ $ticket->id }}'
                                                    wire:click="blacklistUser({{ $ticket->id }})" color="red">
                                                    {{ $ticket->is_blacklisted ? 'Unblacklist' : 'Blacklist' }}
                                                </x-button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-2 px-4">No tickets found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $purchasedTicketPagination->links() }}
                        </div>
                    </div>


                </div>

            </x-tab.items>

            @if ($event->event_type === 'seating')
                <x-tab.items tab="Seating">

                    <x-slot:left>
                        <x-icon name="pencil-square" class="w-6 h-6" />
                    </x-slot:left>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Tickets</h2>
                        <x-button text="Open Editor"
                            href="{{ route('editor', ['organization_slug' => $orgSlug, 'event_slug' => $event->event_slug]) }}" />
                    </div>
                    <div>
                        <livewire:layout-component :layout="$elements" :popup="true" />
                    </div>
                </x-tab.items>
            @endif
            </x-tab.items>
    </div>

    <script>
        const dumagueteCenter = [9.30697, 123.30877];
        const radius = 5000;
        let map;
        let marker;
        let backupEvent = {};

        // Initialize the map when the slide is opened
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

                    // Backup the current event data
                    backupEvent = {
                        event_name: @this.get('event_name'),
                        event_slug: @this.get('event_slug'),
                        event_description: @this.get('event_description'),
                        event_date: [...@this.get('event_date')], // clone the array
                        event_time_start: @this.get('event_time_start'),
                        event_time_end: @this.get('event_time_end'),
                        event_latitude: @this.get('event_latitude'),
                        event_longitude: @this.get('event_longitude'),
                        event_address_string: @this.get('event_address_string'),
                        event_img_url: @this.get('event_img_url'),
                        event_img_banner_url: @this.get('event_img_banner_url')
                    };

                    // If the event has coordinates, place a marker
                    if (@this.get('event_latitude') && @this.get('event_longitude')) {
                        const lat = @this.get('event_latitude');
                        const lng = @this.get('event_longitude');
                        const address = @this.get('event_address_string') || 'Selected Location';

                        // Place a marker at the provided coordinates
                        marker = L.marker([lat, lng]).addTo(map);

                        // Optionally, add a popup with the address
                        marker.bindPopup(address).openPopup();

                        // Set the map view to the marker location
                        map.setView([lat, lng], 15);
                    }

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

        // Reset values and revert to backup when closing the slide
        function closeEditSlide() {
            // Trigger Livewire method to reset values
            @this.call('resetToBackup');

            // Revert the marker to its backup coordinates
            if (backupEvent.event_latitude && backupEvent.event_longitude) {
                const lat = backupEvent.event_latitude;
                const lng = backupEvent.event_longitude;

                // If a marker already exists, move it back to the backup location
                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng]).addTo(map);
                }

                // Optionally, update the map view to the original position
                map.setView([lat, lng], 15);

                // Optionally, update the address if necessary
                const address = backupEvent.event_address_string || 'Selected Location';
                marker.bindPopup(address).openPopup();
            }
        }
    </script>
    @script
        <script>
            $wire.on('close-thing', () => {
                $modalClose('scanner-ticket');
                stopScanner();
            });
        </script>
    @endscript

</div>
