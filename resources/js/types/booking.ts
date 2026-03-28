export type BookingEvent = {
    id: number;
    title: string;
    start: string;
    end: string;
    type: BookingType;
    status: BookingStatus;
    instructor: string;
    vehicle: string | null;
    notes: string | null;
    attended: boolean | null;
    attendance_recorded_at: string | null;
};

export type BookingType =
    | 'driving_lesson'
    | 'theory_lesson'
    | 'track_driving'
    | 'slippery_driving'
    | 'theory_exam'
    | 'practical_exam';

export type BookingStatus = 'scheduled' | 'completed' | 'cancelled' | 'no_show';

export const bookingTypeColors: Record<BookingType, string> = {
    driving_lesson: '#3b82f6',
    theory_lesson: '#22c55e',
    track_driving: '#f97316',
    slippery_driving: '#a855f7',
    theory_exam: '#ef4444',
    practical_exam: '#b91c1c',
};

export const bookingTypeLabels: Record<BookingType, string> = {
    driving_lesson: 'Køretime',
    theory_lesson: 'Teorilektion',
    track_driving: 'Banekørsel',
    slippery_driving: 'Glat bane',
    theory_exam: 'Teoriprøve',
    practical_exam: 'Køreprøve',
};
