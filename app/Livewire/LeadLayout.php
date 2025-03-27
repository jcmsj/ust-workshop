<?php

namespace App\Livewire;

use App\Models\Lead;
use Livewire\Component;
use Illuminate\Database\Eloquent\Model;

class LeadLayout extends Component
{
    public Model $record;
    public $keyToHeaders = [
        // 'first_name' => 'First Name',
        // 'last_name' => 'Last Name',
        'name' => "Name",
        'insurance_type' => 'Insurance Quote Type',
        'birthdate' => 'Birthdate',
        'province_territory' => 'Province/Territory',
        'sex' => 'Sex',
        'desired_amount' => 'Desired Amount ($)',
        'length_coverage' => 'Length Coverage',
        'mortgage_amortization' => 'Mortgage Amortization',
        'length_payment' => 'Length Payment',
        'health_class' => 'Health Class',
        'tobacco_use' => 'Tobacco Use',
        'journey' => 'Journey',
        'mobile_number' => 'Mobile Number',
        'email' => 'Email',
        'created_at' => 'Requested on',
        'status' => 'Status',
    ];

    public function mount(Model $record): void
    {
        $this->record = $record;
    }

    public function render()
    {
        return view('livewire.lead-layout');
    }
}
