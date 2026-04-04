import { Head } from '@inertiajs/react';
import { CheckCircle, ShieldCheck, Target } from 'lucide-react';
import { motion } from 'framer-motion';
import Heading from '@/components/heading';
import StudentLayout from '@/layouts/student-layout';
import { skills } from '@/routes/student';
import type { BreadcrumbItem } from '@/types';

type Skill = {
    key: string;
    label: string;
    count: number;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Færdigheder', href: skills().url },
];

function SkillRadar({ skills, completedSkills }: { skills: Skill[]; completedSkills: string[] }) {
    if (skills.length === 0) return null;

    const maxCount = Math.max(...skills.map(s => s.count), 1);
    const totalSkills = skills.length;
    const practicedCount = skills.filter(s => s.count > 0).length;
    const approvedCount = completedSkills.length;

    return (
        <div className="grid gap-6 sm:grid-cols-3">
            <div className="flex flex-col items-center justify-center gap-1 rounded-xl border bg-card p-5 shadow-sm">
                <Target className="size-5 text-primary" />
                <span className="mt-1 text-2xl font-bold tabular-nums">{practicedCount}/{totalSkills}</span>
                <span className="text-xs text-muted-foreground">Øvede færdigheder</span>
            </div>
            <div className="flex flex-col items-center justify-center gap-1 rounded-xl border bg-card p-5 shadow-sm">
                <ShieldCheck className="size-5 text-green-500" />
                <span className="mt-1 text-2xl font-bold tabular-nums">{approvedCount}/{totalSkills}</span>
                <span className="text-xs text-muted-foreground">Godkendte færdigheder</span>
            </div>
            <div className="flex flex-col items-center justify-center gap-1 rounded-xl border bg-card p-5 shadow-sm">
                <CheckCircle className="size-5 text-amber-500" />
                <span className="mt-1 text-2xl font-bold tabular-nums">{skills.reduce((s, sk) => s + sk.count, 0)}</span>
                <span className="text-xs text-muted-foreground">Øvelser i alt</span>
            </div>
        </div>
    );
}

function SkillCard({ skill, isApproved, index }: { skill: Skill; isApproved: boolean; index: number }) {
    const isPracticed = skill.count > 0;

    return (
        <motion.div
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: index * 0.04 }}
            className={`relative overflow-hidden rounded-xl border p-5 transition-all ${
                isApproved
                    ? 'border-green-500/30 bg-gradient-to-br from-green-500/10 to-green-500/[0.02] shadow-sm'
                    : isPracticed
                        ? 'border-primary/20 bg-gradient-to-br from-primary/5 to-transparent shadow-sm'
                        : 'border-border bg-muted/10'
            }`}
        >
            {/* Practice count badge in corner */}
            {isPracticed && (
                <div className="absolute -right-1 -top-1 flex size-8 items-center justify-center rounded-bl-xl bg-primary/10 text-xs font-bold text-primary">
                    {skill.count}
                </div>
            )}

            <div className="flex flex-col gap-2">
                <p className={`text-sm font-semibold ${isPracticed || isApproved ? 'text-foreground' : 'text-muted-foreground/60'}`}>
                    {skill.label}
                </p>

                {isApproved ? (
                    <div className="flex items-center gap-1.5">
                        <CheckCircle className="size-4 text-green-500" />
                        <span className="text-xs font-medium text-green-600">Godkendt</span>
                    </div>
                ) : isPracticed ? (
                    <div className="space-y-1.5">
                        <div className="h-1.5 overflow-hidden rounded-full bg-muted">
                            <div
                                className="h-full rounded-full bg-primary transition-all duration-700"
                                style={{ width: `${Math.min(100, (skill.count / 5) * 100)}%` }}
                            />
                        </div>
                        <p className="text-xs text-muted-foreground">Øvet {skill.count} {skill.count === 1 ? 'gang' : 'gange'}</p>
                    </div>
                ) : (
                    <p className="text-xs text-muted-foreground/50">Ikke øvet endnu</p>
                )}
            </div>
        </motion.div>
    );
}

export default function StudentFaerdigheder({ skills, completedSkills = [] }: { skills: Skill[]; completedSkills: string[] }) {
    const practiced = skills.filter((s) => s.count > 0);
    const notPracticed = skills.filter((s) => s.count === 0);

    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Færdigheder" />
            <div className="flex h-full flex-1 flex-col gap-8 rounded-xl p-4 sm:p-6">
                <div className="space-y-1">
                    <Heading title="Færdigheder" />
                    <p className="text-sm text-muted-foreground">
                        Oversigt over dine kørefærdigheder — øvede, godkendte og hvad der mangler.
                    </p>
                </div>

                <SkillRadar skills={skills} completedSkills={completedSkills} />

                {practiced.length > 0 && (
                    <section className="space-y-4">
                        <Heading variant="small" title="Øvede færdigheder" />
                        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                            {practiced.map((skill, i) => (
                                <SkillCard key={skill.key} skill={skill} isApproved={completedSkills.includes(skill.key)} index={i} />
                            ))}
                        </div>
                    </section>
                )}

                {notPracticed.length > 0 && (
                    <section className="space-y-4">
                        <Heading variant="small" title="Endnu ikke øvet" />
                        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                            {notPracticed.map((skill, i) => (
                                <SkillCard key={skill.key} skill={skill} isApproved={completedSkills.includes(skill.key)} index={practiced.length + i} />
                            ))}
                        </div>
                    </section>
                )}

                {skills.every((s) => s.count === 0) && (
                    <div className="flex flex-col items-center gap-3 rounded-2xl border border-dashed py-10 text-center">
                        <Target className="size-10 text-muted-foreground/30" />
                        <div>
                            <p className="font-medium text-muted-foreground">Ingen færdigheder registreret endnu</p>
                            <p className="mt-1 text-sm text-muted-foreground/70">
                                De vil dukke op her efter din første kørelektion.
                            </p>
                        </div>
                    </div>
                )}
            </div>
        </StudentLayout>
    );
}
