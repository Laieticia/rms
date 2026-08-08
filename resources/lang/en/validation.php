<?php

return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute field must be a valid email address.',
    'numeric' => 'The :attribute field must be a number.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'unique' => 'The :attribute has already been taken.',
    'min' => [
        'string' => 'The :attribute field must be at least :min characters.',
        'numeric' => 'The :attribute field must be at least :min.',
    ],
    'max' => [
        'string' => 'The :attribute field must not exceed :max characters.',
        'numeric' => 'The :attribute field must not exceed :max.',
    ],
    'invalid_phone' => 'The Cameroonian phone number is not valid.',
    'phone_format' => 'The phone number must be in the format +237 XXX XXX XXX.',

    // Custom attributes
    'attributes' => [
        'first_name' => 'first name',
        'last_name' => 'last name',
        'email' => 'email address',
        'password' => 'password',
        'password_confirm' => 'password confirmation',
        'phone' => 'phone number',
        'phone_number' => 'phone number',
        'full_name' => 'full name',
        'name' => 'name',
        'title' => 'title',
        'content' => 'content',
        'description' => 'description',
        'price' => 'price',
        'quantity' => 'quantity',
        'date' => 'date',
        'time' => 'time',
        'address' => 'address',
        'city' => 'city',
        'country' => 'country',
        'postal_code' => 'postal code',
        'region' => 'region',
        'status' => 'status',
    ],
];
