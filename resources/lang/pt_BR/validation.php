<?php

return [
    'accepted' => 'O campo :attribute deve ser aceito.',
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed' => 'A confirmação de :attribute não confere.',
    'current_password' => 'A senha informada não confere com a sua senha atual.',
    'email' => 'O campo :attribute deve ser um e-mail válido.',
    'min' => [
        'string' => 'O campo :attribute deve ter no mínimo :min caracteres.',
    ],
    'max' => [
        'string' => 'O campo :attribute deve ter no máximo :max caracteres.',
    ],
    'required' => 'O campo :attribute é obrigatório.',
    'string' => 'O campo :attribute deve ser um texto.',
    'unique' => 'O :attribute já está em uso.',
    'same' => 'Os campos :attribute e :other devem ser iguais.',
    'size' => [
        'string' => 'O campo :attribute deve ter :size caracteres.',
    ],

    // Nomes bonitos dos campos (aparecem nas mensagens)
    'attributes' => [
        'name' => 'nome',
        'email' => 'e-mail',
        'password' => 'senha',
        'current_password' => 'senha atual',
        'password_confirmation' => 'confirmação de senha',

        // do seu app
        'nome' => 'nome',
        'tipo' => 'tipo',
    ],
];
