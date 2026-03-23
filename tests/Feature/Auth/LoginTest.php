<?php

test('user can login with valid credentials', function () {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'admin@bdms.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'token',
                'user'
            ]
        ]);
});

test('it fails to login with incorrect password', function () {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'admin@bdms.com',
        'password' => 'wrong-password', // password incorrect
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Invalid credentials',
            'status' => 401,
        ]);
});

test('it fails to login with non-existent email', function () {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'nobody@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(401);
});
