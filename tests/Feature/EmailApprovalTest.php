<?php

use App\Mail\UserApproved;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it sends an email with correct recipient', function () {
    Mail::fake();

    // Create a user
    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'johndoe@example.com',
    ]);

    // Trigger the email sending logic
    Mail::to('recipient@example.com')->send(new UserApproved($user));

    // Assert that the email was sent
    Mail::assertSent(UserApproved::class, function ($mail) {
        return $mail->hasTo('recipient@example.com');
    });

    // Assert the user's name is present in the email body
    Mail::assertSent(UserApproved::class, function ($mail) use ($user) {
        return strpos($mail->render(), $user->name) !== false;
    });
});
