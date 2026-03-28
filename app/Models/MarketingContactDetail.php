<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingContactDetail extends Model
{
    protected $fillable = [
        'phone',
        'phone_href',
        'email',
        'opening_hours',
        'address_line',
    ];

    public static function singleton(): self
    {
        $first = static::query()->first();
        if ($first !== null) {
            return $first;
        }

        return static::query()->create([
            'phone' => config('marketing.contact.phone'),
            'phone_href' => config('marketing.contact.phone_href'),
            'email' => config('marketing.contact.email'),
            'opening_hours' => null,
            'address_line' => null,
        ]);
    }

    /**
     * @return array{phone: string, phone_href: string, email: string, opening_hours: string, address_line: string}
     */
    public static function sharedForInertia(): array
    {
        $row = static::query()->first();
        if ($row === null) {
            return [
                'phone' => config('marketing.contact.phone'),
                'phone_href' => config('marketing.contact.phone_href'),
                'email' => config('marketing.contact.email'),
                'opening_hours' => '',
                'address_line' => '',
            ];
        }

        return [
            'phone' => $row->phone,
            'phone_href' => $row->phone_href,
            'email' => $row->email,
            'opening_hours' => $row->opening_hours ?? '',
            'address_line' => $row->address_line ?? '',
        ];
    }
}
