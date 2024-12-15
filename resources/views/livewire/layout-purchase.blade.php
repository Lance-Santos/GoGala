<div x-data="seatingApp()">
    <x-button data="centerMe">Center</x-button>
    <x-button data="zoomIn">Zoom In and Center</x-button>
    <br>
    <br>
    <div class="w-full border h-96 viewer" wire:ignore>
        <div class="w-full h-full viewport">
            @foreach ($layout as $element)
                <div key="{{ $element->id }}" class="cube cube{{ $element->id }} border"
                    style="
                    position: absolute;
                    width: {{ $element->width }}px;
                    height: {{ $element->height }}px;
                    border-radius: 8px;
                    transform: rotate({{ $element->rotation }}deg) translate({{ $element->posX }}px, {{ $element->posY }}px);
                    justify-content: center;
                    color: white;
                    font-weight: bold;
                "
                    data-element-id="{{ $element->id }}">

                    @if ($element->type === 'seatContainerRect')
                        <div class="flex flex-wrap items-start justify-center w-full gap-2 p-3">
                            @foreach ($element->seats as $seat)
                                @php
                                    $isClaimed = $seat->isClaimed;
                                @endphp
                                <div class="relative w-10 h-10 transition-all ease-in-out transform
                                {{ $isClaimed ? 'bg-red-600 border-red-500' : 'bg-indigo-600 border-indigo-500' }}
                                rounded-full hover:scale-105 cursor-pointer"
                                    style="pointer-events: {{ $isClaimed ? 'none' : 'auto' }}"
                                    x-on:click="selectSeat({{ json_encode($seat) }}, {{ $element->ticket_id }}); $modalOpen('seat-modal')">
                                </div>
                            @endforeach
                        </div>
                    @elseif($element->type === 'seatContainerRound')
                        <div class="relative w-full h-full flex justify-center items-center">
                            @foreach ($element->seats as $index => $seat)
                                @php
                                    // Calculate the angle step and the rotation angle for each seat
                                    $angleStep = 360 / count($element->seats);
                                    $angle = $index * $angleStep;
                                    $radius = $element->width / 2.6;
                                    $isClaimed = $seat->isClaimed;
                                @endphp
                                <div class="absolute w-10 h-10 flex items-center justify-center transition-all ease-in-out transform
                                {{ $isClaimed ? 'bg-red-600 border-red-500' : 'bg-indigo-600 border-indigo-500' }}
                                rounded-full text-white hover:scale-105 cursor-pointer"
                                    style="
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%) rotate({{ $angle }}deg) translate({{ $radius }}px) rotate(-{{ ($angle * 240) / 2 }}deg);
                                    pointer-events: {{ $isClaimed ? 'none' : 'auto' }};
                                    transition: transform 0.3s ease, background-color 0.2s;
                                "
                                    x-on:click="selectSeat({{ json_encode($seat) }}, {{ $element->ticket_id }}); $modalOpen('seat-modal')">
                                    <span style="font-size: 12px;">
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @elseif($element->type === 'seatContainerTable')
                        <div class="table border rounded"
                            style="
                            position: relative;
                            width: {{ $element->width - 100 }}px;
                            height: {{ $element->height - 100 }}px;
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%);
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            background-color: #f9f9f9;
                        ">
                            <!-- Top -->
                            <div class="top"
                                style="
                                position: absolute;
                                top: -40px;
                                width: 100%;
                                display: flex;
                                justify-content: space-around;
                            ">
                                @foreach ($element->seats as $seat)
                                    @if (strpos($seat->name, 'Top') === 0)
                                        @php
                                            $isClaimed = $seat->isClaimed;
                                        @endphp
                                        <div class="relative w-10 h-10 {{ $isClaimed ? 'bg-red-600 border-red-500' : 'bg-indigo-600 border-indigo-500' }}
                                        border-2 rounded-full chair transition-all ease-in-out transform hover:scale-105 cursor-pointer"
                                            style="pointer-events: {{ $isClaimed ? 'none' : 'auto' }}"
                                            x-on:click="selectSeat({{ json_encode($seat) }}, {{ $element->ticket_id }}); $modalOpen('seat-modal')">
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Left -->
                            <div class="left"
                                style="
                                position: absolute;
                                left: -40px;
                                height: 100%;
                                display: flex;
                                flex-direction: column;
                                justify-content: space-around;
                            ">
                                @foreach ($element->seats as $seat)
                                    @if (strpos($seat->name, 'Left') === 0)
                                        @php
                                            $isClaimed = $seat->isClaimed;
                                        @endphp
                                        <div class="relative w-10 h-10 {{ $isClaimed ? 'bg-red-600 border-red-500' : 'bg-indigo-600 border-indigo-500' }}
                                        border-2 rounded-full chair transition-all ease-in-out transform hover:scale-105 cursor-pointer"
                                            style="pointer-events: {{ $isClaimed ? 'none' : 'auto' }}"
                                            x-on:click="selectSeat({{ json_encode($seat) }}, {{ $element->ticket_id }}); $modalOpen('seat-modal')">
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Right -->
                            <div class="right"
                                style="
                                position: absolute;
                                right: -40px;
                                height: 100%;
                                display: flex;
                                flex-direction: column;
                                justify-content: space-around;
                            ">
                                @foreach ($element->seats as $seat)
                                    @if (strpos($seat->name, 'Right') === 0)
                                        @php
                                            $isClaimed = $seat->isClaimed;
                                        @endphp
                                        <div class="relative w-10 h-10 {{ $isClaimed ? 'bg-red-600 border-red-500' : 'bg-indigo-600 border-indigo-500' }}
                                        border-2 rounded-full chair transition-all ease-in-out transform hover:scale-105 cursor-pointer"
                                            style="pointer-events: {{ $isClaimed ? 'none' : 'auto' }}"
                                            x-on:mouseenter="showTooltip('{{ $isClaimed ? 'Claimed' : 'Unclaimed' }}')"
                                            x-on:click="selectSeat({{ json_encode($seat) }}, {{ $element->ticket_id }}); $modalOpen('seat-modal')">
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Bottom -->
                            <div class="bottom"
                                style="
                                position: absolute;
                                bottom: -40px;
                                width: 100%;
                                display: flex;
                                justify-content: space-around;
                            ">
                                @foreach ($element->seats as $seat)
                                    @if (strpos($seat->name, 'Bottom') === 0)
                                        @php
                                            $isClaimed = $seat->isClaimed;
                                        @endphp
                                        <div class="relative w-10 h-10 {{ $isClaimed ? 'bg-red-600 border-red-500' : 'bg-indigo-600 border-indigo-500' }}
                                        border-2 rounded-full chair transition-all ease-in-out transform hover:scale-105 cursor-pointer"
                                            style="pointer-events: {{ $isClaimed ? 'none' : 'auto' }}"
                                            x-on:click="selectSeat({{ json_encode($seat) }}, {{ $element->ticket_id }}); $modalOpen('seat-modal')">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <x-modal id="seat-modal" x-show="selectedSeat" size="md" center blur>
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Ticket Header -->
            <div class="bg-indigo-600 text-white p-4 text-center rounded-t-lg">
                <h2 class="text-2xl font-bold">Your Seat</h2>
            </div>

            <!-- Ticket Body -->
            <div class="p-6 space-y-6">
                <!-- Seat Details Grid -->
                <div class="grid grid-cols-2 gap-6">
                    <div x-show="selectedSeat?.seatNumber" x-cloak>
                        <p class="text-sm font-medium text-gray-500">Seat</p>
                        <p class="text-lg font-semibold text-gray-800" x-text="selectedSeat?.seatNumber ?? 'N/A'"></p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <p class="text-lg font-semibold text-gray-800"
                            x-text="selectedSeat?.isClaimed ? 'Claimed' : 'Unclaimed'"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Type</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $selectedSeat['type'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Price</p>
                        <p class="text-lg font-semibold text-gray-800">
                            ₱{{ number_format($selectedSeat['price'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 p-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                <x-button wire:click="addToCart(selectedSeat?.seatNumber, selectedSeat?.id)"
                    class="w-full text-white bg-indigo-600 hover:bg-indigo-700">
                    Add to Cart
                </x-button>
                <x-button x-on:click="$modalClose('seat-modal')"
                    class="w-full text-gray-700 bg-gray-300 hover:bg-gray-400">
                    Close
                </x-button>
            </div>
        </div>
    </x-modal>



    <script src="//daybrush.com/infinite-viewer/release/latest/dist/infinite-viewer.min.js"></script>
    <script>
        function seatingApp() {
            return {
                selectedSeat: null,
                showTooltip(text) {
                    // Show tooltip
                },
                hideTooltip() {
                    // Hide tooltip
                },
                selectSeat(seat, id) {
                    this.selectedSeat = seat;
                    // Trigger Livewire function to get ticket details
                    @this.call('showTicket', id);
                },
                addToCart() {
                    alert(`Seat ${this.selectedSeat.seatNumber} added to cart.`);
                },
                closeCard() {
                    this.selectedSeat = null;
                }
            };
        }
    </script>
    <script>
        let currentZoom = 1;
        let scrollPosition = {
            left: 0,
            top: 0
        };

        const infiniteViewer = new InfiniteViewer(
            document.querySelector(".viewer"),
            document.querySelector(".viewport"), {
                useAutoZoom: true,
                zoomX: 0.9,
                zoomY: 0.9
            }
        );

        infiniteViewer.on("scroll", () => {
            scrollPosition = {
                left: infiniteViewer.getScrollLeft(),
                top: infiniteViewer.getScrollTop(),
            };
        });

        document.querySelector('[data="centerMe"]').addEventListener("click", () => {
            infiniteViewer.scrollCenter({
                duration: 300
            });
        });

        document.querySelector('[data="zoomIn"]').addEventListener("click", () => {
            currentZoom = 8; // Update zoom level
            infiniteViewer.setZoom(currentZoom, {
                duration: 300
            });
            infiniteViewer.scrollTo(scrollPosition.left, scrollPosition.top, {
                duration: 300
            });
        });
    </script>
</div>
