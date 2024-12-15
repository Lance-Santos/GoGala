<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Event;

class Events extends Component
{
    public $search = ''; // Search query
    public $viewType = 'list'; // Default view type (list or grid)
    public $events = []; // Initialize an empty array for events
    public $isFree = false; // Track whether to filter for free events

    // Use mount to initialize properties when the component is loaded
    public function mount()
    {
        $this->loadEvents(); // Load events initially
    }

    // Load events with filters based on search and free events
    public function loadEvents()
    {
        // Start building the query
        $query = Event::with('organization')
            ->where('event_status', 'public')
            ->where('hasEnded', false)
            ->where('event_name', 'like', '%' . $this->search . '%'); // Apply search filter

        // Apply the filter for free events if $this->isFree is true
        if ($this->isFree) {
            $query->where('event_type', 'free'); // Filter by event type (assuming 'free' is a value in event_type)
        }

        // Execute the query and get the events
        $this->events = $query->get(); // This returns an Eloquent Collection
    }

    // Search button action
    public function searchEvents()
    {
        $this->loadEvents(); // Reload events based on search criteria
    }

    // Reset the search input and load events
    public function resetSearch()
    {
        $this->search = '';
        $this->loadEvents(); // Reload events after resetting search
    }

    // Toggle the free events filter
    public function toggleFreeEvents()
    {
        $this->isFree = !$this->isFree; // Toggle the free events filter
        $this->loadEvents(); // Reload events based on the updated filter
    }

    public function render()
    {
        return view('livewire.pages.events')->extends('layouts.auth'); // Return the view
    }
}
