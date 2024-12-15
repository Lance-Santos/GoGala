<div class="container px-6 mx-auto my-12 space-y-12 lg:px-10">
    <h1 class="mb-8 text-4xl font-extrabold text-gray-900">Checkout</h1>

    <x-step wire:model="step" helpers previous>
        <!-- Step 1: Cart Review -->
        <x-step.items step="1" title="Cart Review" description="Review your items">
            <div class="p-8 bg-gray-100 rounded-lg shadow-lg">
                <h2 class="mb-6 text-3xl font-bold text-gray-800">🛒 Your Cart</h2>

                @forelse ($cart as $index => $item)
                    <div class="flex items-center justify-between p-6 mb-4 bg-white rounded-lg shadow-sm">
                        <div>
                            <p class="text-lg font-semibold text-gray-900">{{ $item['ticket_type'] }}</p>
                            <p class="text-gray-600">Price: ${{ number_format($item['ticket_price'], 2) }}</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <x-input type="number" min="1" wire:model="cart.{{ $index }}.quantity"
                                wire:change="updateQuantity({{ $index }})"
                                class="w-16 text-center border-gray-300 rounded-lg shadow-sm" />
                            <p class="text-lg font-medium text-gray-700">
                                Total: ${{ number_format($item['ticket_price'] * $item['quantity'], 2) }}
                            </p>
                        </div>

                        <!-- Remove Button -->
                        <x-button.circle icon="trash" wire:click="removeItem({{ $item['ticket_id'] }})"
                            class="text-white bg-red-600 hover:bg-red-700" />
                    </div>
                @empty
                    <p class="text-center text-gray-600">Your cart is empty. Add items to proceed.</p>
                @endforelse

                <!-- Total Price -->
                <div class="mt-8 text-right">
                    <p class="text-2xl font-extrabold text-gray-900">Total: ${{ number_format($totalPrice, 2) }}</p>
                </div>

                <!-- Proceed Button -->
                <x-button wire:click="$set('step', 2)"
                    class="w-full py-3 mt-6 font-semibold text-white bg-blue-600 rounded-lg shadow-lg hover:bg-blue-700">
                    Proceed to Payment
                </x-button>
            </div>
        </x-step.items>

        <!-- Step 2: Payment Method -->
        <x-step.items step="2" title="Payment Method" description="Choose a payment method">
            <div class="p-8 bg-gray-100 rounded-lg shadow-lg">
                <h2 class="mb-6 text-3xl font-bold text-gray-800">💳 Select Payment Method</h2>

                <div class="grid grid-cols-2 gap-6">
                    @foreach ($paymentMethods as $key => $method)
                        <x-button wire:click="initiatePayment('{{ $key }}')"
                            class="w-full py-3 text-center font-semibold rounded-lg shadow-sm
                                         {{ $selectedPaymentMethod === $key ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            {{ $method }}
                        </x-button>
                    @endforeach
                </div>

                <!-- Proceed to Payment Button -->
                {{-- <div class="mt-8 text-right">
                    <x-button wire:click="initiatePayment('{{ $selectedPaymentMethod }}')"
                        class="w-full py-3 font-semibold text-white bg-blue-600 rounded-lg shadow-lg hover:bg-blue-700"
                        :disabled="!$selectedPaymentMethod">
                        Proceed to Pay
                    </x-button>
                </div> --}}
        </x-step.items>
        <x-step.items step="3" title="Payment Status" description="Payment Confirmation">
            <div class="p-8 bg-gray-100 rounded-lg shadow-lg">
                <h2 class="mb-6 text-3xl font-bold text-gray-800">⏳ Waiting for Payment Confirmation...</h2>

                @if ($paymentStatus === 'success')
                    <div class="flex flex-col items-center space-y-4">
                        <x-icon name="check-circle" class="w-16 h-16 text-green-600" />
                        <p class="text-2xl font-bold text-green-700">Payment Success</p>
                    </div>
                @else
                    <p class="text-lg text-gray-600">Please wait while we confirm your payment...</p>
                @endif
            </div>
        </x-step.items>

    </x-step>

    <!-- JavaScript for handling redirection -->
    <script>
        window.addEventListener('redirect-to-checkout', event => {
            window.open(event.__livewire.params[0].url, '_blank');
        });

        window.addEventListener('payment-error', event => {
            alert(event.detail.message); // Display payment error message
        });
    </script>
</div>
