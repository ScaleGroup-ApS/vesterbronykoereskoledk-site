<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Customer Identity
    |--------------------------------------------------------------------------
    |
    | Set via CUSTOMER_ID in the Kubernetes ConfigMap. Used to load the
    | matching customer module from app/Customers/{id}/ServiceProvider.php.
    |
    */

    'id' => env('CUSTOMER_ID'),

];
