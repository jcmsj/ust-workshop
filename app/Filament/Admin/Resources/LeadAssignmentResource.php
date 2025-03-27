<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LeadAssignmentResource\Pages;
use App\Filament\Resources\LeadAssignmentResource\RelationManagers;
use App\Models\LeadAssignment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

use function App\Helpers\searchableNameField;
class LeadAssignmentResource extends Resource
{
    protected static ?string $model = LeadAssignment::class;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'phosphor-target';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                searchableNameField()
                ->visible(fn() => auth()->user()->is_admin)
                ->relationship('userWithReserves', 'name')
                ->preload()
                ->required(),
                Forms\Components\Select::make('lead_id')
                    ->relationship('lead', 'first_name')
                    ->searchable(['first_name', 'last_name'])
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        return $record->name;
                    })
                    ->preload()
                    ->required(),
                Forms\Components\Radio::make('status')
                    ->options([
                        LeadAssignment::STATUS_TO_CALL => 'To Call',
                        LeadAssignment::STATUS_SUCCESS => 'Success',
                        LeadAssignment::STATUS_FAILED => 'Failed',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name'])
                    ->visible(fn() => Auth::user()->is_admin),
                Tables\Columns\TextColumn::make('lead.name')
                    ->sortable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'success' => 'success',
                        'to call' => 'warning',
                        'failed' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Assigned on')
                    ->getStateUsing(fn($record) => $record->created_at->diffForHumans())
                    ->dateTimeTooltip()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        LeadAssignment::STATUS_TO_CALL => 'To Call',
                        LeadAssignment::STATUS_SUCCESS => 'Success',
                        LeadAssignment::STATUS_FAILED => 'Failed',
                    ]),
            ])
            ->actions([
                // view lead
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                if (!auth()->user()->is_admin) {
                    $query->latest();
                }
            });
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!auth()->user()->is_admin) {
            $query->where('user_id', auth()->id());
        }

        return $query;
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
            'index' => Pages\ListLeadAssignment::route('/'),
            'create' => Pages\CreateLeadAssignment::route('/create'),
            'edit' => Pages\EditLeadAssignment::route('/{record}/edit'),
        ];
    }
}
