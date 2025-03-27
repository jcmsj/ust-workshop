<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotesResource\Pages;
use App\Filament\Resources\NotesResource\RelationManagers;
use App\Models\Notes;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

use function App\Helpers\searchableNameField;

class NotesResource extends Resource
{
    protected static ?string $model = Notes::class;
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'phosphor-sticker';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->label('Content'),
                searchableNameField()->required(),
                    Actions::make([
                        Forms\Components\Actions\Action::make('view_lead')
                        ->label('View Lead information')
                        ->url(fn ($record) => Auth::user()->is_admin 
                            ? route('filament.admin.resources.leads.view', $record->lead_id) 
                            : route('filament.app.resources.leads.view', $record->lead_id))
                        ->openUrlInNewTab(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (!Auth::user()->is_admin) {
                    $query->where('user_id', Auth::user()->id);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('content')
                    ->label('Content')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('insuranceQuote.name')
                    ->label('Insurance Quote')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListNotes::route('/'),
            'create' => Pages\CreateNotes::route('/create'),
            'edit' => Pages\EditNotes::route('/{record}/edit'),
        ];
    }
}
