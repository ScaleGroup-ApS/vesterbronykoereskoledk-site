import { router } from '@inertiajs/react';
import { store as storeAttendance } from '@/routes/bookings/attendance';

type Props = {
    bookingId: number;
    attended: boolean | null;
};

export function AttendanceCheckbox({ bookingId, attended }: Props) {
    function toggle() {
        router.post(
            storeAttendance({ id: bookingId }).url,
            { attended: !attended },
            { preserveScroll: true },
        );
    }

    return (
        <button
            type="button"
            onClick={toggle}
            className={`flex size-7 items-center justify-center rounded-md border transition-colors ${
                attended === true
                    ? 'border-green-500 bg-green-500/10 text-green-600'
                    : attended === false
                      ? 'border-destructive bg-destructive/10 text-destructive'
                      : 'border-input bg-transparent text-muted-foreground hover:border-primary/40'
            }`}
            aria-label={attended ? 'Fjern fremmøde' : 'Registrer fremmøde'}
            title={attended === true ? 'Mødt' : attended === false ? 'Ikke mødt' : 'Uregistreret'}
        >
            {attended === true && '✓'}
            {attended === false && '✗'}
            {attended === null && '–'}
        </button>
    );
}
