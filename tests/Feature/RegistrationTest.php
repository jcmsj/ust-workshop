<?php

use App\Filament\Pages\Unapproved;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(302);
    // check that it redirects to /app/register
    $response->assertRedirect('/app/register');

})->skip(function () {
    return ! Features::enabled(Features::registration());
}, 'Registration support is not enabled.');

test('registration screen cannot be rendered if support is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
})->skip(function () {
    return Features::enabled(Features::registration());
}, 'Registration support is enabled.');

// test('new users can register', function () {
//     $response = $this->post("register", [
//         'first_name' => 'Test',
//         'last_name' => 'User',
//         'email' => 'test@example.com',
//         'password' => 'password',
//         'password_confirmation' => 'password',
//         'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
//         'payment_details' => 'Visa 1234',
//         'payment_proof_url' => 'https://example.com/payment-proof.jpg',
//     ]);

//     // $response->dd();
//     // $this->assertAuthenticated();
//     // output the response content
//     $response->assertRedirect(Unapproved::getUrl());
// })->skip(function () {
//     return ! Features::enabled(Features::registration());
// }, 'Registration support is not enabled.');
