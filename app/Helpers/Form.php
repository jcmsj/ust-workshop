<?php

namespace App\Helpers;

use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

function searchableNameField() {
  return Select::make('user_id')
    // https://stackoverflow.com/a/78417516
    ->relationship('user', 'name', function (Builder $query) {
      $query->select('id', 'first_name', 'last_name');
    })
    ->getOptionLabelFromRecordUsing(function ($record) {
      return $record->name;
    })
    ->searchable(['first_name', 'last_name']);
}
