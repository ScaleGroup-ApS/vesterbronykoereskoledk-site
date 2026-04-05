<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Customer Slug
    |--------------------------------------------------------------------------
    |
    | Set via CUSTOMER_SLUG in the Kubernetes pod spec (inline env var, not a
    | secret). Matches the k8s namespace, database prefix, and image name for
    | this deployment. Used to load app/Customers/{slug}/ServiceProvider.php.
    |
    */

    'slug' => env('CUSTOMER_SLUG'),

];
