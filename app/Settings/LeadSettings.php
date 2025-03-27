<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LeadSettings extends Settings
{
    public float $cost_per_lead = 100.00;

    public static function group(): string
    {
        return 'Lead';
    }
}
