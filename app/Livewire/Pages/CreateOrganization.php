<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Organization;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use TallStackUi\Traits\Interactions;

class CreateOrganization extends Component
{
    use WithFileUploads, Interactions;

    // Component properties for binding to the form
    public $organization_name;
    public $organization_slug;
    public $organization_bio;
    public $organization_email;
    public $img_url_profile;
    public $img_url_background;

    public function mount()
    {
        // Initialization logic can go here if needed
    }

    public function createOrganization()
    {
        // Validate the form data
        $this->validate([
            'organization_name' => 'required|string|max:255',
            'organization_slug' => 'required|string|max:100|unique:organizations,organization_slug',
            'organization_bio' => 'required|string|max:250',
            'organization_email' => 'required|email|max:255|unique:organizations,organization_email',
            'img_url_profile' => 'nullable|image|max:1024', // 1MB max size
            'img_url_background' => 'nullable|image|max:2048', // 2MB max size
        ]);

        try {
            // Handle image uploads if provided
            $profileImagePath = $this->img_url_profile ? $this->img_url_profile->store('organization/profile_images', 'public') : null;
            $backgroundImagePath = $this->img_url_background ? $this->img_url_background->store('organization/background_images', 'public') : null;

            // Create a new organization
            Organization::create([
                'user_id' => Auth::id(),
                'organization_name' => $this->organization_name,
                'organization_slug' => Str::slug($this->organization_slug),
                'organization_bio' => $this->organization_bio,
                'organization_email' => $this->organization_email,
                'img_url_profile' => $profileImagePath,
                'img_url_background' => $backgroundImagePath,
            ]);

            // Redirect or emit an event for success notification
            $this->toast()->success('Organization Created', 'The organization ' . $this->organization_name . ' has been created')->flash()->send();
            return redirect()->route('profile');
        } catch (\Exception $e) {
            // Handle any errors that occur during the organization creation process
            $this->toast()->error('Creation Failed', 'An error occurred while creating the organization: ' . $e->getMessage())->flash()->send();
        }
    }

    public function render()
    {
        return view('livewire.pages.create-organization')->extends('layouts.auth');
    }
}
