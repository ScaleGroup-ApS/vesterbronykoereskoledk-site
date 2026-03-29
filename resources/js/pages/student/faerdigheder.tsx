import { Head } from '@inertiajs/react';
import { motion } from 'framer-motion';
import Heading from '@/components/heading';
import StudentLayout from '@/layouts/student-layout';
import { faerdigheder } from '@/routes/student';
import type { BreadcrumbItem } from '@/types';

type Skill = {
    key: string;
    label: string;
    count: number;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Færdigheder', href: faerdigheder().url },
];

function SkillCard({ skill }: { skill: Skill }) {
    const isPracticed = skill.count > 0;

    return (
        <motion.div
            initial={{ opacity: 0, y: 8 }}
            animate={{ opacity: 1, y: 0 }}
            className={`flex flex-col items-center justify-center gap-1 rounded-xl border p-5 text-center transition-colors ${
                isPracticed
                    ? 'border-primary/30 bg-primary/5'
                    : 'border-border bg-muted/20 opacity-50'
            }`}
        >
            <p className={`text-sm font-semibold ${isPracticed ? 'text-foreground' : 'text-muted-foreground'}`}>
                {skill.label}
            </p>
            {isPracticed ? (
                <p className="text-xs font-medium text-primary">× {skill.count}</p>
            ) : (
                <p className="text-xs text-muted-foreground">Ikke øvet endnu</p>
            )}
        </motion.div>
    );
}

export default function StudentFaerdigheder({ skills }: { skills: Skill[] }) {
    const practiced = skills.filter((s) => s.count > 0);
    const notPracticed = skills.filter((s) => s.count === 0);

    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Færdigheder" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="space-y-1">
                    <Heading title="Færdigheder" />
                    <p className="text-sm text-muted-foreground">
                        Her ser du hvilke kørefærdigheder du har øvet, og hvor mange gange.
                    </p>
                </div>

                {practiced.length > 0 && (
                    <section className="space-y-3">
                        <Heading variant="small" title="Øvede færdigheder" />
                        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                            {practiced.map((skill) => (
                                <SkillCard key={skill.key} skill={skill} />
                            ))}
                        </div>
                    </section>
                )}

                {notPracticed.length > 0 && (
                    <section className="space-y-3">
                        <Heading variant="small" title="Endnu ikke øvet" />
                        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                            {notPracticed.map((skill) => (
                                <SkillCard key={skill.key} skill={skill} />
                            ))}
                        </div>
                    </section>
                )}

                {skills.every((s) => s.count === 0) && (
                    <p className="text-sm text-muted-foreground">
                        Ingen kørefærdigheder registreret endnu — de vil dukke op her efter din første kørelektion.
                    </p>
                )}
            </div>
        </StudentLayout>
    );
}
