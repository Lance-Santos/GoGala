<div class="flex items-center justify-center min-h-screen bg-gray-100 p-4">
  <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-6 text-center">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">You're Invited!</h1>
    <p class="text-gray-600 mb-6">
      You have been invited to join an organization. Click the button below to accept the invitation:
    </p>
    <a href="{{ url('/invitations/accept/'.$token) }}" class="inline-block w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
      Accept Invitation
    </a>
  </div>
</div>
