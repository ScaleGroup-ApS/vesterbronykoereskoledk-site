<?php

namespace App\Events;

use Thunk\Verbs\Event;

class MaterialUnlockSet extends Event
{
    public int $offer_id;

    public int $media_id;

    public string $media_name;

    public ?int $unlock_at_lesson;

    public int $set_by;
}
