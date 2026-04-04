import { Head } from '@inertiajs/react';
import { ArrowDownRight, ArrowUpRight, CreditCard, Receipt, Wallet } from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import StudentLayout from '@/layouts/student-layout';
import { payments } from '@/routes/student';
import type { BreadcrumbItem } from '@/types';

type Balance = {
    total_owed: number;
    total_paid: number;
    outstanding: number;
};

type Payment = {
    id: number;
    amount: number;
    method: string;
    method_label: string;
    recorded_at: string;
    notes: string | null;
};

type OfferPrice = {
    name: string;
    price: number;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Betalinger', href: payments().url },
];

function formatCurrency(amount: number): string {
    return Number(amount).toLocaleString('da-DK', { minimumFractionDigits: 0 }) + ' kr.';
}

const methodIcons: Record<string, React.ElementType> = {
    cash: Wallet,
    card: CreditCard,
    mobile_pay: CreditCard,
    invoice: Receipt,
};

export default function StudentPayments({
    balance,
    payments: paymentList,
    offer_prices,
}: {
    balance: Balance;
    payments: Payment[];
    offer_prices: OfferPrice[];
}) {
    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Betalinger" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6">
                <div className="space-y-1">
                    <Heading title="Betalinger" />
                    <p className="text-sm text-muted-foreground">
                        Overblik over din saldo, betalinger og pakkepriser.
                    </p>
                </div>

                {/* Balance cards */}
                <div className="grid gap-3 sm:grid-cols-3">
                    <div className="rounded-xl border bg-card p-5 shadow-sm">
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                            <ArrowUpRight className="size-4 text-blue-500" />
                            Samlet pris
                        </div>
                        <p className="mt-2 text-2xl font-bold tabular-nums">{formatCurrency(balance.total_owed)}</p>
                    </div>
                    <div className="rounded-xl border bg-card p-5 shadow-sm">
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                            <ArrowDownRight className="size-4 text-green-500" />
                            Betalt i alt
                        </div>
                        <p className="mt-2 text-2xl font-bold tabular-nums text-green-600">{formatCurrency(balance.total_paid)}</p>
                    </div>
                    <div className={`rounded-xl border p-5 shadow-sm ${balance.outstanding > 0 ? 'border-amber-500/30 bg-amber-500/5' : 'bg-card'}`}>
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                            <Wallet className="size-4 text-amber-500" />
                            Udestående
                        </div>
                        <p className={`mt-2 text-2xl font-bold tabular-nums ${balance.outstanding > 0 ? 'text-amber-600' : 'text-green-600'}`}>
                            {formatCurrency(balance.outstanding)}
                        </p>
                    </div>
                </div>

                {/* Offer price breakdown */}
                {offer_prices.length > 0 && (
                    <section className="space-y-3">
                        <h2 className="text-base font-medium">Pakkepriser</h2>
                        <div className="rounded-xl border shadow-sm">
                            {offer_prices.map((offer, i) => (
                                <div
                                    key={i}
                                    className="flex items-center justify-between border-b px-5 py-3 last:border-b-0"
                                >
                                    <span className="text-sm">{offer.name}</span>
                                    <span className="text-sm font-medium tabular-nums">
                                        {formatCurrency(offer.price)}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </section>
                )}

                {/* Payment history */}
                <section className="space-y-3">
                    <h2 className="text-base font-medium">Betalingshistorik</h2>
                    {paymentList.length === 0 ? (
                        <div className="flex flex-col items-center gap-3 rounded-2xl border border-dashed py-10 text-center">
                            <Receipt className="size-10 text-muted-foreground/30" />
                            <div>
                                <p className="font-medium text-muted-foreground">Ingen betalinger endnu</p>
                                <p className="mt-1 text-sm text-muted-foreground/70">
                                    Dine betalinger vil blive vist her.
                                </p>
                            </div>
                        </div>
                    ) : (
                        <div className="divide-y rounded-xl border shadow-sm">
                            {paymentList.map((p) => {
                                const Icon = methodIcons[p.method] ?? CreditCard;
                                return (
                                    <div key={p.id} className="flex items-center justify-between gap-4 px-5 py-4">
                                        <div className="flex items-center gap-3">
                                            <div className="flex size-9 items-center justify-center rounded-full bg-green-500/10">
                                                <Icon className="size-4 text-green-600" />
                                            </div>
                                            <div>
                                                <p className="text-sm font-medium">{formatCurrency(p.amount)}</p>
                                                <p className="text-xs text-muted-foreground">
                                                    {new Date(p.recorded_at).toLocaleDateString('da-DK', {
                                                        day: 'numeric',
                                                        month: 'short',
                                                        year: 'numeric',
                                                    })}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            {p.notes && (
                                                <span className="hidden max-w-[200px] truncate text-xs text-muted-foreground sm:inline">
                                                    {p.notes}
                                                </span>
                                            )}
                                            <Badge variant="secondary" className="text-xs font-normal">
                                                {p.method_label}
                                            </Badge>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </section>
            </div>
        </StudentLayout>
    );
}
