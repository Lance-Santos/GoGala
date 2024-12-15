<?php

namespace App\Livewire;

use App\Models\Tickets;
use Livewire\Component;

class LayoutPurchase extends Component
{
    public $layout;
    public $popup;
    public $selectedSeat;
    public $ticketDetails;
    public  $seatModalOpen;
    public function showTicket($ticket_id)
    {
        $ticket = Tickets::find($ticket_id);
        $this->ticketDetails = $ticket;
        $this->selectedSeat['price'] = $ticket->price;
        $this->selectedSeat['type'] = $ticket->type;
    }
    public function mount($layout, $popup)
    {
        $this->$popup = $popup;
        $this->layout = $layout;
    }
    public function addToCart($seatNumber,$seatId){
        $this->dispatch('create-cart',ticketId: $this->ticketDetails->id, ticketType: $this->ticketDetails->type, ticketPrice: $this->ticketDetails->price,ticketQuantity: 1,seatNumber:$seatNumber,seatId:$seatId);
    }
    public function render()
    {
        return view('livewire.layout-purchase');
    }
}
