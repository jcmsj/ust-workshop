<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Unapproved extends Page
{
    protected static string $view = 'filament.pages.unapproved';
    protected static bool $shouldRegisterNavigation = false;
}
