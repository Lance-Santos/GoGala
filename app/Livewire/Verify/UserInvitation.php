<?php

namespace App\Livewire\Verify;

use Livewire\Component;
use App\Models\Organization;
use App\Models\OrganizationUser;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use App\Models\UserInvitation as ModelsUserInvitation;
use TallStackUi\Traits\Interactions;

class UserInvitation extends Component
{
    use Interactions;
    public $token;

    public function mount($token)
    {
        $this->token = $token;
        $invitation = ModelsUserInvitation::where('token', $token)->firstOrFail();
        if (Auth::check()) {
            $user = Auth::user();
            $this->addUserToOrganization($user->id, $invitation->organization_id, $invitation->role);
            $organization = Organization::find($invitation->organization_id);
            $this->toast()->success('Added')->flash()->send();
            return redirect()->route('organization-profile',['organization_slug' => $organization->organization_slug]);
        } else {
            session(['invitation_token' => $token, 'organization_id' => $invitation->organization_id, 'role' => $invitation->role]);

            $user = User::where('email', $invitation->email)->first();
            if ($user) {
                return redirect()->route('login');
            } else {
                return redirect()->route('register', ['email' => $invitation->email]);
            }
        }
    }

    protected function addUserToOrganization($userId, $organizationId, $role)
    {
        OrganizationUser::create([
            'organization_id' => $organizationId,
            'user_id' => $userId,
            'role' => $role,
        ]);
    }
    public function render()
    {
        return view('livewire.verify.user-invitation')->extends('layouts.base');
    }
}
