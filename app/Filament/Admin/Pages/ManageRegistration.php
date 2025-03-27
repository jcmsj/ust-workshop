<?php

namespace App\Filament\Admin\Pages;

use App\Settings\RegistrationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageRegistration extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = RegistrationSettings::class;
    protected static ?string $navigationGroup = 'Settings';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // ...
                Forms\Components\TextInput::make('payment_amount_due')
                ->label('Registration Payment Amount Due')
                ->prefix('PHP')
                ->required()
                ->numeric(),
            ]);
    }
}
