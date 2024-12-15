<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Favorite;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class Favorites extends Component
{
    public $favorites;
    public $search = ''; // For the search input
    public $viewType = 'list';

    public function mount()
    {
        $this->loadFavorites();
    }

    // Load favorites with an optional search query
    public function loadFavorites()
    {
        $query = Favorite::where('user_id', Auth::id());

        // If there is a search term, filter by event name
        if ($this->search) {
            $query->whereHas('event', function ($q) {
                $q->where('event_name', 'like', '%' . $this->search . '%');
            });
        }

        $this->favorites = $query->get();
    }

    public function render()
    {
        return view('livewire.pages.favorites')->extends('layouts.auth');
    }
}
