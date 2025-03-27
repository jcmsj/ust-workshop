<?php

namespace App\Http\Controllers;

use App\Http\Mail\NewLead;
use App\Models\LeadAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function test() {
        $leadAssignemnt = LeadAssignment::with('lead')->find(1);
    
        $prefixes = [
            'desired_amount' => '$',
        ];
    
        $suffixes = [
            'mortgage_amortization' => 'years',
            'length_coverage' => 'years',
            'length_payment' => 'years',
        ];

        Mail::to("jcsanjuan12@gmail.com")->send(new NewLead($leadAssignemnt->lead, $leadAssignemnt));

        return view('emails.lead-notification', [
            'lead' => $leadAssignemnt->lead,
            'leadAssignment' => $leadAssignemnt,
            'keyToHeaders' => NewLead::keyToHeaders,
            'prefixes' => $prefixes,
            'suffixes' => $suffixes,
        ]);
    }
}
