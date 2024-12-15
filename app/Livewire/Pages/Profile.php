<?php

namespace App\Livewire\Pages;

use App\Models\User;
use App\Models\Event;
use Livewire\Component;
use App\Models\Organization;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class Profile extends Component
{
    use WithFileUploads, WithPagination; // Handle file uploads and pagination

    // Profile form properties
    public $first_name, $middle_name, $last_name, $suffix, $username, $email, $gender, $contact_number, $bio;
    public $profile_img = [], $banner_img = []; // For image uploads

    // Pagination and search properties for organizations and events
    public $search = '', $quantity = 10;
    public $eventSearch = '';
    public $headers, $rows, $eventHeaders, $eventRows;
    public $current_password, $password, $password_confirmation;

    public function mount()
    {
        // Initialize the profile data
        $user = Auth::user();
        $this->first_name = $user->first_name;
        $this->middle_name = $user->middle_name;
        $this->last_name = $user->last_name;
        $this->suffix = $user->suffix;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->gender = $user->gender;
        $this->contact_number = $user->contact_number;
        $this->bio = $user->bio;
        $this->profile_img = null;
        $this->banner_img = null;


        // Initialize headers for organizations and events
        if (Auth::user()->role_id === 2) {
            $this->headers = [
                ['index' => 'organization_name', 'label' => 'Organization Name'],
            ];



            $this->updateOrganizationRows();
            $this->updateEventRows();
        }
    }

    // Method to update profile information
    public function updateProfileInformation()
    {
        $user = Auth::user();

        // Validate form input
        $this->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'gender' => 'nullable|string|max:10',
            'contact_number' => 'nullable|string|max:15',
            'bio' => 'nullable|string|max:500',
        ]);

        // Update the user's profile data
        $user->first_name = $this->first_name;
        $user->middle_name = $this->middle_name;
        $user->last_name = $this->last_name;
        $user->suffix = $this->suffix;
        $user->username = $this->username;
        $user->email = $this->email;
        $user->gender = $this->gender;
        $user->contact_number = $this->contact_number;
        $user->bio = $this->bio;
        if ($this->profile_img) {
            $this->validate(['profile_img' => 'image|max:1024']);
            $user->profile_img_url = $this->profile_img->store('profile_images', 'public');
        }

        if ($this->banner_img) {
            $this->validate(['banner_img' => 'image|max:1024']);
            $user->banner_img_url = $this->banner_img->store('banner_images', 'public');
        }
        $user->save();
        session()->flash('message', 'Profile updated successfully!');
    }

    // Method to update organization rows for pagination
    public function updateOrganizationRows()
    {
        $pagination = Organization::query()
            ->when($this->search, function (Builder $query) {
                return $query->where('organization_name', 'like', "%{$this->search}%");
            })
            ->paginate($this->quantity)
            ->withQueryString();

        $this->rows = $pagination->items();
        $this->pagination = $pagination;
    }

    public function updateEventRows()
    {
        // Define the headers for the events table
        $this->eventHeaders = [
            ['index' => 'event_name', 'label' => 'Event Name'],
            ['index' => 'organization_name', 'label' => 'Organization'],
        ];

        // Query events directly, filtering by authenticated user's organization
        $pagination = Event::query()
            ->with('organization') // Eager load organization relationship
            ->whereHas('organization', function ($query) {
                $query->where('user_id', Auth::id()); // Only get events related to the authenticated user's organization
            })
            ->when($this->eventSearch, function (Builder $query) {
                return $query->where('event_name', 'like', "%{$this->eventSearch}%");
            })
            ->paginate($this->quantity)
            ->withQueryString();

        // Map the events to rows
        $this->eventRows = $pagination->map(function ($event) {
            return [
                'event_name' => $event->event_name,
                'event_slug' => $event->event_slug,
                'organization_name' => $event->organization->organization_name,
                'organization_slug' => $event->organization->organization_slug,
            ];
        })->toArray();

        // Update pagination
        $this->pagination = $pagination;
    }




    // Reset pagination on search update
    public function updatedSearch()
    {
        $this->resetPage();
        $this->updateOrganizationRows();
    }

    // Reset pagination on event search update
    public function updatedEventSearch()
    {
        $this->resetPage();
        $this->updateEventRows();
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($this->current_password, Auth::user()->password)) {
            session()->flash('password_message', 'Current password is incorrect.');
            return;
        }

        Auth::user()->update([
            'password' => Hash::make($this->password),
        ]);

        session()->flash('password_message', 'Password updated successfully!');
    }

    // Render the view for the profile page
    public function render()
    {
        return view('livewire.pages.profile')
            ->extends('layouts.auth');
    }
}
