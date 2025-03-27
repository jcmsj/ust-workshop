<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use App\Filament\Resources\NotesResource\Widgets\NotesWidget;
use App\Livewire\LeadAssignmentStatus;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Livewire;
use App\Livewire\LeadLayout;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Actions\Action as InfoListAction;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Illuminate\Support\Facades\Auth;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    public function getHeaderActions(): array
    {
        $actions = parent::getHeaderActions();
        $actions[] =
            Action::make('Assign')
            ->url(fn(Lead $record): string => route('filament.admin.resources.lead-assignments.create', ['lead_id' => $record->id]))
            ->visible(fn($record) => $record->leadAssignments->isEmpty());

        return $actions;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make([
                    'default' => 1,
                    'xl' => 2,
                ])
                    ->schema([
                        Infolists\Components\Grid::make(1)
                            ->columnSpan(1)
                            ->schema([
                                Livewire::make(LeadLayout::class),
                                $this->adminAssignmentDetails(),
                                $this->userAssignmentDetails(),
                            ]),
                        // Second Column
                        Livewire::make(NotesWidget::class, [
                            'record' => $this->record
                        ])->key('notes'),
                    ])
            ]);
    }

    public function userAssignmentDetails()
    {
        return Section::make('Assignment Details')
            ->visible(fn($record) => $record->leadAssignments->isNotEmpty() && Auth::user()->is_user)
            ->schema([
                Infolists\Components\RepeatableEntry::make('leadAssignments')
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'to call' => 'warning',
                                'success' => 'success',
                                'failed' => 'danger',
                                default => 'gray',
                            })->hintAction(
                                InfoListAction::make('status')
                                    ->icon('heroicon-o-pencil')
                                    ->label('Update')
                                    ->requiresConfirmation()
                                    ->form(function ($record) {
                                        return [
                                            Radio::make('status')
                                                ->default($record->status)
                                                ->options([
                                                    'to call' => 'To Call',
                                                    'success' => 'Success',
                                                    'failed' => 'Failed',
                                                ])
                                        ];
                                    })
                                    ->action(function ($record, array $data): void {
                                        $record->update([
                                            'status' => $data['status'],
                                        ]);
                                    })
                            ),
                        TextEntry::make('created_at')
                            ->label('Assigned At')
                            ->getStateUsing(fn($record) => $record->created_at->diffForHumans())
                    ])
                    ->label('')
            ]);
    }

    public function adminAssignmentDetails()
    {
        return Section::make('Assignment Details (admin)')
            ->visible(fn($record) => $record->leadAssignments->isNotEmpty() && Auth::user()->is_admin)
            ->schema([
                Infolists\Components\RepeatableEntry::make('leadAssignments')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name'),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'to call' => 'warning',
                                        'success' => 'success',
                                        'failed' => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('created_at')
                                    ->label('Assigned At')
                                    ->getStateUsing(fn($record) => $record->created_at->diffForHumans())
                            ])
                            ->label('')
                    ]),
            ]);
    }
}
