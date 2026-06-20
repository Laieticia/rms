<?php

return [
    'required' => 'Le champ :attribute est requis.',
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'unique' => 'La valeur du champ :attribute est déjà utilisée.',
    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'numeric' => 'Le champ :attribute doit être au moins :min.',
    ],
    'max' => [
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        'numeric' => 'Le champ :attribute ne doit pas dépasser :max.',
    ],
    'invalid_phone' => 'Le numéro de téléphone camerounais n\'est pas valide.',
    'phone_format' => 'Le numéro de téléphone doit être au format +237 XXX XXX XXX.',
    
    // Custom attributes
    'attributes' => [
        'first_name' => 'prénom',
        'last_name' => 'nom de famille',
        'email' => 'adresse email',
        'password' => 'mot de passe',
        'password_confirm' => 'confirmation du mot de passe',
        'phone' => 'numéro de téléphone',
        'phone_number' => 'numéro de téléphone',
        'full_name' => 'nom complet',
        'name' => 'nom',
        'title' => 'titre',
        'content' => 'contenu',
        'description' => 'description',
        'price' => 'prix',
        'quantity' => 'quantité',
        'date' => 'date',
        'time' => 'heure',
        'address' => 'adresse',
        'city' => 'ville',
        'country' => 'pays',
        'postal_code' => 'code postal',
        'region' => 'région',
        'status' => 'statut',
    ],
];
