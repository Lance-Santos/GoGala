<?php

namespace App\Livewire\Pages;

use App\Models\Event;
use App\Models\Layouts;
use App\Models\Tickets;
use Livewire\Component;
use App\Models\Category;
use App\Models\EventAnswer;
use App\Models\EventRating;
use Illuminate\Support\Str;
use App\Models\Organization;
use Livewire\WithPagination;
use App\Models\EventQuestion;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use App\Models\PurchasedTicket;
use Illuminate\Validation\Rule;
use App\Models\OrganizationUser;
use TallStackUi\Traits\Interactions;

class EventProfile extends Component
{
    use WithFileUploads, Interactions, WithPagination;

    public $organization;
    public $event;

    public string $event_name = '';
    public string $event_slug = '';
    public string $event_description = '';
    public array $event_date = [];
    public string $event_time_start = '';
    public string $event_time_end = '';
    public $event_img_url = null;
    public $event_img_banner_url = null;
    public string $event_status = '';
    public string $event_type = '';
    public $event_latitude = null;
    public $event_longitude = null;
    public $event_address_string = '';
    public $hasEnded;
    public $event_backup;
    public $ticketHeaders;
    public $ticketRows;
    public $newTicketType;
    public $ticketId;
    public $newTicketPrice = 0;
    public $newTicketQuantity = 0;
    public ?string $ticketSearch = null;
    public $ticketQuantity = 10;
    public $ifHasTickets;
    public $orgSlug;
    public $rows;
    public $elements;
    public $verifiedFilter;
    public $blacklistedFilter;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $score = 3;
    public $comment;
    public $search = '';
    public $quantity = 10;
    public $pagination;
    public $tickets;
    public $ticketCount;
    public $totalRevenue;
    public $questions;
    public $selectedQuestionId;
    public $replyText;
    public $ratings;
    public $averageRating;
    public $selectedCategories = [];

    public function mount($organization_slug, $event_slug)
    {

        $this->ticketHeaders =
            [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'type', 'label' => 'Name'],
                ['index' => 'formattedPrice', 'label' => 'Price'],
                ['index' => 'quantity', 'label' => 'Quantity'],
                ['index' => 'action']
            ];
        $this->organization = Organization::where('organization_slug', $organization_slug)->firstOrFail();
        $this->event = Event::where('event_slug', $event_slug)->with('categories')
            ->where('organization_id', $this->organization->id)
            ->firstOrFail();
        $this->selectedCategories = $this->event->categories->pluck('id')->toArray();
        $isOwner = $this->organization->user_id === auth()->id();
        if (!$isOwner) {
            $isMember = OrganizationUser::where('organization_id', $this->organization->id)->where('user_id', auth()->id())->exists();
            if (!$isMember) {
                abort(403, 'You do not have access to this organization.');
            }
        }
        $this->questions = EventQuestion::where('event_id', $this->event->id)->with('user','answers')->latest()->get();
        $this->ticketCount = PurchasedTicket::where('event_id', $this->event->id)->count();
        $this->totalRevenue = PurchasedTicket::where('event_id', $this->event->id)->sum('price');
        $this->event_name = $this->event->event_name;
        $this->event_slug = $this->event->event_slug;
        $this->ratings = EventRating::where('event_id', $this->event->id)->get();
        $this->averageRating = number_format(EventRating::where('event_id', $this->event->id)->avg('score'), 1);

