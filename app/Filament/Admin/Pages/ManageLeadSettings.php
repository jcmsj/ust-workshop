<?php

namespace App\Filament\Admin\Pages;

use App\Settings\LeadSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageLeadSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static string $settings = LeadSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cost_per_lead')
                    ->numeric()
                    ->prefix('CA$')
                    ->minValue(0)
                    ->required(),
            ]);
    }
}
