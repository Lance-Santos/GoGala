<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\OrganizationUser;
use Illuminate\Support\Facades\Auth;
use TallStackUi\Traits\Interactions;

class Login extends Component
{
    use Interactions;
    /** @var string */
    public $email = '';

    /** @var string */
    public $password = '';

    /** @var bool */
    public $remember = false;

    protected $rules = [
        'email' => ['required', 'email'],
        'password' => ['required'],
    ];

    /**
     * The function authenticate() validates user credentials and logs in the user if authentication is
     * successful.
     *
     *  `authenticate` function is returning a redirect response to the intended route after
     * a successful authentication. If the authentication attempt fails, it adds an error message for
     * the email field and does not redirect.
     */
    public function authenticate()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            if (session()->has('invitation_token')) {
                $organizationId = session('organization_id');
                $role = session('role');
                $user = Auth::user();
                $this->addUserToOrganization($user->id, $organizationId, $role);
                $this->toast()->success('Successfully joined organization')->send();
                return redirect()->route('organization.view', $organizationId);
            }

            $this->toast()->success('Logged in', 'You are logged in.')->flash()->send();
            return redirect()->intended(route('home'));
        } else {
            $this->toast()->success('Login failed, check your credentials')->send();
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
        return view('livewire.auth.login')->extends('layouts.auth');
    }
}
