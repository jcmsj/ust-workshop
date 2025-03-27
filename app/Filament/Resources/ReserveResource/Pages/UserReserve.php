<?php

namespace App\Filament\Resources\ReserveResource\Pages;

use App\Filament\Resources\ReservesResource;
use App\Models\Reserve;
use App\Models\ReserveRequest;
use App\Settings\LeadSettings;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Auth;

class UserReserve extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = ReservesResource::class;
    protected static string $view = 'filament.resources.reserves-resource.pages.user-reserve';
    public ?Reserve $reserve = null;
    public ?ReserveRequest $pendingRequest = null;
    public array $data = [];
    public function mount(): void
    {
        $this->reserve = Auth::user()->reserve;
        $this->data = [
            'count' => $this->reserve->count,
            'status' => $this->reserve->status,
            'new_reserve_amount' => 1,
            'total_cost' => app(LeadSettings::class)->cost_per_lead, // Initialize total cost
        ];
        $this->pendingRequest = ReserveRequest::getPendingRequest(auth()->id());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Grid::make([
                            'default' => 2,
                            'lg' => 1,
                        ])
                            ->columnSpan(2)
                            ->schema([
                                Section::make('Reserves remaining')->label('')
                                    ->schema([
                                        Placeholder::make('count')
                                            ->label('')
                                            ->content(new HtmlString(
                                                "<div class='text-2xl text-center'>{$this->reserve->count}</div>"
                                            )),
                                    ])->columnSpan(1),
                                Section::make('')
                                    ->schema([
                                        Radio::make('status')
                                            ->label('Accept incoming leads?')
                                            ->options([
                                                'accept' => 'Accepting',
                                                'pause' => 'Paused',
                                            ])
                                            ->live()
                                            ->afterStateUpdated(fn() => $this->save()),
                                    ])->columnSpan(1),
                            ])->columnSpan(1),
                        $this->pendingRequest
                            ? Section::make('Pending Request')
                            ->description('You have a pending request for more reserves.')
                            ->schema([
                                Placeholder::make('pending_amount')
                                    ->label('Requested Amount')
                                    ->content($this->pendingRequest->count),
                                Placeholder::make('pending_status')
                                    ->label('Status')
                                    ->content('Pending Review'),
                                Placeholder::make('payment_details')
                                    ->label('Payment Details')
                                    ->content($this->pendingRequest->payment_details ?? 'No payment details provided'),
                            ])
                            ->columnSpan(2)
                            ->extraAttributes(['class' => 'relative'])
                            ->headerActions([
                                Action::make('cancel')
                                    ->color('danger')
                                    ->icon('heroicon-o-x-mark')
                                    ->action('cancelRequest')
                                    ->requiresConfirmation()
                            ])
                            : Section::make('New Reserve Request')
                            ->schema([
                                Grid::make(4)->schema([
                                    Grid::make(1)->schema([
                                        TextInput::make('new_reserve_amount')
                                            ->label('Amount of reserves')
                                            ->numeric()
                                            ->minValue(1)
                                            ->live()
                                            ->afterStateUpdated(fn($state) => $this->data['total_cost'] = $state * app(LeadSettings::class)->cost_per_lead),
                                        Placeholder::make('cost_per_lead')
                                            ->label('Cost per lead')
                                            ->content(fn() => "CA$" . app(LeadSettings::class)->cost_per_lead),
                                        Placeholder::make('total_cost')
                                            ->label('Total Cost')
                                            ->content(fn() => "CA$" . ($this->data['total_cost'] ?? 0)),
                                    ])->columnSpan(1),
                                    Grid::make(1)->schema([
                                        Textarea::make('payment_details')
                                            ->label('Payment Details')
                                            ->placeholder('Enter payment reference number or other details'),
                                    ])->columnSpan(3)
                                ]),
                            ])
                            ->columnSpan(2)
                            ->extraAttributes(['class' => 'relative'])
                            ->footerActions([
                                Action::make('submit')
                                    ->label('Submit Request')
                                    ->action(fn() => $this->requestReserves($this->form->getState()))
                            ])
                    ]),
            ])->statePath('data');
    }

    protected function requestReserves($data): void
    {
        if (ReserveRequest::hasPendingRequest(auth()->id())) {
            Notification::make()
                ->warning()
                ->title('You already have a pending request')
                ->send();
            return;
        }

        auth()->user()->reserveRequests()->create([
            'count' => $data['new_reserve_amount'],
            'status' => ReserveRequest::STATUS_PENDING,
            'payment_proof_url' => $data['payment_proof_url'],
            'payment_details' => $data['payment_details'],
            'cost_per_lead' => app(LeadSettings::class)->cost_per_lead,
        ]);

        Notification::make()
            ->success()
            ->title('Reserve request submitted successfully')
            ->send();

        $this->redirect(request()->header('Referer'));
    }

    public function cancelRequest(): void
    {
        $this->pendingRequest->reject();
        $this->pendingRequest = null;

        Notification::make()
            ->success()
            ->title('Request cancelled successfully')
            ->send();
    }

    public function save(): void
    {
        $this->reserve->status = $this->data['status'];
        $this->reserve->save();

        Notification::make()->success()
            ->title('Reserve status updated successfully')
            ->send();
    }
}
