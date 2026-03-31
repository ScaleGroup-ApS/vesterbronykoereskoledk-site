<?php

namespace App\Enums;

enum DrivingSkill: string
{
    case Parkering = 'parking';
    case Motorvej = 'motorvej';
    case Rundkorsel = 'roundabouts';
    case Bykørsel = 'city_driving';
    case Overhaling = 'overtaking';
    case Bakring = 'reversing';
    case Filskifte = 'lane_change';
    case Nodstop = 'emergency_stop';

    public function label(): string
    {
        return match ($this) {
            self::Parkering => 'Parkering',
            self::Motorvej => 'Motorvej',
            self::Rundkorsel => 'Rundkørsel',
            self::Bykørsel => 'Bykørsel',
            self::Overhaling => 'Overhaling',
            self::Bakring => 'Bakring',
            self::Filskifte => 'Filskifte',
            self::Nodstop => 'Nødstop',
        };
    }
}
