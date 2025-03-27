<?php

use function Livewire\Volt\{state, mount};
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;

state([
    'listId' => null,
    'leads' => [],
    'search' => '',
    'perPage' => 5,
    'page' => 1,
]);

mount(function ($listId) {
    $this->listId = $listId;
    $this->loadLeads();
});
$loadLeads = function () {
  // Start with user's assigned leads
  $query = Lead::forUser(Auth::id());
  
  // Apply search
  if (!empty($this->search)) {
    $query->where(function($q) {
      $q->where('first_name', 'ilike', '%' . $this->search . '%')
        ->orWhere('last_name', 'ilike', '%' . $this->search . '%')
        ->orWhere('email', 'ilike', '%' . $this->search . '%')
        ->orWhere('mobile_number', 'ilike', '%' . $this->search . '%');
    });
  }
  
  // Skip leads that are already associated with a KanTask
  $query->whereDoesntHave('kanTask');
  
  // Get paginated results
  $this->leads = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage, ['*'], 'page', $this->page)
            ->toArray();
};

$nextPage = function () {
    if ($this->page < $this->leads['last_page']) {
        $this->page++;
        $this->loadLeads();
    }
};

$prevPage = function () {
    if ($this->page > 1) {
        $this->page--;
        $this->loadLeads();
    }
};

$selectLead = function ($leadId) {
    $lead = Lead::find($leadId);
    if ($lead) {
        $this->dispatch('lead-selected', $lead->toArray(), $this->listId);
    }
};

$closeModal = function () {
    $this->dispatch('close-lead-modal');
};

// Search leads
$searchLeads = function () {
    $this->page = 1;
    $this->loadLeads();
};

?>

<dialog id="lead-form-modal" class="modal" open>
  <div class="modal-box max-w-3xl">
    <h3 class="text-lg font-bold mb-4">Select a Lead</h3>

    <!-- Search bar -->
    <label class="input input-bordered  flex items-center">
      <input type="text" placeholder="Search leads..." class="grow border-none" wire:model="search"
        wire:keydown.enter="searchLeads" />
      <button class="" wire:click="searchLeads">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </button>
    </label>

    <!-- Lead list -->
    <div class="overflow-x-auto mb-4">
      <table class="table w-full">
        <thead>
          <tr>
            <th>Name</th>
            <th>Contact</th>
            <th>Insurance Type</th>
            <th>Created</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($leads['data'] ?? [] as $lead)
          <tr>
            <td>{{ $lead['first_name'] }} {{ $lead['last_name'] }}</td>
            <td>
              <div>{{ $lead['email'] }}</div>
              <div class="text-sm opacity-70">{{ $lead['mobile_number'] }}</div>
            </td>
            <td>{{ $lead['insurance_type'] }}</td>
            <td>{{ \Carbon\Carbon::parse($lead['created_at'])->format('M d, Y') }}</td>
            <td>
              <button class="btn btn-sm btn-primary" wire:click="selectLead({{ $lead['id'] }})">
                Select
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center py-4">No leads found</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if(isset($leads['total']) && $leads['total'] > 0)
    <div class="flex justify-between items-center">
      <span>Showing {{ ($leads['current_page']-1) * $leads['per_page'] + 1 }} to
        {{ min($leads['current_page'] * $leads['per_page'], $leads['total']) }}
        of {{ $leads['total'] }} leads
      </span>
      <div class="join">
        <button class="join-item btn" wire:click="prevPage" {{ $leads['current_page'] <=1 ? 'disabled' : ''
          }}>«</button>
        <button class="join-item btn">Page {{ $leads['current_page'] }}</button>
        <button class="join-item btn" wire:click="nextPage" {{ $leads['current_page']>= $leads['last_page'] ? 'disabled'
          : '' }}>»</button>
      </div>
    </div>
    @endif

    <div class="modal-action">
      <button class="btn" wire:click="closeModal">Close</button>
    </div>
  </div>

  <!-- Backdrop to close on click outside -->
  <form method="dialog" class="modal-backdrop">
    <button wire:click="closeModal">close</button>
  </form>
</dialog>
