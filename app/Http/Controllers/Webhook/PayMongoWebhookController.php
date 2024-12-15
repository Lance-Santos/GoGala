<?php

namespace App\Http\Controllers\Webhook;

use App\Models\Layouts;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PurchasedTicket;
use App\Models\SuccessfulPurchase;
use Illuminate\Routing\Controller;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\User;
use Symfony\Component\HttpFoundation\Response;

class PayMongoWebhookController extends Controller
{
    /**
     * Handle PayMongo Webhook Events.
     */
    public function handle(Request $request)
    {
        try {
            // Log the received payload for debugging
            Log::info('PayMongo Webhook Received:', $request->all());

            // Extract event type and attributes
            $eventType = $request->input('data.attributes.type');
            $attributes = $request->input('data.attributes.data.attributes');

            // Handle the webhook based on its type
            switch ($eventType) {
                case 'checkout_session.payment.paid':
                    $this->handlePaymentSuccess($attributes);
                    break;

                default:
                    // Log unhandled event types for future debugging
                    Log::info('Unhandled PayMongo Webhook Event', ['type' => $eventType]);
            }

            // Respond with 200 to acknowledge receipt
            return response()->json(['message' => 'Webhook handled successfully'], Response::HTTP_OK);
        } catch (\Throwable $e) {
            // Log any errors that occur during processing
            Log::error('Error processing PayMongo webhook:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Always respond with 2xx to avoid retries, even on errors
            return response()->json(['message' => 'Webhook processing error'], Response::HTTP_OK);
        }
    }
    /**
     * Handle a successful payment event.
     */
    private function handlePaymentSuccess($attributes)
    {
        $metadata = $attributes['payment_intent']['attributes']['metadata'] ?? [];
        $checkoutUrl = $attributes['checkout_url'] ?? null;
        $paymentMethod = $attributes['payment_method_used'] ?? null;
        $userId = $metadata['user_id'] ?? null;
        $totalPrice = $metadata['total_price'] ?? 0;
        $eventId = $metadata['event_id'] ?? null;
        $cartItems = json_decode($metadata['cart_items'] ?? '[]', true);
        $paymentId = $attributes['payments'][0]['id'] ?? null;
        $successful_purchases = SuccessfulPurchase::create([
            'user_id' => $userId,
            'event_id' => $eventId,
            'total_price' => $totalPrice,
            'is_free' => $totalPrice == 0,
            'type' => $paymentMethod,
            'transaction_type' => 'transact',
            'is_successful' => true,
            'processed_at' => now(),
        ]);
        $layout = Layouts::where('event_id', $eventId)->first();
        $elements = is_string($layout->data) ? json_decode($layout->data, true) : $layout->data;

        foreach ($cartItems as $item) {
            $ticketId = $item['ticket_id'];
            $quantity = $item['quantity'];
            $price = $item['ticket_price'];
            $seatId = $item['seat_id'] ?? null;
            $seatIdentifier = $item['seat_identifier'] ?? null; // Check if seat_id exists
            for ($i = 0; $i < $quantity; $i++) {
                $qrCodeString = Str::uuid();
                PurchasedTicket::create([
                    'user_id' => $userId,
                    'event_id' => $eventId,
                    'ticket_id' => $ticketId,
                    'successful_purchases_id' => $successful_purchases->id,
                    'is_free' => $totalPrice == 0,
                    'is_verified' => false,
                    'seat_id' => $seatId,
                    'seat_identifier' => $seatIdentifier,
                    'payment_id' => $paymentId,
                    'price' => $price,
                    'qr_code' => $qrCodeString,
                ]);
                $isClaimed = $this->updateSeatClaim($layout, $seatIdentifier, $userId);
            }
        }

        Log::info('IsClaimed?',[$isClaimed]);
        Log::info('Payment successful and tickets generated from cart items.', [
            'user_id' => $userId,
            'event_id' => $eventId,
            'cart_items' => $cartItems,
            'total_price' => $totalPrice,
            'payment_method' => $paymentMethod,
            'payment_id' => $paymentId,
        ]);
    }
    private function updateSeatClaim($layout, $seatId, $userId)
    {
        try {
            // Log layout data, seatId, and userId to ensure we're receiving the correct data
            Log::info('Layout data before updating seat:', ['layout' => $layout, 'seatId' => $seatId, 'userId' => $userId]);

            // Check if the user exists
            $user = User::find($userId);
            if (!$user) {
                Log::error('User not found', ['userId' => $userId]);
                return false; // Exit early if the user is not found
            }

            // Decode the layout data
            $elements = is_string($layout->data) ? json_decode($layout->data, true) : $layout->data;
            if (!$elements) {
                Log::error('Failed to decode layout data', ['layout_data' => $layout->data]);
                return false; // Exit if layout data cannot be decoded
            }

            // Loop through the layout elements to find the seat container and the seat
            foreach ($elements as &$element) {
                if ($element['type'] === 'seatContainerRect' || $element['type'] === 'seatContainerRound' || $element['type'] === 'seatContainerTable') {
                    foreach ($element['seats'] as &$seat) {
                        // Log the seat being checked
                        Log::info('Checking seat:', ['seat' => $seat]);

                        if ($seat['id'] == $seatId) {
                            // Check if the seat is already claimed
                            if ($seat['isClaimed']) {
                                Log::info('Seat already claimed', ['seatId' => $seatId]);
                                return false; // Return false if the seat is already claimed
                            }

                            // Claim the seat
                            $seat['isClaimed'] = true;
                            $seat['userID'] = $user->id;
                            $seat['userName'] = $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name . ' ' . $user->suffix;

                            // Log the updated seat data
                            Log::info('Seat claimed and updated:', ['updated_seat' => $seat]);

                            // Save the updated layout back to the database
                            $layout->data = json_encode($elements);

                            // Log before saving the layout
                            Log::info('Saving updated layout data...');

                            $saveResult = $layout->save();
                            if ($saveResult) {
                                Log::info('Layout data saved successfully.');
                            } else {
                                Log::error('Failed to save layout data.');
                            }

                            return true; // Return true if the seat was successfully claimed and the layout saved
                        }
                    }
                }
            }

            // If seat not found, log it
            Log::error('Seat not found in layout', ['seatId' => $seatId]);
            return false; // Return false if seat is not found

        } catch (\Exception $e) {
            // Catch any exceptions and log the error
            Log::error('Error updating seat claim', [
                'exception_message' => $e->getMessage(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'seatId' => $seatId,
                'userId' => $userId
            ]);
            return false; // Return false if there is an error
        }
    }


}
