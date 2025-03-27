<?php

namespace App\Filament\Admin\Resources\ArticleResource\Pages;

use App\Filament\Admin\Resources\ArticleResource;
use App\Models\ArticleCategory;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class OrderArticles extends ListRecords {

    protected static string $resource = ArticleResource::class;

    public ?string $selectedCategory = null;

    public function mount(): void
    {
        $this->selectedCategory = ArticleCategory::where('name', 'featured')->first()?->id 
            ?? ArticleCategory::first()?->id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->getTableQuery()
            )
            ->reorderable('display_order')
            ->defaultSort('display_order', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('selectCategory')
                    ->form([
                        Select::make('category')
                            ->options(ArticleCategory::pluck('label', 'id'))
                            ->default($this->selectedCategory)
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->selectedCategory = $state;
                            }),
                    ])
                    ->label('Select Category'),
                Tables\Actions\Action::make('currentCategory')
                    ->label('Current Category: ' . ArticleCategory::find($this->selectedCategory)?->label)
                    ->disabled(),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return static::$resource::getEloquentQuery()
            ->where('category_id', $this->selectedCategory);
    }

    public function getBreadcrumbs(): array
    {
        return [
            './' => "Articles",
            'reorder' => 'Reorder',
        ];
    }

}
