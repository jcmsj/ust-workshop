<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required(),
                        Forms\Components\TextInput::make('last_name')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required(),
                        Forms\Components\Select::make('role')
                            ->options([
                                User::ROLE_USER => 'User',
                                User::ROLE_ADMIN => 'Admin',
                            ])
                            ->required(),
                        // if apprroved, show a date (disabled)
                        Forms\Components\Grid::make()
                            ->columns(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('approved_at')
                                    ->label('Account was activated on')
                                    ->disabled(),
                                Forms\Components\ToggleButtons::make('is_approved')
                                    ->hidden(fn(User $record): bool => !$record->is_approved)
                                    ->label('Deactivate user?')
                                    ->options([
                                        false => 'Yes',
                                    ])
                                    ->afterStateUpdated(function () use ($form) {
                                        $form->fill([
                                            ...$form->getState(),
                                            'approved_at' => null,
                                        ]);
                                    }),
                            ]),
                        // activate button
                        Forms\Components\ToggleButtons::make('is_approved')
                            ->hidden(fn(User $record): bool => $record->is_approved)
                            ->label('Activiation:')
                            ->options([
                                true => 'Activate user account',
                            ])
                            ->afterStateUpdated(function ($set, bool $state) {
                                if ($state) {
                                    $set('approved_at', now());
                                }
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        User::ROLE_USER => 'success',
                        User::ROLE_ADMIN => 'warning',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        User::ROLE_USER => 'User',
                        User::ROLE_ADMIN => 'Admin',
                    ]),
                Tables\Filters\TernaryFilter::make('approved_at')
                    ->label('Approval Status')
                    ->placeholder('All Users')
                    ->trueLabel('Approved Users')
                    ->falseLabel('Pending Users')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('approved_at'),
                        false: fn(Builder $query) => $query->whereNull('approved_at'),
                        blank: fn(Builder $query) => $query
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn(User $record) => !$record->is_approved)
                    ->action(fn(User $record) => $record->approve()),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approveMultiple')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn(Collection $records) => $records->each->approve()),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RegistrationPaymentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
