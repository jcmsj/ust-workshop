<?php

use App\Http\Mail\NewLead;
use App\Models\Lead;
use App\Models\LeadAssignment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it sends an email with correct recipient', function () {
    Mail::fake();

    // Trigger the email sending logic
    $lead = Lead::factory()->create();
    // make a random user
    $user = User::factory()->create([
        'first_name' => 'Anon',
        'last_name' => 'Tokyo',
        'email' => 'anontokyo@gmail.com',
    ]);
    $transaction = LeadAssignment::factory()->create([
        'lead_id' => $lead->id,
        'user_id' => $user->id,
    ]);
    Mail::to('recipient@example.com')->send(new NewLead($lead, $transaction));

    // Assert that the email was sent
    Mail::assertSent(NewLead::class, function ($mail) {
        return $mail->hasTo('recipient@example.com');
    });

    // Assert the first name is present in the email body
    Mail::assertSent(NewLead::class, function ($mail) use ($lead, $transaction) {
        return $mail->hasSubject("New Lead: {$lead->name}") &&
            strpos($mail->render(), $transaction->user->name) !== false;
    });
});
