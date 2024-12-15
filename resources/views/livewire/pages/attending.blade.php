<div x-data="{ viewType: @entangle('viewType'), qrCode: null, selectedEvent: null }" class="container px-4 mx-auto my-8 space-y-8 lg:px-8">
    <!-- Search and Filter Row -->
    <div class="flex flex-col w-full space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0">
        <div class="flex items-center w-full space-x-4 lg:w-1/2">
            <div class="flex-1">
                <x-input wire:model.debounce.500ms="search" label="Search Events" hint="Search by event name" />
            </div>
            <x-button wire:click="loadAttendingEvents" class="flex-shrink-0">
                Search
            </x-button>
            <x-select.styled wire:model="filter" :options="[1 => 'Ongoing', 2 => 'Ended']" />
        </div>
        <div class="flex w-full mt-4 space-x-4 lg:w-auto lg:mt-0">
            <x-button @click="viewType = 'list'"
                class="flex-1 text-gray-800 border border-gray-300 hover:bg-gray-100 hover:text-indigo-600">
                List View
            </x-button>
            <x-button @click="viewType = 'grid'"
                class="flex-1 text-gray-800 border border-gray-300 hover:bg-gray-100 hover:text-indigo-600">
                Grid View
            </x-button>
        </div>
    </div>

    <div class="overflow-y-scroll h-[80vh] relative min-h-4 overflow-x-clip border rounded">
        <x-loading loading="loadAttendingEvents" />

        <div
            :class="viewType === 'list' ? 'space-y-6' :
                'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 p-5'">
            @foreach ($attendingEvents as $event)
                <div x-on:click="
                selectedEvent = {
                    id: {{ $event['id'] }},
                    event_name: '{{ $event['event_name'] }}',
                    event_description: '{{ $event['event_description'] }}',
                    tickets: @js($event['tickets'])
                };
                $slideOpen('event-slide');"
                    class="relative p-4 bg-white rounded-lg border group hover:border-indigo-400 hover:bg-indigo-50 transition-all">
                    <div class="relative w-full h-40 overflow-hidden rounded-lg">
                        <img src="{{ asset('storage/' . $event['event_img_banner']) }}" alt="{{ $event['event_name'] }}"
                            class="object-cover w-full h-full transition-all duration-300 hover:scale-105">
                    </div>
                    <div class="flex flex-col mt-4">
                        <h3 class="text-xl font-semibold text-indigo-700">{{ $event['event_name'] }}</h3>
                        <p class="mt-2 text-sm text-gray-700">{{ Str::limit($event['event_description'], 100) }}</p>
                        <div class="flex justify-between mt-4 text-xs text-gray-500">
                            <span>You have {{ $event['tickets_count'] }} tickets</span>
                            <span>{{ \Carbon\Carbon::parse($event['event_time_end'])->format('h:i A') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if (empty($attendingEvents))
            <p class="mt-6 text-center text-gray-500">You are not attending any events yet.</p>
        @endif
    </div>

    <x-slide id="event-slide" persistent>
        <div class="flex flex-col h-full bg-gray-50 rounded border overflow-hidden">
            <!-- Header Section -->
            <x-slot:title>
                <h2 class="text-3xl font-semibold text-center text-indigo-700 p-6">
                    <span x-text="selectedEvent.event_name"></span>
                </h2>
            </x-slot:title>

            <!-- Scrollable Tickets Section -->
            <div class="flex-1 overflow-y-auto max-h-[75vh] p-6 space-y-6">
                <template x-for="ticket in selectedEvent.tickets" :key="ticket.id">
                    <div class="p-6 bg-white border border-gray-300 rounded hover:border-indigo-400 transition-all">
                        <!-- Ticket Type Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h1 class="text-lg font-semibold text-indigo-700" x-text="ticket.type"></h1>
                            <div class="text-right">
                                <span class="block text-lg font-bold text-blue-600" x-text="'₱' + ticket.price"></span>
                                <span class="text-sm font-medium"
                                    :class="ticket.is_verified ? 'text-green-600' : 'text-red-600'"
                                    x-text="ticket.is_verified ? 'Verified' : 'Unverified'"></span>
                                <template x-if="ticket.seat_id">
                                    <div class="text-sm font-medium text-gray-700">
                                        <p>Seat Number <span x-text="ticket.seat_id"></span></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div x-text="console.log(ticket)"></div>
                        <div class="flex flex-col items-center justify-center space-y-4">
                            <div class="w-40 h-40 bg-gray-100 border rounded-md flex items-center justify-center">
                                <img @click="qrCode = ticket.qr_code; $modalOpen('modal-ticket');"
                                    :src="ticket.qr_code" alt="QR Code" class="object-cover w-full h-full rounded-md">
                            </div>

                            <!-- Conditional Rendering of seatId -->

                            <div class="flex justify-center space-x-3 mt-4">
                                <x-button wire:click="cancelAttendance(ticket.id)"
                                    class="rounded transition-colors hover:bg-red-600 hover:text-white">
                                    Cancel Attendance
                                </x-button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer Section -->
            <x-slot:footer end>
                <div class="p-6 text-center">
                    <x-button x-on:click="$slideClose('event-slide')"
                        class="hover:border-indigo-500 hover:bg-indigo-100">
                        Close
                    </x-button>
                </div>
            </x-slot:footer>
        </div>
    </x-slide>


    <!-- Modal for QR Code -->
    <x-modal id="modal-ticket" size="lg" center>
        <div class="p-6">
            <h3 class="text-2xl font-semibold text-center text-indigo-700">QR Code</h3>
            <div class="flex items-center justify-center my-4 border p-5 rounded">
                <img :src="qrCode" alt="QR Code" class="w-full h-full">
            </div>
            <x-button class="w-full mt-4 text-white bg-gray-600 hover:bg-gray-700"
                x-on:click="
                    const link = document.createElement('a');link.href = qrCode;link.download = 'qr_code.png';link.click();">
                Download QR Code
            </x-button>
            <x-button x-on:click="$modalClose('modal-ticket')"
                class="w-full mt-4 text-white bg-blue-600 hover:bg-blue-700">
                Close
            </x-button>
        </div>
    </x-modal>

    <script>
        Livewire.on('close-slide-ticket', () => {
            $slideClose('event-slide'); // Close the modal using your modal close function
        });
    </script>
</div>
