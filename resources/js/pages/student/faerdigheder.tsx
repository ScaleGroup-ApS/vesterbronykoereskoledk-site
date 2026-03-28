import { Head } from '@inertiajs/react';
import StudentLayout from '@/layouts/student-layout';

export default function StudentFaerdigheder() {
    return (
        <StudentLayout breadcrumbs={[]}>
            <Head title="Færdigheder" />
            <div className="p-4">Færdigheder</div>
        </StudentLayout>
    );
}
