<?php

namespace App\Livewire\Pages;

use App\Models\Event;
use App\Models\Layouts;
use Livewire\Component;
use App\Models\Favorite;
use App\Models\EventRating;
use Livewire\Attributes\On;
use App\Models\EventQuestion;
use App\Models\PurchasedTicket;
use App\Models\SuccessfulPurchase;
use Illuminate\Support\Facades\Auth;
use TallStackUi\Traits\Interactions;

class ViewEvent extends Component
{
    use Interactions;
    public $cart = [];
    public $event;
    public $score;
    public $comment;
    public $eventType;
    public $isFavorited = false;
    public $existingPurchase = null;
    public $totalPrice;
    public $elements;
    public $hasEnded;
    public $question;
    public $existingRating;
    public $questions = [];
    protected $casts = [
        'cart' => 'array',
    ];
    public function evaluate(int $quantity): void
    {
        $this->score = $quantity;
    }
    public function submitRating()
    {
        // Validation rules inside the function
        $this->validate([
            'score' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if the user has already rated the event
        $existingRating = EventRating::where('event_id', $this->event->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingRating) {
            $this->toast()->error('You already submitted a rating')->send();

            return;
        }
        EventRating::create([
            'event_id' => $this->event->id,
            'user_id' => auth()->id(),
            'score' => $this->score,
            'comment' => $this->comment,
        ]);
        $this->toast()->success('Rating submitted')->send();
        // Reset the form fields after submission
        $this->score = null;
        $this->comment = '';
    }
    public function mount($eventSlug)
    {
        // Get event and its related data
        $this->event = Event::with('organization', 'tickets')
            ->where('event_slug', $eventSlug)
            ->firstOrFail();
        $this->questions = EventQuestion::where('event_id', $this->event->id)->where('user_id', auth()->id())->with('user','answers')->latest()->take(5)->get();
        // dd($this->questions);
        $this->hasEnded = $this->event->hasEnded == 1;
        // Set event type for conditional rendering
        $this->eventType = $this->event->event_type;

        // Check if the user has already joined this event
        $this->existingPurchase = SuccessfulPurchase::where('user_id', Auth::user()->id)
            ->where('event_id', $this->event->id)
            ->first();
        $this->existingRating = EventRating::where('event_id', $this->event->id)
        ->where('user_id', auth()->id())
            ->first();

        // If a rating exists, pre-fill the form fields with the existing rating
        if ($this->existingRating) {
            $this->score = $this->existingRating->score;
            $this->comment = $this->existingRating->comment;
        }
        $layout = Layouts::where('event_id', $this->event->id)->first();
        if ($layout && is_string($layout->data)) {
            $this->elements = json_decode($layout->data);
        } elseif ($layout && is_array($layout->data)) {
            $this->elements = $layout->data;
        } else {
            // Handle the case where there's no layout or invalid data
            $this->elements = [];
        }
        // Check if the event is favorited by the user
        $this->isFavorited = Favorite::where('event_id', $this->event->id)
            ->where('user_id', Auth::id())
            ->exists();
    }
    public function submitQuestion()
    {
        // Validate the question input here
        $validatedData = $this->validate([
            'question' => 'required|string|max:255',
        ]);

        // Store the question in the database
        EventQuestion::create([
            'question' => $this->question,
            'event_id' => $this->event->id,
            'user_id' => auth()->id(),
        ]);
        $this->questions = EventQuestion::where('event_id', $this->event->id)->latest()->take(5)->get();
        $this->question = '';
        $this->toast()->success('Question submitted')->send();
    }
    public function addToCart($ticketId, $ticketType, $ticketPrice, $ticketQuantity)
    {
        $existingTicket = collect($this->cart)->firstWhere('ticket_id', $ticketId);

        if ($existingTicket) {
            $this->cart = collect($this->cart)->map(function ($item) use ($ticketId) {
                if ($item['ticket_id'] == $ticketId) {
                    $item['quantity'] += 1;
                }
                return $item;
            })->toArray();
        } else {
            $this->cart[] = [
                'ticket_id' => $ticketId,
                'ticket_type' => $ticketType,
                'ticket_price' => $ticketPrice,
                'quantity' => 1,
                'event_id' =>$this->event->id,
                'max_quantity' => $ticketQuantity,
            ];
        }

        $this->updateTotalPrice();
    }

    public function updateItemQuantity($index)
    {
        if (!isset($this->cart[$index])) {
            return;
        }

        $this->cart[$index]['quantity'] = max(1, (int) $this->cart[$index]['quantity']);
        $this->updateTotalPrice();
    }
    #[On('create-cart')]
    public function addToCartSeats($ticketId, $ticketType, $ticketPrice, $ticketQuantity, $seatNumber, $seatId)
    {
        // Check if the seat has already been purchased
        $existingPurchasedTicket = PurchasedTicket::where('seat_identifier', $seatId)->first();
        if ($existingPurchasedTicket) {
            // If the seat is already purchased, show an error toast and return
            $this->toast()->error('This seat has already been purchased.')->send();
            return;
        }

        // Check if the seat is already in the cart
        $existingSeat = collect($this->cart)->firstWhere('seat_id', $seatId);

        if ($existingSeat) {
            // If the seat is already in the cart, show an error toast and return
            $this->toast()->error('Ticket already exists in the cart.')->send();
            return;
        }

        // Add the seat to the cart
        $this->cart[] = [
            'ticket_id' => $ticketId,
            'ticket_type' => $ticketType,
            'ticket_price' => $ticketPrice,
            'quantity' => 1, // Fixed quantity for a seat
            'event_id' => $this->event->id,
            'max_quantity' => $ticketQuantity,
            'seat_id' => $seatNumber,
            'seat_identifier' => $seatId // Unique seat identifier
        ];
        $this->toast()->success('Ticket added to cart')->send();

        // Update the total price
        $this->updateTotalPrice();
    }


    public function updateTotalPrice()
    {
        $this->totalPrice = collect($this->cart)->sum(function ($item) {
            return $item['ticket_price'] * $item['quantity'];
        });
    }

    public function removeFromCart($ticketId, $seatId = null)
    {
        $this->cart = array_values(array_filter($this->cart, function ($item) use ($ticketId, $seatId) {
            return $seatId ? $item['seat_id'] != $seatId : $item['ticket_id'] != $ticketId;
        }));

        $this->updateTotalPrice();
    }


    public function createCheckoutSession()
    {
        // Save cart data to session
        // dd ($this->cart);
        session([
            'cart' => $this->cart,
            'event_id' => $this->event->id,
            'cart_total_price' => $this->totalPrice,
            'cart_created_at' => now(), // Add timestamp for expiration
        ]);

        // Redirect to the checkout page
        return redirect()->route('checkout');
    }

    public function toggleFavorite()
    {
        // Toggle favorite status
        if ($this->isFavorited) {
            Favorite::where('event_id', $this->event->id)
                ->where('user_id', Auth::id())
                ->delete();
            $this->isFavorited = false;
        } else {
            Favorite::create([
                'event_id' => $this->event->id,
                'user_id' => Auth::id(),
            ]);
            $this->isFavorited = true;
        }
    }

    public function joinEvent()
    {
        // Check if the user has already joined the event
        $existingPurchase = SuccessfulPurchase::where('user_id', Auth::user()->id)
            ->where('event_id', $this->event->id)
            ->first();

        // If the user has not joined the event yet
        if (!$existingPurchase) {
            // Create the successful purchase record for a free event
            SuccessfulPurchase::create([
                'user_id' => Auth::user()->id,
                'event_id' => $this->event->id,
                'ticket_id' => null,
                'total_price' => 0.00,  // Free event
                'qr_code' => null,
                'isFree' => true,
            ]);
            $this->existingPurchase = true;

            // Success message
            $this->toast()->success('You joined', 'You are now attending the event: ' . $this->event->event_name . '. Check your profile for your attended events.')->send();
        } else {
            // Message for already joined event
            $this->toast()->info('Already joined', 'You are already attending the event: ' . $this->event->event_name)->send();
        }
    }

    public function render()
    {
        return view('livewire.pages.view-event')->extends('layouts.auth');
    }
}
