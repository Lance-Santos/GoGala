<div>
    <div class="w-full border h-96 viewer ">
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
                                <div class="w-5 h-5 transition-all ease-in-out transform bg-indigo-600 border-2 border-indigo-500 rounded-full hover:scale-105"
                                    style="position: relative;">
                                    @if ($popup)
                                        <x-tooltip :text="$seat->isClaimed ? $seat->userName : 'Not claimed'" position="left" icon="shield-exclamation" />
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif($element->type === 'seatContainerRound')
                        <div class="absolute"
                            style="width: {{ $element->width / 1.5 }}px; height: {{ $element->width / 1.5 }}px; border: 1px solid rgba(0, 0, 0, 0.15); border-radius: 50%; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            @foreach ($element->seats as $index => $seat)
                                @php
                                    $angleStep = 360 / count($element->seats);
                                    $angle = $index * $angleStep;
                                @endphp
                                <div class="absolute w-5 h-5 bg-indigo-600 border-2 border-indigo-500 rounded-full"
                                    style="top: 50%; left: 50%; transform: translate(-50%, -50%) rotate({{ $angle }}deg) translate({{ $element->width / 2.6 }}px) rotate(-{{ ($angle * 240) / 2 }}deg); transition: transform 0.3s ease, background-color 0.2s;">
                                    @if ($popup)
                                        <x-tooltip :text="$seat->isClaimed ? $seat->userName : 'Not claimed'" position="left" icon="shield-exclamation" />
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif($element->type === 'seatContainerTable')
                        <div class="table border rounded"
                            style="
                                position: relative;
                                width: {{ $element->width - 100 }}px; /* Subtracting 100px for seat padding */
                                height: {{ $element->height - 100 }}px; /* Same here */
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                background-color: #f9f9f9; /* Light pastel background for table */
                            ">
                            <!-- Top -->
                            <div class="top"
                                style="
                                    position: absolute; /* Absolute positioning to ensure it doesn't affect parent */
                                    top: -40px; /* Placing it outside the parent */
                                    width: 100%;
                                    display: flex;
                                    justify-content: space-around;
                                ">
                                @foreach ($element->seats as $seat)
                                    @if (strpos($seat->name, 'Top') === 0)
                                        <div class="w-5 h-5 chair"
                                            style="
                                                border-radius: 50%;
                                                background-color: #7c3aed; /* Soft purple color */
                                                border: 1px solid rgba(0, 0, 0, 0.15); /* Soft border */
                                                margin: 4px; /* Slightly more space between chairs */
                                            ">
                                            @if ($popup)
                                                <x-tooltip :text="$seat->isClaimed ? $seat->userName : 'Not claimed'" position="left" icon="shield-exclamation" />
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Left -->
                            <div class="left"
                                style="
                                    position: absolute;
                                    left: -40px; /* Placing it outside the parent */
                                    height: 100%;
                                    display: flex;
                                    flex-direction: column;
                                    justify-content: space-around;
                                ">
                                @foreach ($element->seats as $seat)
                                    @if (strpos($seat->name, 'Left') === 0)
                                        <div class="w-5 h-5 chair"
                                            style="
                                                border-radius: 50%;
                                                background-color: #7c3aed; /* Soft purple color */
                                                border: 1px solid rgba(0, 0, 0, 0.15); /* Soft border */
                                                margin: 4px; /* Slightly more space between chairs */
                                            ">
                                            @if ($popup)
                                                <x-tooltip :text="$seat->isClaimed ? $seat->userName : 'Not claimed'" position="left" icon="shield-exclamation" />
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Right -->
                            <div class="right"
                                style="
                                    position: absolute;
                                    right: -40px; /* Placing it outside the parent */
                                    height: 100%;
                                    display: flex;
                                    flex-direction: column;
                                    justify-content: space-around;
                                ">
                                @foreach ($element->seats as $seat)
                                    @if (strpos($seat->name, 'Right') === 0)
                                        <div class="w-5 h-5 chair"
                                            style="
                                                border-radius: 50%;
                                                background-color: #7c3aed; /* Soft purple color */
                                                border: 1px solid rgba(0, 0, 0, 0.15); /* Soft border */
                                                margin: 4px; /* Slightly more space between chairs */
                                            ">
                                            @if ($popup)
                                                <x-tooltip :text="$seat->isClaimed ? $seat->userName : 'Not claimed'" position="left" icon="shield-exclamation" />
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Bottom -->
                            <div class="bottom"
                                style="
                                    position: absolute;
                                    bottom: -40px; /* Placing it outside the parent */
                                    width: 100%;
                                    display: flex;
                                    justify-content: space-around;
                                ">
                                @foreach ($element->seats as $seat)
                                    @if (strpos($seat->name, 'Bottom') === 0)
                                        <div class="w-5 h-5 chair"
                                            style="
                                                border-radius: 50%;
                                                background-color: #7c3aed; /* Soft purple color */
                                                border: 1px solid rgba(0, 0, 0, 0.15); /* Soft border */
                                                margin: 4px; /* Slightly more space between chairs */
                                            ">
                                            @if ($popup)
                                                <x-tooltip :text="$seat->isClaimed ? $seat->userName : 'Not claimed'" position="left" icon="shield-exclamation" />
                                            @endif
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
    <x-button data="centerMe">Center Me</x-button>
    <x-button data="zoomIn">Zoom In and Center</x-button>
    <script src="//daybrush.com/infinite-viewer/release/latest/dist/infinite-viewer.min.js"></script>
    <script>
        // Initialize zoom and scroll position
        let currentZoom = 1;
        let scrollPosition = { left: 0, top: 0 };

        const infiniteViewer = new InfiniteViewer(
            document.querySelector(".viewer"),
            document.querySelector(".viewport"), {
                useAutoZoom: true,
                zoomX: 0.9,
                zoomY: 0.9
            }
        );

        // Save the current scroll position
        infiniteViewer.on("scroll", () => {
            scrollPosition = {
                left: infiniteViewer.getScrollLeft(),
                top: infiniteViewer.getScrollTop(),
            };
            console.log("scrolling-", scrollPosition.left, scrollPosition.top);
        });

        document.querySelector('[data="centerMe"]').addEventListener("click", () => {
            infiniteViewer.scrollCenter({ duration: 300 });
        });

        document.querySelector('[data="zoomIn"]').addEventListener("click", () => {
            currentZoom = 8; // Update zoom level
            infiniteViewer.setZoom(currentZoom, { duration: 300 });
            infiniteViewer.scrollTo(scrollPosition.left, scrollPosition.top, { duration: 300 });
        });

        document.querySelector('[data="getZoom"]').addEventListener("click", () => {
            currentZoom = infiniteViewer.getZoom();
            console.log("Current Zoom:", currentZoom);
        });

        document.querySelectorAll('[data="item"]').forEach((item) =>
            item.addEventListener("click", () => {
                currentZoom = 8; // Update zoom level
                infiniteViewer.setZoom(currentZoom, { duration: 300 });
                infiniteViewer.scrollTo(scrollPosition.left, scrollPosition.top, { duration: 300 });
            })
        );
    </script>
</div>
