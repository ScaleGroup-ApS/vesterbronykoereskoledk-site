<?php

namespace App\States;

use Thunk\Verbs\State;

class EnrollmentRequestState extends State
{
    public string $status = 'pending';

    public string $payment_method = '';
}
