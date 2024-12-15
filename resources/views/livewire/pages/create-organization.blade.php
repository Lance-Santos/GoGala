<div class="max-w-4xl p-8 mx-auto bg-white rounded-lg shadow-lg">
    <h2 class="mb-8 text-4xl font-bold text-center text-gray-800">{{ __('Create Organization') }}</h2>
    <form wire:submit.prevent="createOrganization" class="space-y-8">
        <!-- Organization Name and Slug Row -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <x-input label="Organization Name *" hint="Enter your organization name" wire:model="organization_name" required />
            <x-input label="Organization Handle *" hint="Unique identifier (e.g., @org)" wire:model="organization_slug" required />
        </div>

        <!-- Organization Bio/Description -->
        <div class="mt-4">
            <x-textarea label="Organization Bio/Description *" hint="Describe your organization (max 250 characters)" maxlength="250" count wire:model="organization_bio" required />
        </div>

        <!-- Contact Email -->
        <div class="mt-4">
            <x-input label="Organization Email *" hint="Contact email for your organization" wire:model="organization_email" type="email" required />
        </div>

        <!-- Image Uploads for Profile and Background -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <x-upload label="Profile Image" hint="Upload a profile image" tip="Drag and drop your profile image here or click to select" wire:model="img_url_profile" />
            <x-upload label="Background Image" hint="Upload a background image" tip="Drag and drop your background image here or click to select" wire:model="img_url_background" />
        </div>

        <!-- Submit Button Row -->
        <div class="flex items-center justify-between mt-8">
            <x-link href="{{ route('profile') }}" text="Back to Dashboard" class="text-sm text-gray-600 hover:text-gray-800" />
            <x-button wire:click="createOrganization" loading class="px-4 py-2 font-semibold text-white transition duration-200 bg-green-600 rounded hover:bg-green-700">
                Create Organization
            </x-button>
        </div>
    </form>
</div>
