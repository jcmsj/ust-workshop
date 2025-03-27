<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Lead;

class StoreLeadRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'tobacco_use' => filter_var($this->tobacco_use, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'life_insurance_quotes' => 'required|string',
            'province_territory' => 'required|string',
            'birthdate' => 'required|date',
            'sex' => 'required|string|in:Male,Female',
            'desired_amount' => 'nullable|integer',
            'length_coverage' => 'nullable|string',
            'mortgage_amortization' => 'nullable|integer',
            'length_payment' => 'nullable|string',
            'health_class' => 'nullable|string|in:Average,Good,Excellent',
            'tobacco_use' => 'required|boolean',
            'journey' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'mobile_number' => 'required|string',
            'email' => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            'life_insurance_quotes.required' => 'Please select a type of life insurance.',
            'province_territory.required' => 'Please select your province or territory.',
            'birthdate.required' => 'Please enter your birth date.',
            'birthdate.date' => 'The birth date must be a valid date.',
            'sex.required' => 'Please select your sex.',
            'sex.in' => 'The selected sex is invalid.',
            'desired_amount.integer' => 'The desired amount must be a number.',
            'length_coverage.string' => 'The length of coverage must be a string.',
            'mortgage_amortization.integer' => 'The mortgage amortization must be a number.',
            'length_payment.string' => 'The length of payment must be a string.',
            'health_class.in' => 'The selected health class is invalid.',
            'tobacco_use.required' => 'Please specify your tobacco use.',
            'tobacco_use.in' => 'The tobacco use field must be a boolean',
            'journey.required' => 'Please select your journey.',
            'first_name.required' => 'Please enter your first name.',
            'last_name.required' => 'Please enter your last name.',
            'mobile_number.required' => 'Please enter your mobile number.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'The email address must be a valid email.',
        ];
    }
}
