<?php

use App\Rules\DBQuery;

return [

    /*
    |--------------------------------------------------------------------------
    | Jobs Available
    |--------------------------------------------------------------------------
    */

    'query' => [
        'title' => 'Query',
        'file' => \App\Jobs\Query::class,
        'allow_params' => true,
        'validations' => ['required', 'string', new DBQuery],
    ],

    'other' => [
        'title' => 'Other',
        'file' => \App\Jobs\Other::class,
        'allow_params' => false,
    ],

];
