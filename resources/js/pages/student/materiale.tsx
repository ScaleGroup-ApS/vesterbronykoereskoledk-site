import { Head } from '@inertiajs/react';
import StudentLayout from '@/layouts/student-layout';

export default function StudentMateriale() {
    return (
        <StudentLayout breadcrumbs={[]}>
            <Head title="Materiale" />
            <div className="p-4">Materiale</div>
        </StudentLayout>
    );
}
