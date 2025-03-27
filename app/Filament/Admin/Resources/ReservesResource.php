<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReservesResource\Pages;
use App\Filament\Resources\ReservesResource\RelationManagers;
use App\Models\Reserve;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager;
use function App\Helpers\searchableNameField;

class ReservesResource extends Resource
{
    protected static ?string $model = Reserve::class;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                searchableNameField()
                    ->required()
                    ->preload()
                    ->options(function () {
                        return \App\Models\User::whereDoesntHave('reserve')
                            ->get()
                            ->pluck('name', 'id');
                    })
                    ->searchable(),
                Forms\Components\TextInput::make('count')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'accept' => 'Accepting',
                        'pause' => 'Paused',
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
                    ->sortable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'accept' => 'Accepting',
                        'pause' => 'Paused',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'accept' => 'Accepting',
                        'pause' => 'Paused',
                    ]),
                Tables\Filters\TernaryFilter::make('hide_admin_reserves')
                    ->label('Hide admin reserves')
                    ->default(true)
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('user', fn ($q) => $q->where('role', '!=', User::ROLE_ADMIN)),
                        false: fn (Builder $query) => $query,
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReserves::route('/'),
            'create' => Pages\CreateReserves::route('/create'),
            'edit' => Pages\EditReserves::route('/{record}/edit'),
        ];
    }
}
