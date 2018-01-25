<?php

return [
    'serializer' => \League\Fractal\Serializer\ArraySerializer::class,

    /*
    |--------------------------------------------------------------------------
    | Retrieves a list of contents
    |--------------------------------------------------------------------------
    |
    | Get multi records.
    |
    */

    'listing'   => [
        'response' => ':response',
        'header'   => 200
    ],


    /*
    |--------------------------------------------------------------------------
    | Retrieves a specific content
    |--------------------------------------------------------------------------
    |
    | Get single record.
    |
    */

    'show'      => [
        'response' => ':response',
        'header'   => 200
    ],


    /*
    |--------------------------------------------------------------------------
    | Creates a new content
    |--------------------------------------------------------------------------
    |
    | Insert record.
    |
    */

    'created'   => [
        'response' => ':response',
        'header'   => 201
    ],


    /*
    |--------------------------------------------------------------------------
    | Updates a specific content
    |--------------------------------------------------------------------------
    |
    | Update record.
    |
    */

    'updated'   => [
        'response' => ':response',
        'header'   => 200
    ],


    /*
    |--------------------------------------------------------------------------
    | Deletes a specific content
    |--------------------------------------------------------------------------
    |
    | Delete record.
    |
    */

    'deleted'   => [
        'response' => null,
        'header'   => 204
    ],

    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    |
    | OK.
    |
    */

    'success'   => [
        'response' => [
            'message' => ':response|OK'
        ],
        'header'   => 200
    ],

    /*
    |--------------------------------------------------------------------------
    | Error 400
    |--------------------------------------------------------------------------
    |
    | Bad Request.
    |
    */

    'error_400' => [
        'response' => [
            'code'        => 400,
            'message'     => 'Bad Request',
            'description' => ':response|The request was invalid or cannot be otherwise served.'
        ],
        'header'   => 400
    ],

    /*
    |--------------------------------------------------------------------------
    | Error 401
    |--------------------------------------------------------------------------
    |
    | Unauthorized.
    |
    */

    'error_401' => [
        'response' => [
            'code'        => 401,
            'message'     => 'Unauthorized',
            'description' => ':response|Authentication credentials were missing or incorrect.'
        ],
        'header'   => 401
    ],


    /*
    |--------------------------------------------------------------------------
    | Error 403
    |--------------------------------------------------------------------------
    |
    | Forbidden.
    |
    */

    'error_403' => [
        'response' => [
            'code'        => 403,
            'message'     => 'Forbidden',
            'description' => ':response|The request is understood, but it has been refused or access is not allowed.'
        ],
        'header'   => 403
    ],


    /*
    |--------------------------------------------------------------------------
    | Error 404
    |--------------------------------------------------------------------------
    |
    | Not found.
    |
    */

    'error_404' => [
        'response' => [
            'code'        => 404,
            'message'     => 'Not found',
            'description' => ':response|The request was not found.'
        ],
        'header'   => 404
    ],


    /*
    |--------------------------------------------------------------------------
    | Error 405
    |--------------------------------------------------------------------------
    |
    | Method Not Allowed.
    |
    */

    'error_405' => [
        'response' => [
            'code'        => 405,
            'message'     => 'Method Not Allowed',
            'description' => ':response|Request method is not allowed.'
        ],
        'header'   => 405
    ],


    /*
    |--------------------------------------------------------------------------
    | Error 406
    |--------------------------------------------------------------------------
    |
    | Not Acceptable.
    |
    */

    'error_406' => [
        'response' => [
            'code'        => 406,
            'message'     => 'Not Acceptable',
            'description' => ':response|Returned when an invalid format is specified in the request.'
        ],
        'header'   => 406
    ],


    /*
    |--------------------------------------------------------------------------
    | Error 410
    |--------------------------------------------------------------------------
    |
    | Gone.
    |
    */

    'error_410' => [
        'response' => [
            'code'        => 410,
            'message'     => 'Gone',
            'description' => ':response|This resource is gone. Used to indicate that an API endpoint has been turned off.'
        ],
        'header'   => 410
    ],


    /*
    |--------------------------------------------------------------------------
    | Error 422 (Validation error)
    |--------------------------------------------------------------------------
    |
    | Unprocessable Entity.
    |
    */

    'error_422' => [
        'response' => [
            'code'        => 422,
            'message'     => 'Unprocessable Entity',
            'description' => 'Data is unable to be processed.',
            'errors'      => ':response'
        ],
        'header'   => 422
    ],


    /*
    |--------------------------------------------------------------------------
    | Error 429
    |--------------------------------------------------------------------------
    |
    | Too Many Requests.
    |
    */

    'error_429' => [
        'response' => [
            'code'        => 429,
            'message'     => 'Too Many Requests',
            'description' => ':response|Request cannot be served due to the application\'s rate limit having been exhausted for the resource.'
        ],
        'header'   => 429
    ]

];