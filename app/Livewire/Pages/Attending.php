<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\PurchasedTicket;
use App\Models\SuccessfulPurchase;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\Auth;
use TallStackUi\Traits\Interactions;
class Attending extends Component
{
    use Interactions;
    public $attendingEvents = [];
    public $search = '';
    public $filter;

    public function mount()
    {
        $this->loadAttendingEvents();
    }

    public function loadAttendingEvents()
    {
        $query = PurchasedTicket::with(['event', 'ticket']) // Include ticket relationship
        ->where('user_id', Auth::id());

        if ($this->search) {
            $query->whereHas('event', function ($q) {
                $q->where('event_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filter === 'Ended') {
            $query->whereHas('event', function ($q) {
                $q->where('hasEnded', true);
            });
        } elseif ($this->filter === 'Ongoing') {
            $query->whereHas('event', function ($q) {
                $q->where('hasEnded', false);
            });
        }

        $this->attendingEvents = $query->get()
            ->groupBy('event_id')
            ->map(function ($tickets) {
                $event = $tickets->first()->event;

                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'event_description' => $event->event_description,
                    'event_time_end' => $event->event_time_end,
                    'tickets_count' => $tickets->count(),
                    'event_img_banner' => $event->event_img_banner_url,
                    'tickets' => $tickets->map(function ($ticket) {
                        $qrCode = Builder::create()->data($ticket->qr_code)->size(300)->margin(10)->build();
                        return [
                            'id' => $ticket->id,
                            'type' => $ticket->ticket->type,
                            'price' => $ticket->ticket->price,
                            'is_verified' => $ticket->is_verified,
                            'seat_id' => $ticket->seat_id,
                            'qr_code' => $qrCode->getDataUri(),
                        ];
                    })->toArray(),
                ];
            })->values()->toArray();
    }
    public function showQrCode($ticketId)
    {
        $ticket = PurchasedTicket::find($ticketId);

        if ($ticket) {
            return [
                'qrCode' => Builder::create()
                    ->data($ticket->qr_code)
                    ->size(300)
                    ->margin(10)
                    ->build()
                    ->getDataUri(),
            ];
        }
        return ['qrCode' => null];
    }

    public function cancelAttendance($ticketId)
    {
        $purchasedTicket = PurchasedTicket::where('id', $ticketId)
            ->with('successful_purchase')
            ->where('user_id', Auth::id())
            ->first();

        if (!$purchasedTicket) {
            $this->dialog()->error('Error', 'Ticket not found or access denied.')->send();
            return;
        }

        $originalPurchase = $purchasedTicket->successful_purchase;

        if (!$originalPurchase) {
            $this->dialog()->error('Error', 'Original purchase details not found.')->send();
            return;
        }

        try {
            $refundAmount = $purchasedTicket->price;
            $refundPercentage = 100;
            if ($originalPurchase->created_at->diffInDays(now()) > 2) {
                $refundPercentage -= 5;
            }
            $additionalFee = 0;
            switch (strtolower($originalPurchase->type)) {
                case 'card':
                    $additionalFee = ($refundAmount * 0.035) + 15;
                    break;
                case 'gcash':
                    $additionalFee = $refundAmount * 0.025;
                    break;
                case 'grabpay':
                    $additionalFee = $refundAmount * 0.022;
                    break;
                case 'maya':
                    $additionalFee = $refundAmount * 0.02;
                    break;
                default:
                    $additionalFee = 0;
                    break;
            }
            $refundAmount = ($refundAmount * ($refundPercentage / 100)) - $additionalFee;
            if ($refundAmount < 0) {
                $refundAmount = 0;
            }

            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://api.paymongo.com/v1/refunds', [
                'body' => json_encode([
                    'data' => [
                        'attributes' => [
                            'amount' => $refundAmount * 100, // Convert to cents
                            'payment_id' => $purchasedTicket->payment_id,
                            'reason' => 'requested_by_customer',
                        ],
                    ],
                ]),
                'headers' => [
                    'accept' => 'application/json',
                    'authorization' => 'Basic ' . base64_encode(env('PAYMONGO_WEBHOOK_SECRET')), // Secure your API key in .env
                    'content-type' => 'application/json',
                ],
            ]);

            // Decode API response
            $responseData = json_decode($response->getBody(), true);

            // Check if the refund is successful
            if (isset($responseData['data']['attributes']['status']) && $responseData['data']['attributes']['status'] === 'pending') {
                // Log the refund as a successful purchase entry
                SuccessfulPurchase::create([
                    'user_id' => $purchasedTicket->user_id,
                    'event_id' => $purchasedTicket->event_id,
                    'total_price' => $refundAmount,
                    'is_free' => $purchasedTicket->is_free,
                    'type' => $originalPurchase->type, // Use the type from the original purchase
                    'transaction_type' => 'refund',
                    'is_successful' => true,
                ]);

                // Delete the ticket
                $purchasedTicket->delete();
                $this->loadAttendingEvents(); // Refresh the events
                $this->dispatch('close-slide-ticket'); // Trigger frontend to close the ticket slide
                $this->dialog()->success('Success', 'Attendance successfully canceled and refunded.')->send();
            } else {
                $this->dialog()->error('Error', 'Refund failed. Please try again.')->send();
            }
        } catch (\Exception $e) {
            $this->dialog()->error('Error', 'An error occurred while processing the refund: ' . $e->getMessage())->send();
        }
    }



    public function render()
    {
        return view('livewire.pages.attending')->extends('layouts.auth');
    }
}
