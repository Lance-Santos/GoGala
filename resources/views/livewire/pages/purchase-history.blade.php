<div class="container mx-auto my-8 space-y-6">
    <!-- Search and Filter Row -->
    <div class="flex flex-col w-full space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0">
    <div class="flex items-center w-full space-x-4 lg:w-1/2">
        <div class="flex-1">
            <x-input wire:model.debounce.500ms="search" class="w-full" label="Search Purchases" hint="Search by event name" />
        </div>
        <x-button wire:click="loadPurchases" class="flex-shrink-0">Search</x-button>
    </div>
    <x-select.styled wire:model="filter" :options="$filters" label="Filter by Transaction Type" />
</div>


    <!-- Purchases List -->
    <div class="overflow-y-scroll max-h-[70vh] border rounded">
        @if ($purchases->isEmpty())
            <p class="p-6 text-center text-gray-500">No purchases found.</p>
        @else
            <div class="space-y-4">
                @foreach ($purchases as $purchase)
                    <div class="p-4 bg-white border rounded hover:shadow-md">
                        <h3 class="text-lg font-bold text-indigo-600">{{ $purchase->event->event_name }}</h3>
                        <p class="text-sm text-gray-600">
                            Purchased by: {{ $purchase->user->first_name }}
                        </p>
                        <p class="text-sm text-gray-500">Transaction Type: {{ ucfirst($purchase->transaction_type) }}</p>
                        <p class="text-sm text-gray-500">Total Price: ₱{{ number_format($purchase->total_price, 2) }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
