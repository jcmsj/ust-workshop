<x-filament-panels::page>
  <div class="flex items-center justify-center">
    <div class="card bg-base-100 dark:bg-black w-max shadow-xl">
      <div class="card-body">
        <h2 class="card-title">Account Pending Approval</h2>
        <div class="mb-4">
          <svg class='mx-auto h-20 w-20 text-warning' xmlns="http://www.w3.org/2000/svg" width="32" height="32"
            viewBox="0 0 24 24">
            <path fill="currentColor"
              d="M1 21L12 2l11 19zm3.45-2h15.1L12 6zM12 18q.425 0 .713-.288T13 17t-.288-.712T12 16t-.712.288T11 17t.288.713T12 18m-1-3h2v-5h-2zm1-2.5" />
          </svg>
        </div>
        <p class="mb-4 text-lg">
          Your account is pending approval from an administrator. <br>
          You'll be notified via email once your account has been approved.
        </p>
        {{-- <p class="text-sm">
          If you have any questions, please contact our support team.
        </p> --}}
      </div>
    </div>
</x-filament-panels::page>
