<?php

namespace App\Livewire\Pages;

use App\Models\SuccessfulPurchase;
use Livewire\Component;

class PurchaseHistory extends Component
{
    public $search = '';
    public $filter = 'All';
    public $purchases = [];

    public function mount()
    {
        $this->loadPurchases();
    }

    public function updatedSearch()
    {
        $this->loadPurchases();
    }

    public function updatedFilter()
    {
        $this->loadPurchases();
    }

    public function loadPurchases()
    {
        $query = SuccessfulPurchase::with(['user', 'event', 'ticket'])
        ->when($this->search, function ($q) {
            $q->whereHas('event', function ($query) {
                $query->where('event_name', 'like', '%' . $this->search . '%');
            });
        })
            ->when($this->filter !== 'All', function ($q) {
                $q->where('transaction_type', $this->filter);
            });

        $this->purchases = $query->get(); // Return a collection, not an array
    }


    public function render()
    {
        return view('livewire.pages.purchase-history', [
            'filters' => [
                'all' => 'All',
                'transact' => 'Transaction',
                'refund' => 'Refund',
                'adjustment' => 'Adjustment',
                'chargeback' => 'Chargeback',
            ]
        ])->extends('layouts.auth');
    }
}
