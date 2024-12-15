<div class="container px-4 mx-auto my-8 space-y-8 lg:px-8">
    <x-slide id="slide-edit" size="full">
        <x-slot:title>
            <div class="flex justify-end">
                <x-button class="w-full" wire:click='updateProfileInformation'>{{ __('Save Changes') }}</x-button>
            </div>
        </x-slot:title>
        <div class="flex-1 overflow-y-auto py-8 max-h-[75vh] p-6 space-y-6 border rounded">
            <x-card class="my-8">
                <x-slot:header>
                    Change Profile Info
                </x-slot:header>
                <div class="m-10">
                    @if (session()->has('message'))
                        <x-alert title="Profile Updated" icon="check-circle" class="mt-6" color="green">
                            {{ session('message') }}
                        </x-alert>
                        <br>
                    @endif
                    <form wire:submit.prevent="updateProfileInformation" class="space-y-6">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input :label="__('First Name')" wire:model="first_name" type="text" required class="w-full" />
                            <x-input :label="__('Last Name')" wire:model="last_name" type="text" required class="w-full" />
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input :label="__('Middle Name')" wire:model="middle_name" type="text" class="w-full" />
                            <x-input :label="__('Suffix')" wire:model="suffix" type="text" class="w-full" />
                        </div>

                        {{-- Username and Email --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input :label="__('Username')" wire:model="username" type="text" required class="w-full" />
                            <x-input :label="__('Email')" wire:model="email" type="email" required class="w-full" />
                        </div>

                        {{-- Gender and Contact Number --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input :label="__('Gender')" wire:model="gender" type="text" class="w-full" />
                            <x-input :label="__('Contact Number')" wire:model="contact_number" type="text" class="w-full" />
                        </div>

                        {{-- Bio --}}
                        <x-textarea :label="__('Bio')" wire:model="bio" class="w-full" rows="3" />
                        {{-- Upload Profile and Banner Images --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-upload label="Profile Image" wire:model="profile_img"
                                hint="Drag and drop your profile image here" />
                            <x-upload label="Banner Image" wire:model="banner_img"
                                hint="Drag and drop your banner image here" />
                        </div>
                    </form>
                </div>
            </x-card>
            <x-card class="my-8">
                <x-slot:header>
                    Change Your Password
                </x-slot:header>
                <div class="m-10">
                    @if (session()->has('password_message'))
                        <x-alert title="Password Updated" icon="check-circle" class="mt-6" color="green">
                            {{ session('password_message') }}
                        </x-alert>
                        <hr>
                    @endif

                    <form wire:submit.prevent="updatePassword" class="space-y-6">
                        {{-- Current Password --}}
                        <x-input :label="__('Current Password')" wire:model="current_password" type="password" required
                            class="w-full" />

                        {{-- New Password (Password Generator) --}}
                        <x-password generator :rules="['min:8', 'symbols', 'numbers', 'mixed']" wire:model="password" class="w-full" />

                        {{-- Confirm New Password --}}
                        <x-input :label="__('Confirm New Password')" wire:model="password_confirmation" type="password" required
                            class="w-full" />

                        {{-- Save Button --}}
                        <x-button class="w-full" wire:click='updatePassword'>{{ __('Update Password') }}</x-button>
                    </form>
                </div>
            </x-card>
        </div>
        <x-slot:footer end>
            <x-button text="Close" x-on:click="$slideClose('slide-edit')" />
        </x-slot:footer>
    </x-slide>
    <div class="border rounded p-5">
        <div class="relative">
        @if (Auth::user()->banner_img_url)
            <img src="{{ Storage::url(Auth::user()->banner_img_url) }}" alt="Banner"
                class="object-cover w-full h-60 lg:h-80 rounded-xl">
        @else
            <div class="flex items-center justify-center w-full h-60 lg:h-80 bg-slate-200 rounded-xl">
                <x-avatar color="slate" class="w-20 h-20" />
            </div>
        @endif
        <div class="absolute bottom-[-30px] left-6 lg:left-10">
            @if (Auth::user()->profile_img_url)
                <x-avatar image="{{ Storage::url(Auth::user()->profile_img_url) }}"
                    class="w-24 h-24 border-4 border-white rounded-full" />
            @else
                <x-avatar color="slate" class="w-24 h-24 border-4 border-white rounded-full" />
            @endif
        </div>
    </div>

    <div class=" mt-16 space-y-4 lg:pl-10">
        <h1 class="text-4xl font-bold">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h1>
        <p class="text-lg text-gray-600">@ {{ Auth::user()->username }}</p>
        <p class="mt-2 text-gray-800">{{ Auth::user()->bio }}</p>
        <div class="flex items-center px-4 py-2 text-base rounded-lg shadow-sm bg-slate-100 justify-between">
            <x-button text="Edit Profile" x-on:click="$slideOpen('slide-edit')" />
            <x-clipboard text="{{ url('/profile/' . Auth::user()->username) }}" icon="clipboard"/>
        </div>

    </div>
    </div>


    {{-- Tabs for User Sections --}}
    <div class="pt-6 mt-10 border-t">
        @if (Auth::user()->role_id === 2)
            <x-tab selected="Your Organizations">
                <x-tab.items tab="Your Organizations">
                    <x-slot:left>
                        <x-icon name="building-office" class="w-6 h-6" />
                    </x-slot:left>

                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Your Organizations</h2>
                        <x-button text="Create Organization" href="{{ route('create-organization') }}" />
                    </div>
                    <x-table :headers="$headers" :rows="$rows" filter loading
                        link="organization/{organization_slug}" :quantity="[5,10,30]" />

                </x-tab.items>

                <x-tab.items tab="Your Events">
                    <x-slot:left>
                        <x-icon name="calendar" class="w-6 h-6" />
                    </x-slot:left>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Your Events</h2>
                        <x-button text="Create Event" />
                    </div>

                    <x-table :headers="$eventHeaders" :rows="$eventRows" :filter="['quantity' => 5, 'search' => $eventSearch]" loading
                        link="organization/{organization_slug}/{event_slug}" :quantity="[5,10,30]"/>

                </x-tab.items>
            </x-tab>
        @endif
    </div>
</div>
