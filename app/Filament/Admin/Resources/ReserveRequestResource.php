<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReserveRequestResource\Pages;
use App\Models\ReserveRequest;
use App\Settings\LeadSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

use function App\Helpers\searchableNameField;

class ReserveRequestResource extends Resource
{
    protected static ?string $model = ReserveRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $modelLabel = "Reserve Requests";
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                searchableNameField()
                    ->required(),
                Forms\Components\TextInput::make('count')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('cost_per_lead')
                    ->required()
                    // ->money('CAD')
                    ->numeric()
                    ->default(fn() => app(LeadSettings::class)->cost_per_lead)
                    ->minValue(0),
                Forms\Components\Select::make('status')
                    ->options([
                        ReserveRequest::STATUS_ACCEPTED => 'Accepted',
                        ReserveRequest::STATUS_PENDING => 'Pending',
                        ReserveRequest::STATUS_REJECTED => 'Rejected',
                        ReserveRequest::STATUS_CANCELLED => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('payment_details')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name'])
                    ->label('Requester'),
                Tables\Columns\TextColumn::make('count')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_per_lead')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_per_lead')
                    ->money('PH')
                    // column: total_cost = count * cost_per_lead
                    ->formatStateUsing(fn($state, $record) => DB::table('reserve_requests')
                        ->where('id', $record->id)
                        ->value('count') * $state)
                    ->label('Total Cost'),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'accepted' => 'success',
                        'pending' => 'warning',
                        'rejected', 'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('handledBy.name')
                    ->label('Handled By')
                    ->sortable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested on')
                    ->getStateUsing(fn($record) => $record->created_at->diffForHumans())
                    ->dateTimeTooltip()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'accepted' => 'Accepted',
                        'pending' => 'Pending',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('Accept')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn(ReserveRequest $record) => $record->status === 'pending')
                    ->action(fn(ReserveRequest $record) => $record->accept()),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn(ReserveRequest $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn(ReserveRequest $record) => $record->reject()),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('acceptMultiple')
                        ->label('Accept Selected')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->action(fn(Collection $records) => $records->each->accept()),
                ]),
            ]);
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
            'index' => Pages\ListReserveRequests::route('/'),
            'create' => Pages\CreateReserveRequest::route('/create'),
            'edit' => Pages\EditReserveRequest::route('/{record}/edit'),
        ];
    }
}
