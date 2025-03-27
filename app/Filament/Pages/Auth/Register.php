<?php

namespace App\Filament\Pages\Auth;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Pages\Page;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use App\Models\RegistrationPayments;
use App\Settings\RegistrationSettings;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        
        $registrationPaymentAmountDue = app(RegistrationSettings::class)->payment_amount_due;

        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Grid::make(['default' => 1, 'md' => 2])->schema([
                            TextInput::make('first_name')
                                ->label('First Name')
                                ->required(),
                            TextInput::make('last_name')
                                ->label('Last Name')
                                ->required(),
                            $this->getEmailFormComponent()->columnSpanFull(),
                            $this->getPasswordFormComponent()->columnSpanFull(),
                            $this->getPasswordConfirmationFormComponent()->columnSpanFull(),
                            Placeholder::make('amount_due')
                                ->label('Amount Due')
                                ->content('PHP ' . $registrationPaymentAmountDue),
                            TextInput::make('payment_details')
                                ->label('Payment Details')
                                ->columnSpanFull()
                                ->required(),
                        ]),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
    
    /**
     * @param array<string, mixed> $data
     * @return Model
     */
    protected function handleRegistration(array $data): Model
    {
        // Extract payment data from the form data
        $paymentData = [
            'payment_details' => $data['payment_details'],
            'amount_due' => app(RegistrationSettings::class)->payment_amount_due,
        ];
        
        // Remove payment data from user registration data
        $userData = collect($data)
            ->except(['payment_proof_url', 'payment_details'])
            ->toArray();
        
        // Register the user with parent method (creates user)
        $user = parent::handleRegistration($userData);
        
        // Create the registration payment record linked to the user
        RegistrationPayments::create([
            'user_id' => $user->id,
            'payment_details' => $paymentData['payment_details'],
            'amount_due' => $paymentData['amount_due'],
        ]);
        
        return $user;
    }
}
