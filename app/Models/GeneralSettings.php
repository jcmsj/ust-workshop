<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\LaravelSettings\Settings; 

class GeneralSettings extends Settings implements Auditable
{
  use \OwenIt\Auditing\Auditable;
    public static function group(): string
    {
        return 'general';
    }
}
