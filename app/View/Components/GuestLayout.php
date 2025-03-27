<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use App\Services\Metadata;

class GuestLayout extends Component
{
    public function __construct()
    {
        Metadata::set('title', 'Hip & Valley Financial Solutions - Insurance, Mortgages & Financial Planning');
        Metadata::set('description', 'Expert financial solutions including life insurance, mortgages, and financial planning. Secure your future with personalized guidance from Hip & Valley.');
        Metadata::set('keywords', 'life insurance, mortgage solutions, financial planning, financial services, Winnipeg financial advisor');
        Metadata::set('og:title', 'Hip & Valley Financial Solutions');
        Metadata::set('og:description', 'Your trusted partner for life insurance, mortgages, and financial planning solutions.');
        Metadata::set('og:type', 'website');
        Metadata::set('robots', 'index, follow');
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest', [
            'metadata' => Metadata::all(),
        ]);
    }
}
