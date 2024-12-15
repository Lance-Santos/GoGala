<?php

namespace App\Livewire\Pages;

use App\Models\User;
use App\Models\Event;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Organization;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\UserInvitation;
use Illuminate\Support\Carbon;
use App\Mail\UserInvitationMail;
use App\Models\OrganizationUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use TallStackUi\Traits\Interactions;

class OrganizationProfile extends Component
{
    use WithPagination, WithFileUploads, Interactions;

    public $search = '';
    public $quantity = 10;
    public $eventHeaders;
    public $eventRows;
    public $organization;
    public $organization_name;
    public $organization_slug;
    public $organization_bio;
    public $organization_email;
    public $img_url_profile;
    public $img_url_background;
    public string $event_name = '';
    public $event_latitude = null;
    public $event_longitude = null;
    public $event_address_string = '';
    public string $event_slug = '';
    public string $event_description = '';
    public array $event_date = []; // Will set defaults in mount
    public string $event_time_start = '';
    public string $event_time_end = '';
    public $event_img_url = null;
    public $event_img_banner_url = null;
    public $assignedUsers;
    public $searchQuery;
    public $results;
    public function mount($organization_slug)
    {

        $this->eventHeaders = [
            ['index' => 'id', 'label' => '#'],
            ['index' => 'event_name', 'label' => 'Event Name'],
            ['index' => 'organization.organization_name', 'label' => 'Organization Name'],
        ];

        // Retrieve the organization by slug
        $this->organization = Organization::where('organization_slug', $organization_slug)->firstOrFail();
        // Check if the user is the owner
        $isOwner = $this->organization->user_id === auth()->id();

        // If the user is not the owner, check if they are a member
        if (!$isOwner) {
            $isMember = OrganizationUser::where('organization_id', $this->organization->id)->where('user_id', auth()->id())->exists();
            if (!$isMember) {
                abort(403, 'You do not have access to this organization.');
            }
        }
        $this->assignedUsers = OrganizationUser::with(['organization', 'user'])
        ->whereHas('organization', function ($query) {
            $query->where('user_id', $this->organization->id);
        })
            ->get();

        // Initialize the fields for editing
        $this->organization_name = $this->organization->organization_name;
        $this->organization_slug = $this->organization->organization_slug;
        $this->organization_bio = $this->organization->organization_bio;
        $this->organization_email = $this->organization->organization_email;

        // Set default values for event_date
        $this->event_date = [now()->toDateString(), null]; // Default start date to now, end date to null

        // Initialize the event list
        $this->updateEventRows();
    }

