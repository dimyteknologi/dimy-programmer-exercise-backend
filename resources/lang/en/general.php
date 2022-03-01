<?php

return [

    'crud' => [
        'store' => [
            'success' => 'Store data success',
            'failed' => 'Store data failed, please try again'
        ],
        'show' => [
            'success' => 'Show data success',
            'failed' => 'Cannot find data, please try again'
        ],
        'destroy' => [
            'success' => 'Data deleted successfully',
            'failed' => 'Cannot delete data, please try again',
            'empty' => 'Cannot find data, please try again'
        ]
    ],

    'permission' => [
        'access' => [
            'reject' => 'Unauthorized, user dont have access'
        ]
    ],

    'report' => [
        'failed' => 'Cannot generate report, please try again'
    ],

    'request' => [
        'success' => 'Request performed successfully',
        'failed' => 'Request failed, please try again'
    ]

];
