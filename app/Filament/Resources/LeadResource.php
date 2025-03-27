<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;
    protected static ?string $navigationIcon = 'phosphor-target';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Insurance Info')
                        ->schema([
                            Select::make('insurance_type')
                                ->label('Life Insurance Quotes')
                                ->options([
                                    'Term Life Insurance' => 'Term Life Insurance (popular)',
                                    'Mortgage Protection Insurance' => 'Mortgage Protection Insurance',
                                    'Whole Life Insurance' => 'Whole Life Insurance',
                                    'Universal Life Insurance' => 'Universal Life Insurance',
                                    'Term to 100 Life Insurance' => 'Term to 100 Life Insurance',
                                    'No Medical Exam Life Insurance' => 'No Medical Exam Life Insurance',
                                    'Guaranteed Issue Life Insurance' => 'Guaranteed Issue Life Insurance',
                                    'Hard to Insure Life Insurance' => 'Hard to Insure Life Insurance',
                                    'Final Expense Insurance' => 'Final Expense Insurance',
                                    'Life Insurance on Children' => 'Life Insurance on Children',
                                    'Life Insurance on Seniors' => 'Life Insurance on Seniors',
                                    'Key Person Life Insurance' => 'Key Person Life Insurance',
                                    'Corporate-Owned Insurance' => 'Corporate-Owned Insurance',
                                    'Shareholder/Partner Insurance' => 'Shareholder/Partner Insurance',
                                    'Buy/Sell Agreement Insurance' => 'Buy/Sell Agreement Insurance',
                                    'Whole Life for Business Owners' => 'Whole Life for Business Owners',
                                    'Whole Life for High Net Worth' => 'Whole Life for High Net Worth',
                                    'Whole Life for Estate Planning' => 'Whole Life for Estate Planning'
                                ])
                                ->searchable()
                                ->required(),
                            Select::make('province_territory')
                                ->label('Province/Territory')
                                ->options([
                                    'Alberta' => 'Alberta',
                                    'British Columbia' => 'British Columbia',
                                    'Manitoba' => 'Manitoba',
                                    'New Brunswick' => 'New Brunswick',
                                    'Newfoundland' => 'Newfoundland',
                                    'Northwest Territories' => 'Northwest Territories',
                                    'Nova Scotia' => 'Nova Scotia',
                                    'Nunavut' => 'Nunavut',
                                    'Ontario' => 'Ontario',
                                    'Prince Edward Island' => 'Prince Edward Island',
                                    'Quebec' => 'Quebec',
                                    'Saskatchewan' => 'Saskatchewan',
                                    'Yukon' => 'Yukon',
                                ])
                                ->searchable()
                                ->required(),
                        ]),
                    Step::make('Personal Info')
                        ->schema([
                            DatePicker::make('birthdate')
                                ->label('Birth date')
                                ->required()
                                ->displayFormat('F j, Y')
                                ->maxDate('today'),
                            Radio::make('sex')
                                ->label('Sex')
                                ->options(['Male' => 'Male', 'Female' => 'Female'])
                                ->required(),
                            Select::make('desired_amount')
                                ->label('Desired Amount')
                                ->options([
                                    50000 => '$50,000',
                                    100000 => '$100,000',
                                    150000 => '$150,000',
                                    200000 => '$200,000',
                                    250000 => '$250,000',
                                    300000 => '$300,000',
                                    400000 => '$400,000',
                                    500000 => '$500,000',
                                    600000 => '$600,000',
                                    700000 => '$700,000',
                                    800000 => '$800,000',
                                    900000 => '$900,000',
                                    1000000 => '$1,000,000',
                                    1250000 => '$1,250,000'
                                ])
                                ->required()
                                ->visible(fn($get) => !in_array($get('insurance_type'), [
                                    'Corporate-Owned Insurance',
                                    'Shareholder/Partner Insurance',
                                    'Buy/Sell Agreement Insurance'
                                ])),
                            Select::make('length_coverage')
                                ->label('Length of Coverage')
                                ->options([
                                    '10' => '10 years',
                                    '15' => '15 years',
                                    '20' => '20 years',
                                    '25' => '25 years',
                                    '30' => '30 years'
                                ])
                                ->required()
                                ->visible(fn($get) => $get('insurance_type') === 'Term Life Insurance'),
                            Select::make('mortgage_amortization')
                                ->label('Mortgage Amortization')
                                ->options([
                                    '10' => '10 years or less',
                                    '15' => '15 years or less',
                                    '20' => '20 years or less',
                                    '25' => '25 years or less',
                                    '30' => '30 years or less',
                                    '35' => '35 years or greater'
                                ])
                                ->visible(fn($get) => $get('insurance_type') === 'Mortgage Protection Insurance'),
                            Select::make('length_payment')
                                ->label('Length of Payment')
                                ->options([
                                    '10 years' => '10 years',
                                    '15 years' => '15 years',
                                    '20 years' => '20 years',
                                    'Pay to age 65' => 'Pay to age 65',
                                    'Life Pay' => 'Life Pay'
                                ])
                                ->visible(fn($get) => in_array($get('insurance_type'), [
                                    'Whole Life Insurance',
                                    'Universal Life Insurance',
                                    'No Medical Exam Life Insurance',
                                    'Guaranteed Issue Life Insurance',
                                    'Hard to Insure Life Insurance',
                                    'Life Insurance on Children',
                                    'Key Person Life Insurance'
                                ])),
                            Radio::make('health_class')
                                ->label('Health class')
                                ->options(['Average' => 'Average', 'Good' => 'Good', 'Excellent'])
                                ->visible(fn($get) => !in_array($get('insurance_type'), [
                                    'Guaranteed Issue Life Insurance',
                                    'No Medical Exam Life Insurance'
                                ])),
                            Radio::make('tobacco_use')
                                ->label('Nicotine/Tobaco use')
                                ->options([
                                    false => 'No, I do not smoke',
                                    true => 'Yes, I am a smoker'
                                ])
                                ->required(),
                            Select::make('journey')
                                ->label('Your Journey')
                                ->options([
                                    'Still deciding if I need insurance' => 'Still deciding if I need insurance',
                                    'Doing research to find quotes' => 'Doing research to find quotes',
                                    'Ready to get covered soon' => 'Ready to get covered soon',
                                    'Want to get covered right away' => 'Want to get covered right away',
                                ])
                                ->required(),
                        ]),
                    Step::make('Contact Info')
                        ->schema([
                            TextInput::make('first_name')->label('First Name')->required(),
                            TextInput::make('last_name')->label('Last Name')->required(),
                            TextInput::make('mobile_number')
                                ->label('Mobile Number')
                                ->tel()
                                ->telRegex('/^(\+?1\s?)?\(?([0-9]{3})\)?[-.\s]?([0-9]{3})[-.\s]?([0-9]{4})$/')
                                ->required(),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required(),
                        ]),
                ])
                    ->submitAction(new HtmlString(Blade::render(
                        <<<BLADE
                    <x-filament::button
                        type="submit"
                        size="sm"
                    >
                        Submit
                    </x-filament::button>
                    BLADE
                    ))),
                Placeholder::make("Agreement")->hiddenLabel()->content(new HtmlString(Blade::render(
                    <<<BLADE
                        <div class="mt-4 text-sm text-justify text-gray-600 max-w-lg">
                        By clicking "Submit Quote" you grant "Hip&Valley Financial Solutions" expressed written consent that we may contact
                        you to discuss your insurance options. This does not constitute an insurance application. You are under no obligation to purchase a policy. We respect your privacy, and the information provided will never be shared
                        with anyone.
                        </div>
                    BLADE
                ))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Lead name')
                    ->sortable(['first_name', 'last_name'])
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('insurance_type')
                    ->label('Insurance Quote Type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('latestAssignment.user.name')
                    ->label('Assigned To')
                    ->sortable(['first_name', 'last_name'])
                    ->visible(fn (Table $table): bool => ($table->getFilter('assignment_status')?->getState()['value'] ?? null) !== 'unassigned'),

                Tables\Columns\TextColumn::make('latestAssignment.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'success' => 'success',
                        'to call' => 'warning',
                        'failed' => 'danger',
                        'Unassigned' => 'gray',
                    })
                    ->sortable()
                    ->default('Unassigned'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested on')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('assignment_status')
                    ->options(function() {
                        $options = [
                            'to call' => 'To Call',
                            'success' => 'Success',
                            'failed' => 'Failed',
                        ];
                        
                        if (auth()->user()->is_admin) {
                            $options = ['unassigned' => 'Unassigned'] + $options;
                        }
                        
                        return $options;
                    })
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value']) {
                            'unassigned' => $query->doesntHave('leadAssignments'),
                            'to call', 'success', 'failed' => $query->whereHas('latestAssignment', fn ($q) => 
                                $q->where('status', $data['value'])
                            ),
                            default => $query
                        };
                    })
            ])
            ->actions([
                Action::make('assign_handler')
                    ->label('Assign')
                    ->color('success')
                    ->icon('heroicon-m-user-plus')
                    ->url(fn (Lead $record): string => route('filament.admin.resources.lead-assignments.create', ['lead_id' => $record->id]))
                    ->visible(fn (Lead $record): bool => $record->leadAssignments->isEmpty()),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return (new Pages\ViewLead())->infolist($infolist);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'view' => Pages\ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (!auth()->user()->is_admin) {
            $query->forUser(auth()->id());
        }

        return $query;
    }
}
