<?php

test('user can register with valid credentials', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'userName' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => 'password123',
        'passwordConfirmation' => 'password123',
        'roleId' => 3
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user',
                'token'
            ]
        ]);
});

test('it fails if email is already taken', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'user_name' => 'New User',
        'email' => 'admin@bdms.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('it fails if required fields are missing', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'userName' => '',
        'email' => 'not-an-email',
        'password' => '',
        'passwordConfirmation' => ''
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['userName', 'email', 'password', 'passwordConfirmation']);
});

test('it fails if password is too short', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'user_name' => 'Jame Doe',
        'email' => 'jamedoe@example.com',
        'password' => '123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});
