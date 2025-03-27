<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{

    /** @use HasFactory<\Database\Factories\LeadFactory> */
    use HasFactory;
    protected $fillable = [
        'insurance_type',
        'province_territory',
        'birthdate',
        'sex',
        'desired_amount',
        'length_coverage',
        'mortgage_amortization',
        'length_payment',
        'health_class',
        'tobacco_use',
        'journey',
        'first_name',
        'last_name',
        'mobile_number',
        'email',
    ];

    public function name(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Notes::class);
    }

    // birthdate :: 
    public function getBirthdateAttribute($value): string
    {
        return Carbon::parse($value)->format('d/m/Y');
    }

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->format('F j, Y, g:i a');
    }

    public function getDesiredAmountAttribute($value): ?string
    {
        return $value ? number_format($value) : null;
    }

    public function getMortgageAmortizationAttribute($value): ?string
    {
        return $value ? $value . ' years' : null;
    }

    public function getLengthCoverageAttribute($value): ?string
    {
        return $value ? $value . ' years' : null;
    }

    public function getLengthPaymentAttribute($value): ?string
    {
        return $value ? $value : null;
    }

    public function getTobaccoUseAttribute($value): string
    {
        return $value ? 'Yes' : 'No';
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('leadAssignments', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }

    public function leadAssignments(): HasMany
    {
        return $this->hasMany(LeadAssignment::class);
    }

    public function latestAssignment()
    {
        return $this->hasOne(LeadAssignment::class)->latest();
    }

    public function scopeUnassigned($query)
    {
        return $query->doesntHave('leadAssignments');
    }

    public function kanTask(): ?HasOne
    {
        return $this->hasOne(KanTask::class)->withDefault(null);
    }
}
