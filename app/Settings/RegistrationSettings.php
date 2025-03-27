<?php

namespace App\Settings;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelSettings\Settings;

class RegistrationSettings extends Settings 
{
    public float $payment_amount_due;

    public static function group(): string
    {
        return 'register';
    }
}
