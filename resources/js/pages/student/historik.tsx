import { Head } from '@inertiajs/react';
import StudentLayout from '@/layouts/student-layout';

export default function StudentHistorik() {
    return (
        <StudentLayout breadcrumbs={[]}>
            <Head title="Historik" />
            <div className="p-4">Historik</div>
        </StudentLayout>
    );
}
