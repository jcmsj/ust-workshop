<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReserveRequestResource\Pages;
use App\Filament\Resources\ReserveRequestResource\RelationManagers;
use App\Filament\Resources\ReserveResource\Pages\UserReserve;
use App\Models\ReserveRequest;
use App\Settings\LeadSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function App\Helpers\searchableNameField;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\ImageEntry;

class ReserveRequestResource extends Resource
{
    protected static ?string $model = ReserveRequest::class;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'User';
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $modelLabel = "Reserve History";
    protected static ?string $pluralModelLabel = "Reserve History";

    public static function form(Form $form): Form
    {
        $isAdmin = auth()->user()->is_admin;
        $statusOptions = $isAdmin 
            ? [
                ReserveRequest::STATUS_ACCEPTED => 'Accepted',
                ReserveRequest::STATUS_PENDING => 'Pending',
                ReserveRequest::STATUS_REJECTED => 'Rejected',
                ReserveRequest::STATUS_CANCELLED => 'Cancelled',
            ]
            : [
                ReserveRequest::STATUS_PENDING => 'Pending',
                ReserveRequest::STATUS_REJECTED => 'Cancel',
            ];

        return $form
            ->schema([
                    searchableNameField()
                    ->visible(fn () => $isAdmin)
                    ->required(),
                Forms\Components\TextInput::make('count')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->disabled(!$isAdmin),
                Forms\Components\Select::make('status')
                    ->options($statusOptions)
                    ->required()
                    ->default(ReserveRequest::STATUS_PENDING)
                    ->visible(fn ($record) => 
                        $isAdmin || 
                        ($record && $record->status === ReserveRequest::STATUS_PENDING)
                    ),
                Forms\Components\TextInput::make('cost_per_lead')
                    ->required()
                    ->numeric()
                    ->default(fn () => app(LeadSettings::class)->cost_per_lead)
                    ->disabled()
                    ->visible(!$isAdmin),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name'])
                    ->visible(fn () => auth()->user()->is_admin),
                Tables\Columns\TextColumn::make('count')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'pending' => 'warning',
                        'rejected', 'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('handled_by')
                    ->label('Handled By')
                    ->sortable(['first_name', 'last_name'])
                    ->visible(fn () => auth()->user()->is_admin)
                    ->getStateUsing(fn (ReserveRequest $record) => $record->handledBy->name ?? 'N/A'),
                Tables\Columns\TextColumn::make('handled_at')
                    ->label('Handled At')
                    ->dateTime()
                    ->sortable()
                    ->visible(fn () => auth()->user()->is_admin),
                Tables\Columns\TextColumn::make('cost_per_lead')
                    ->money('CAD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_cost')
                    ->money('CAD'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'accepted' => 'Accepted',
                        'pending' => 'Pending',
                        'rejected' => 'Rejected/Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('Accept')
                    ->color('success')
                    ->visible(fn (ReserveRequest $record) => auth()->user()->is_admin && $record->status === 'pending')
                    ->action(fn (ReserveRequest $record) => $record->update(['status' => ReserveRequest::STATUS_ACCEPTED])),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (ReserveRequest $record) => auth()->user()->is_admin && $record->status === 'pending')
                    ->action(fn (ReserveRequest $record) => $record->update(['status' => ReserveRequest::STATUS_REJECTED])),
                Tables\Actions\EditAction::make()
                    ->visible(fn (ReserveRequest $record) => 
                        auth()->user()->is_admin || 
                        ($record->status === 'pending' && $record->user_id === auth()->id())
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->is_admin),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                if (!auth()->user()->is_admin) {
                    $query->latest();
                }
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('user.name')
                    ->visible(fn () => auth()->user()->is_admin)
                    ->label('Requested By'),
                TextEntry::make('count')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'pending' => 'warning',
                        'rejected', 'cancelled' => 'danger',
                    }),
                TextEntry::make('payment_details')
                    ->markdown()
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('handledBy.name')
                    ->label('Handled By')
                    ->visible(fn () => auth()->user()->is_admin),
                TextEntry::make('handled_at')
                    ->dateTime()
                    ->visible(fn () => auth()->user()->is_admin),
                TextEntry::make('cost_per_lead')
                    ->money('CAD'),
                TextEntry::make('total_cost')
                    ->money('CAD'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (!auth()->user()->is_admin) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        if (auth()->user()->is_admin) {
            return true;
        }

        return !ReserveRequest::hasPendingRequest(auth()->id());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        if (auth()->user()?->is_admin) {
            return [
                'index' => Pages\ListReserveRequest::route('/'),
                'create' => Pages\CreateReserveRequest::route('/create'),
                'edit' => Pages\EditReserveRequest::route('/{record}/edit'),
            ];
        }

        return [
            'index' => Pages\ListReserveRequest::route('/'),
            'create' => UserReserve::route('/create'),
            'view' => Pages\ViewReserveRequest::route('/{record}'),
        ];
    }
}