    public function createEvent()
    {
        $originalStartTime = $this->event_time_start;
        $originalEndTime = $this->event_time_end;
        try {
            $this->event_time_start = $this->event_time_start ? Carbon::parse($this->event_time_start)->format('H:i:s') : null;
            $this->event_time_end = $this->event_time_end ? Carbon::parse($this->event_time_end)->format('H:i:s') : null;
        } catch (\Exception $e) {
            // Restore original values and show an error notification
            $this->event_time_start = $originalStartTime;
            $this->event_time_end = $originalEndTime;
            $this->toast()->error('Invalid Time Format', 'Please ensure the start and end times are valid.')->send();
            return;
        }

        // Validate the form fields, ensuring required fields are present
        try {
            $this->validate([
                'event_name' => 'required|string|max:255',
                'event_slug' => 'required|string|max:100|unique:events,event_slug',
                'event_description' => 'required|string|max:500',
                'event_date' => 'required|array|size:2',
                'event_address_string' => 'required',
                'event_date.0' => 'required|date',
                'event_date.1' => 'nullable|date|after_or_equal:event_date.0',
                'event_time_start' => 'required|date_format:H:i:s',
                'event_time_end' => [
                    'required',
                    'date_format:H:i:s',
                    function ($attribute, $value, $fail) {
                        if (Carbon::parse($this->event_time_start)->greaterThan(Carbon::parse($value))) {
                            $fail('The end time must be after the start time.');
                        }
                    },
                ],
                'event_img_url' => 'nullable|image|max:1024',
                'event_img_banner_url' => 'nullable|image|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->event_time_start = $originalStartTime;
            $this->event_time_end = $originalEndTime;
            throw $e;
        }
        $eventImgPath = $this->event_img_url ? $this->event_img_url->store('events/images', 'public') : null;
        $eventBannerPath = $this->event_img_banner_url ? $this->event_img_banner_url->store('events/banners', 'public') : null;
        try {
            $eventCreated = Event::create([
                'organization_id' => $this->organization->id,
                'event_name' => $this->event_name,
                'event_slug' => Str::slug($this->event_slug),
                'event_description' => $this->event_description,
                'event_date_start' => Carbon::parse($this->event_date[0]),
                'event_date_end' => $this->event_date[1] ? Carbon::parse($this->event_date[1]) : null,
                'event_time_start' => $this->event_time_start,
                'event_time_end' => $this->event_time_end,
                'event_latitude' => $this->event_latitude,
                'event_longitude' => $this->event_longitude,
                'event_address_string' => $this->event_address_string,
                'event_img_url' => $eventImgPath,
                'event_img_banner_url' => $eventBannerPath,
                'event_type' => 'seating',
                'event_status' => 'Private',
            ]);
            $this->toast()->success('Event Created', 'The event ' . $this->event_name . ' has been created')->flash()->send();
            return redirect()->route('event-profile',['organization_slug' => $this->organization_slug,'event_slug'=>$eventCreated->event_slug]);
        } catch (\Exception $e) {
            $this->event_time_start = $originalStartTime;
            $this->event_time_end = $originalEndTime;
            $this->toast()->error('Creation Failed', 'An error occurred: ' . $e->getMessage())->send();
        }
    }
    public function inviteUser($email)
    {
        $token = bin2hex(random_bytes(16));
        $invitation = UserInvitation::create([
            'organization_id' => $this->organization->id,
            'invited_by' => Auth::id(),
            'email' => $email,
            'token' => $token,
            'role' => 'moderator',
        ]);

        Mail::to($email)->send(new UserInvitationMail($invitation));
        $this->toast()->success('Added to the organization')->send();
    }
    public function updateEventRows()
    {
        // Start the query and eager load the organization relationship
        $pagination = Event::query()->where('organization_id' , $this->organization->id)
            ->with('organization')
            ->when($this->search, function ($query) {
                return $query->where('event_name', 'like', "%{$this->search}%");
            })
            ->paginate($this->quantity)
            ->withQueryString();

        $this->eventRows = $pagination->items();
        $this->pagination = $pagination;
    }


    public function render()
    {
        $this->results = User::where('username', 'like', '%' . $this->searchQuery . '%')->limit(7)->get();
        return view('livewire.pages.organization-profile',[
            "results" => $this->results
        ])->extends('layouts.auth');
    }

    public function updateOrganization()
    {
        $this->validate([
            'organization_name' => 'required|string|max:255',
            'organization_slug' => 'required|string|max:100|unique:organizations,organization_slug,' . $this->organization->id,
            'organization_bio' => 'required|string|max:250',
            'organization_email' => 'required|email|max:255|unique:organizations,organization_email,' . $this->organization->id,
            'img_url_profile' => 'nullable|image|max:1024',
            'img_url_background' => 'nullable|image|max:2048',
        ]);

        try {
            $profileImagePath = $this->img_url_profile ? $this->img_url_profile->store('organization/profile_images', 'public') : $this->organization->img_url_profile;
            $backgroundImagePath = $this->img_url_background ? $this->img_url_background->store('organization/background_images', 'public') : $this->organization->img_url_background;

            $this->organization->update([
                'organization_name' => $this->organization_name,
                'organization_slug' => Str::slug($this->organization_slug),
                'organization_bio' => $this->organization_bio,
                'organization_email' => $this->organization_email,
                'img_url_profile' => $profileImagePath,
                'img_url_background' => $backgroundImagePath,
            ]);

            $this->toast()->success('Organization Updated', 'The organization ' . $this->organization_name . ' has been updated')->flash()->send();
            return redirect()->route('organization.profile', $this->organization->organization_slug);
        } catch (\Exception $e) {
            $this->toast()->error('Update Failed', 'An error occurred while updating the organization: ' . $e->getMessage())->send();
        }
    }
}
