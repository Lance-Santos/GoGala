<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Checkout extends Component
{
    public $step = 1; // Tracks the current step in the checkout process
    public $cart = [];
    public $paymentStatus;
    public $totalPrice = 0;
    public $paymentMethods = [
        // 'qrph' => 'QRPH',
        // 'billease' => 'Billease',
        // 'card' => 'Card',
        // 'dob' => 'DOB',
        // 'dob_ubp' => 'DOB UBP',
        // 'brankas_bdo' => 'Brankas BDO',
        // 'brankas_landbank' => 'Brankas Landbank',
        // 'brankas_metrobank' => 'Brankas Metrobank',
        'gcash' => 'GCash',
        'grab_pay' => 'GrabPay',
        'paymaya' => 'PayMaya',
    ];
    public $checkoutUrl = null;
    public $selectedPaymentMethod = null;

    public function mount()
    {
        // Initialize cart from session
        $this->cart = session('cart', []);

        // Check if cart is empty
        if (empty($this->cart)) {
            // Redirect back if cart is empty
            return redirect()->back();
        }
        // Proceed with calculating the total price if cart is not empty
        $this->calculateTotalPrice();
    }

    public function calculateTotalPrice()
    {
        $this->totalPrice = collect($this->cart)->sum(fn($item) => $item['ticket_price'] * $item['quantity']);
        session(['cart' => $this->cart]); // Update session cart
    }

    public function removeItem($ticketId)
    {
        $this->cart = collect($this->cart)
            ->reject(fn($item) => $item['ticket_id'] === $ticketId)
            ->values()
            ->toArray();
        $this->calculateTotalPrice();
    }

    public function initiatePayment($method)
    {
        $amountInPesos = $this->totalPrice;
        $amountInCents = $amountInPesos * 100;
        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authorization' => 'Basic c2tfdGVzdF9ZU1lBazR5Ylg3eGRNYWNKVGVXTVNac1M6',
                'content-type' => 'application/json',
            ])->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'send_email_receipt' => false,
                        'show_description' => true,
                        'show_line_items' => true,
                        'line_items' => [
                            [
                                'currency' => 'PHP',
                                'amount' => $amountInCents,
                                'description' => 'Tickets',
                                'name' => 'Event Tickets',
                                'quantity' => 1,
                            ],
                        ],
                        'payment_method_types' => [$method],
                        'description' => 'Event Payment',
                        'success_url' => route('attending'),
                        'metadata' => [
                            'user_id' => auth()->id(),
                            'event_id' => session('event_id'),
                            'ticket_id' => collect($this->cart)->pluck('ticket_id')->toArray(),
                            'cart_items' => json_encode($this->cart),
                            'total_price' => $amountInPesos,
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                // Get the session ID from the response
                $sessionId = $this->getSessionIdFromResponse($response->json());

                if ($sessionId) {
                    $this->checkoutUrl = $response->json()['data']['attributes']['checkout_url'];
                    $this->dispatch('redirect-to-checkout', ['url' => $this->checkoutUrl]);
                    $this->step = 3; // Move to the payment confirmation step

                    // Poll the payment status
                    $status = $this->pollPaymentStatus($sessionId);

                    if ($status === 'paid') {
                        $this->paymentStatus = 'success';
                    } elseif ($status === 'failed') {
                        $this->paymentStatus = 'failed';
                    }
                } else {
                    $this->dispatch('payment-error', ['message' => 'Session ID missing in response.']);
                }
            } else {
                $this->dispatch('payment-error', [
                    'message' => "PayMongo API error: " . $response->status(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Payment initiation error:', ['error' => $e->getMessage()]);
            $this->paymentStatus = 'failed';
        }
    }
    public function getSessionIdFromResponse($response)
    {
        return $response['data']['id'] ?? null;
    }

    public function pollPaymentStatus($sessionId)
    {
        try {
            // Make the GET request to check the payment session
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authorization' => 'Basic c2tfdGVzdF9ZU1lBazR5Ylg3eGRNYWNKVGVXTVNac1M6',
            ])->get("https://api.paymongo.com/v1/checkout_sessions/{$sessionId}");

            if ($response->successful()) {
                $status = $response->json()['data']['attributes']['status'];

                // Return the status
                return $status;
            } else {
                Log::error('Error while polling payment status:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while polling payment status:', [
                'message' => $e->getMessage(),
            ]);
        }

        return null; // Return null if polling fails
    }




    public function render()
    {
        return view('livewire.pages.checkout')->extends('layouts.auth');
    }
}
