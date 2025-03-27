<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReserveResource\Pages\UserReserve;
use App\Filament\Resources\ReservesResource\Pages;
use App\Filament\Resources\ReservesResource\RelationManagers;
use App\Models\Reserve;
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
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'User';
    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $modelLabel = "My reserves";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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

    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             Tables\Columns\TextColumn::make('user.name')
    //                 ->searchable(['first_name', 'last_name'])
    //                 ->sortable(['first_name', 'last_name']),
    //             Tables\Columns\TextColumn::make('count')
    //                 ->numeric()
    //                 ->sortable(),
    //             Tables\Columns\SelectColumn::make('status')
    //                 ->options([
    //                     'accept' => 'Accepting',
    //                     'pause' => 'Paused',
    //                 ])
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('created_at')
    //                 ->dateTime()
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('updated_at')
    //                 ->dateTime()
    //                 ->sortable(),
    //         ])
    //         ->defaultSort('created_at', 'desc')
    //         ->filters([
    //             Tables\Filters\SelectFilter::make('status')
    //                 ->options([
    //                     'accept' => 'Accepting',
    //                     'pause' => 'Paused',
    //                 ]),
    //         ])
    //         ->actions([
    //             Tables\Actions\EditAction::make(),
    //         ])
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }

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
            // 'index' => Pages\ListReserves::route('/'),
            // 'create' => Pages\CreateReserves::route('/,
            'index' => UserReserve::route('/'),
        ];
    }
}
