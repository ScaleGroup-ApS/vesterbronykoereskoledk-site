import { useForm } from '@inertiajs/react';
import { sendLoginLink as StudentLoginLinkController } from '@/actions/App/Http/Controllers/Student/StudentController';
import type { Student } from '@/types';

export function useLoginLink(student: Student) {
    const form = useForm({});

    function sendLoginLink() {
        form.submit(StudentLoginLinkController(student));
    }

    return { sendLoginLink, processing: form.processing };
}
