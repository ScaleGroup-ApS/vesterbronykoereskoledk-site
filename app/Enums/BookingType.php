<?php

namespace App\Enums;

enum BookingType: string
{
    case DrivingLesson = 'driving_lesson';
    case TheoryLesson = 'theory_lesson';
    case TrackDriving = 'track_driving';
    case SlipperyDriving = 'slippery_driving';
    case TheoryExam = 'theory_exam';
    case PracticalExam = 'practical_exam';

    public function label(): string
    {
        return match ($this) {
            self::DrivingLesson => 'Køretime',
            self::TheoryLesson => 'Teorilektion',
            self::TrackDriving => 'Banekørsel',
            self::SlipperyDriving => 'Glat bane',
            self::TheoryExam => 'Teoriprøve',
            self::PracticalExam => 'Køreprøve',
        };
    }
}