        $this->event_description = $this->event->event_description;
        $this->event_date = [
            $this->event->event_date_start,
            $this->event->event_date_end,
        ];
        $this->event_time_start = $this->event->event_time_start;
        $this->event_time_end = $this->event->event_time_end;
        $this->event_status = $this->event->event_status;
        $this->event_type = $this->event->event_type;
        $this->event_latitude = $this->event->event_latitude;
        $this->event_longitude = $this->event->event_longitude;
        $this->event_address_string = $this->event->event_address_string;
        $this->hasEnded = $this->event->hasEnded == 1;
        $this->orgSlug = $organization_slug;
        $this->event_backup = [
            'event_name' => $this->event_name,
            'event_slug' => $this->event_slug,
            'event_description' => $this->event_description,
            'event_date' => $this->event_date,
            'event_time_start' => $this->event_time_start,
            'event_time_end' => $this->event_time_end,
            'event_status' => $this->event_status,
            'event_type' => $this->event_type,
            'event_latitude' => $this->event_latitude,
            'event_longitude' => $this->event_longitude,
            'event_address_string' => $this->event_address_string,
        ];
        $layout = Layouts::where('event_id', $this->event->id)->first();
        if ($layout && is_string($layout->data)) {
            $this->elements = json_decode($layout->data);
        } elseif ($layout && is_array($layout->data)) {
            // If it's already an array, assign it directly
            $this->elements = $layout->data;
        } else {
            // Handle the case where there's no layout or invalid data
            $this->elements = [];
        }
        // $this->showTickets();
    }
    public function updatedSelectedCategories($categories)
    {
        $this->event->categories()->sync($categories);
    }
    public function showReplyField($questionId)
    {
        $this->selectedQuestionId = $questionId;
        $this->replyText = ''; // Clear any previous reply text
    }
    public function submitReply()
    {
        // Validate the reply text
        $this->validate([
            'replyText' => 'required|string|max:255',
        ]);

        // Check if the user has already replied to this question
        $existingReply = EventAnswer::where('event_question_id', $this->selectedQuestionId)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReply) {
            // If a reply already exists, show an error toast and return early
            $this->toast()->error('You have already replied to this question.')->send();
            return;
        }

        // Create the reply (answer) for the selected question
        EventAnswer::create([
            'event_question_id' => $this->selectedQuestionId,  // Correct column name
            'user_id' => auth()->id(),
            'answer_text' => $this->replyText,
        ]);

        // Reset the selected question and reply text after submitting
        $this->selectedQuestionId = null;
        $this->replyText = '';

        // Flash a success message
        $this->toast()->success('Your reply has been submitted!')->send();
    }

    public function resetToBackup()
    {
        $this->event_name = $this->event_backup['event_name'];
        $this->event_slug = $this->event_backup['event_slug'];
        $this->event_description = $this->event_backup['event_description'];
        $this->event_date = $this->event_backup['event_date'];
        $this->event_time_start = $this->event_backup['event_time_start'];
        $this->event_time_end = $this->event_backup['event_time_end'];
        $this->event_status = $this->event_backup['event_status'];
        $this->event_type = $this->event_backup['event_type'];
        $this->event_latitude = $this->event_backup['event_latitude'];
        $this->event_longitude = $this->event_backup['event_longitude'];
        $this->event_address_string = $this->event_backup['event_address_string'];
        $this->showTickets();
    }
    public function editTicket($ticketId){
        $ticket = Tickets::where('id', $ticketId)->first();
        if (!$ticket) {
            $this->toast()->error('Ticket not found')->send();
            return;
        }

    // Populate the form fields with the ticket's current values for editing
        $this->ticketId = $ticket->id;
        $this->newTicketType = $ticket->type;
        $this->newTicketPrice = $ticket->price;
        $this->newTicketQuantity = $ticket->quantity;
    }
    public function updateTicket()
    {

        $this->validate([
            'newTicketType' => ['required', 'string'],
            'newTicketPrice' => ['required', 'numeric', 'min:0'],
            'newTicketQuantity' => 'required|integer|min:1',
        ]);

        $ticket = Tickets::find($this->ticketId);
        if (!$ticket) {
            $this->toast()->error('Ticket not found')->send();
            return;
        }

        $ticket->update([
            'type' => $this->newTicketType,
            'price' => $this->newTicketPrice,
            'quantity' => $this->newTicketQuantity,
        ]);

        $this->resetTicketForm();
        $this->toast()->success('Ticket has been updated')->send();
        $this->showTickets();
    }



    public function updateEvent()
    {
        $originalStartTime = $this->event_time_start;
        $originalEndTime = $this->event_time_end;

        try {
            // Parse and format time inputs to 24-hour format (H:i:s) for storage
            $this->event_time_start = $this->event_time_start ? Carbon::parse($this->event_time_start)->format('H:i:s') : null;
            $this->event_time_end = $this->event_time_end ? Carbon::parse($this->event_time_end)->format('H:i:s') : null;
        } catch (\Exception $e) {
            // Restore original values and show an error notification
            $this->event_time_start = $originalStartTime;
            $this->event_time_end = $originalEndTime;
            $this->toast()->error('Invalid Time Format', 'Please ensure the start and end times are valid.')->send();
            return;
        }

        // Validate the form data
        $this->validate([
            'event_name' => 'string|max:255',
            'event_slug' => 'string|max:100|unique:events,event_slug,' . $this->event->id,
            'event_description' => 'string|max:500',
            'event_date' => 'array|size:2',
            'event_date.0' => 'date',
            'event_date.1' => 'nullable|date|after_or_equal:event_date.0',
            'event_time_start' => 'date_format:H:i:s',
            'event_time_end' => 'date_format:H:i:s|after_or_equal:event_time_start',
            'event_img_url' => 'nullable|image|max:1024',
            'event_img_banner_url' => 'nullable|image|max:2048',
            'event_status' => 'string|max:50',
            'event_type' => 'string|max:50',
        ]);

        // Handle image uploads
        $eventImgPath = $this->event_img_url ? $this->event_img_url->store('events/images', 'public') : $this->event->event_img_url;
        $eventBannerPath = $this->event_img_banner_url ? $this->event_img_banner_url->store('events/banners', 'public') : $this->event->event_img_banner_url;

        // Update the event
        $this->event->update([
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
            'event_status' => $this->event_status,
            'event_type' => $this->event_type,
            'hasEnded' => $this->hasEnded
        ]);

        // Reformat the times back to 12-hour format after update for display
        $this->event_time_start = $this->event_time_start ? Carbon::parse($this->event_time_start)->format('h:i A') : null;
        $this->event_time_end = $this->event_time_end ? Carbon::parse($this->event_time_end)->format('h:i A') : null;

        $this->toast()->success('Event Updated', 'The event ' . $this->event_name . ' has been updated')->flash()->send();
        return redirect()->route('event-profile', [
            'organization_slug' => $this->organization->organization_slug,
            'event_slug' => $this->event->event_slug
        ]);
    }


    public function deleteEvent()
    {
        $this->event->delete();

        $this->toast()->success('Event Deleted', 'The event has been deleted')->flash()->send();
        return redirect()->route('events.index', $this->organization->organization_slug);
    }

    public function render()
    {
        $categories = Category::all();
        $tickets = $this->showTickets();
        $purchasedTickets = $this->showPurchasedTickets();
        return view('livewire.pages.event-profile', [
            'purchasedTicketPagination' => $purchasedTickets['pagination'],
            'ticketsThing' => $tickets['pagination'],
            'categories' => $categories
        ])->extends('layouts.auth');
    }


    public function showTickets()
    {
        // Retrieve tickets based on event_id and filter by search term
        $tickets = Tickets::query()
            ->where('event_id', $this->event->id)
            ->with('events')
            ->when($this->ticketSearch, function ($query) {
                $query->where('type', 'like', "%{$this->ticketSearch}%");
            })
            ->paginate($this->ticketQuantity)
            ->withQueryString();

        // Set the pagination result, no need for separate ticketRows
        return [
            'pagination' => $tickets
        ];
    }

    public function showPurchasedTickets()
    {
        $pagination = PurchasedTicket::query()
            ->with(['user', 'event'])
            ->where('event_id', $this->event->id)
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($subQuery) {
                    $subQuery->where('first_name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->verifiedFilter !== null, function ($query) {
                if ($this->verifiedFilter == 'Verified') {
                    $query->where('is_verified', true);
                } elseif ($this->verifiedFilter == 'Not Verified') {
                    $query->where('is_verified', false);
                }
            })
            ->when($this->blacklistedFilter !== null, function ($query) {
                if ($this->blacklistedFilter == 'Blacklisted') {
                    $query->where('is_blacklisted', true);
                } elseif ($this->blacklistedFilter == 'Whitelisted') {
                    $query->where('is_blacklisted', false);
                }
            })
            ->orderBy($this->sortBy, $this->sortDirection) // sorting based on the selected column and direction
            ->paginate($this->quantity);

        return [
            'pagination' => $pagination, // Pass the pagination object for display
        ];
    }
    public function addTicket()
    {
        $this->validate([
            'newTicketType' => [
                'required',
                'string',
                Rule::unique('tickets', 'type')->where(function ($query) {
                    return $query->where('event_id', $this->event->id);
                }),
            ],
            'newTicketPrice' => [
                'required',
                'numeric',
                'min:0',
                Rule::unique('tickets', 'price')->where(function ($query) {
                    return $query->where('event_id', $this->event->id);
                }),
            ],
            'newTicketQuantity' => 'required|integer|min:1',
        ]);
        Tickets::create([
            'event_id' => $this->event->id,
            'type' => $this->newTicketType,
            'price' => $this->newTicketPrice,
            'quantity' => $this->newTicketQuantity,
            'isFull' => false,
        ]);
        $this->resetTicketForm();
        $this->checkTicket();
        $this->showTickets();
        $this->toast()->success('Ticket has been created')->send();
    }
    public function checkTicket()
    {
        $this->ifHasTickets = $this->event->hasTickets();
    }

    public function resetTicketForm()
    {
        // Reset the form fields
        $this->ticketId = null;
        $this->newTicketType = null;
        $this->newTicketPrice = 0;
        $this->newTicketQuantity = 0;
    }
    public function deleteTicket($id)
    {
        $this->dialog()
            ->question('Warning!', 'Are you sure?')
            ->confirm(method: 'confirmed', params: $id)
            ->cancel('Cancel')
            ->send();
    }
    public function confirmed($id)
    {
        $ticket = Tickets::find($id);

        if ($ticket) {
            $ticket->delete();
            $this->toast()->success('Ticket has been deleted')->send();
        } else {
            $this->toast()->error('Ticket not found')->send();
        }
        $this->showTickets();
    }
    public function checkQr($qrCode)
    {
        $ticket = PurchasedTicket::where('qr_code', $qrCode)->first();
        if ($ticket) {
            $ticket->update(['is_verified' => true]);
            $this->toast()->success('Ticket Verified Successfully')->send();
            $this->showPurchasedTickets();
            $this->dispatch('close-thing');
        } else {
            $this->toast()->error('QR Code Not Found')->send();
            $this->dispatch('close-thing');
        }
    }
    public function blacklistUser($ticketId)
    {
        $ticket = PurchasedTicket::find($ticketId);
        if (!$ticket) {
            session()->flash('error', 'Ticket not found.');
            return;
        }

        // Check if the user is already blacklisted
        if ($ticket->is_blacklisted) {
            // Unblacklist the user if already blacklisted
            PurchasedTicket::where('id', $ticket->id)
                ->update(['is_blacklisted' => false]);
            $this->toast()->success('User is unblacklisted')->send();
        } else {
            // Blacklist the user if not blacklisted
            PurchasedTicket::where('id', $ticket->id)
                ->update(['is_blacklisted' => true]);
            $this->toast()->success('User is blacklisted')->send();
        }

        // Refresh the purchased tickets list
        $this->showPurchasedTickets();
    }
}
