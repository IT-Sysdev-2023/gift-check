<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

// test('new users can register', function () {
    
//     $response = $this->post('/register', [
//         'emp_id' => '1234',
//         'user_name' => 'username',
//         'password' => Hash::make('password'),
//         'firstname' => 'Testing',
//         'lastname' => 'Lastname',
//         'usertype' => '2',
//         'user_status' => 'active',
//         'user_role' => '0',
//         'ip_address' => '',
//         'login' => 'no',
//         'promo_tag' => 0,
//         'store_assigned' => '0',
//         'date_created' => now(),
//         'date_updated' => now(),
//         'user_addby' => 1
//     ]);

//     $this->assertAuthenticated();
//     $response->assertRedirect(route('treasury.dashboard', absolute: false));
// });
