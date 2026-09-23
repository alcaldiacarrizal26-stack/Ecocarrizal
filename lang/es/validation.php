<?php

return [
    'accepted'             => 'El campo :attribute debe ser aceptado.',
    'active_url'           => 'El campo :attribute no es una URL válida.',
    'after'                => 'El campo :attribute debe ser una fecha posterior a :date.',
    'alpha'                => 'El campo :attribute solo puede contener letras.',
    'alpha_num'            => 'El campo :attribute solo puede contener letras y números.',
    'array'                => 'El campo :attribute debe ser un conjunto.',
    'before'               => 'El campo :attribute debe ser una fecha anterior a :date.',
    'between'              => [
        'numeric' => 'El campo :attribute tiene que estar entre :min y :max.',
        'file'    => 'El campo :attribute debe pesarte entre :min y :max kilobytes.',
        'string'  => 'El campo :attribute tiene que tener entre :min y :max caracteres.',
        'array'   => 'El campo :attribute tiene que tener entre :min y :max elementos.',
    ],
    'boolean'              => 'El campo :attribute debe tener un valor verdadero o falso.',
    'confirmed'            => 'La confirmación de :attribute no coincide.',
    'email'                => 'El campo :attribute no es un correo válido.',
    'numeric'              => 'El campo :attribute debe ser un número.',
    'required'             => 'El campo :attribute es obligatorio.',
    'string'               => 'El campo :attribute debe ser una cadena de caracteres.',
    'unique'               => 'El campo :attribute ya ha sido registrado.',
    'url'                  => 'El campo :attribute no es una URL válida.',

    /*
    |--------------------------------------------------------------------------
    | Personalización de nombres de atributos
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'name'                  => 'nombre',
        'email'                 => 'correo electrónico',
        'password'              => 'contraseña',
        'description'           => 'descripción',
    ],
];