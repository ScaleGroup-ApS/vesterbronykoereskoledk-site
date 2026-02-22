import { Head, Link, usePage } from '@inertiajs/react';
import { dashboard, login } from '@/routes';
import { CheckCircle2, Car, ShieldCheck, Clock, MapPin, Phone, Mail, ArrowRight } from 'lucide-react';

interface Offer {
    id: number;
    name: string;
    description: string | null;
    price: string;
    type: 'primary' | 'addon';
    theory_lessons: number;
    driving_lessons: number;
    track_required: boolean;
    slippery_required: boolean;
}

export default function Welcome({ offers = [] }: { offers?: Offer[] }) {
    const { auth } = usePage().props;

    return (
        <div className="min-h-screen bg-background text-foreground selection:bg-primary selection:text-primary-foreground font-sans">
            <Head title="Welcome | Driving School" />

            {/* Navbar */}
            <header className="sticky top-0 z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                <div className="container mx-auto flex h-16 items-center justify-between px-4 lg:px-8">
                    <div className="flex items-center gap-2">
                        <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                            <Car size={20} />
                        </div>
                        <span className="text-xl font-bold tracking-tight">Køreskole Pro</span>
                    </div>

                    <nav className="hidden md:flex flex-1 items-center justify-center gap-8 text-sm font-medium">
                        <a href="#features" className="text-muted-foreground hover:text-primary transition-colors">Features</a>
                        <a href="#packages" className="text-muted-foreground hover:text-primary transition-colors">Packages</a>
                        <a href="#about" className="text-muted-foreground hover:text-primary transition-colors">About Us</a>
                        <a href="#contact" className="text-muted-foreground hover:text-primary transition-colors">Contact</a>
                    </nav>

                    <div className="flex items-center gap-4">
                        {auth.user ? (
                            <Link
                                href={dashboard()}
                                className="inline-flex h-9 items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            >
                                Dashboard
                            </Link>
                        ) : (
                            <Link
                                href={login()}
                                className="inline-flex h-9 items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            >
                                Log in
                            </Link>
                        )}
                    </div>
                </div>
            </header>

            <main>
                {/* Hero Section */}
                <section className="relative overflow-hidden pt-24 pb-32 lg:pt-36 lg:pb-40">
                    <div className="absolute inset-0 bg-gradient-to-br from-primary/5 via-background to-accent/5 -z-10" />
                    <div className="container mx-auto px-4 lg:px-8 flex flex-col items-center text-center">
                        <h1 className="text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl lg:text-7xl">
                            Master the Road with <br />
                            <span className="text-primary">Confidence</span>
                        </h1>
                        <p className="mt-6 max-w-[600px] text-lg text-muted-foreground sm:text-xl">
                            Expert instructors, modern vehicles, and a curriculum designed to make you a safe, confident driver. Start your journey today.
                        </p>
                        <div className="mt-10 flex flex-col sm:flex-row gap-4 flex-wrap justify-center">
                            <a href="#packages" className="inline-flex h-12 w-full sm:w-auto items-center justify-center rounded-md bg-primary px-8 text-base font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90">
                                View Packages
                            </a>
                            <a href="#features" className="inline-flex h-12 w-full sm:w-auto items-center justify-center rounded-md border border-input bg-background px-8 text-base font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground hover:border-accent">
                                Learn More
                            </a>
                        </div>
                    </div>
                </section>

                {/* Features Section */}
                <section id="features" className="py-24 bg-card">
                    <div className="container mx-auto px-4 lg:px-8">
                        <div className="text-center mb-16">
                            <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">Why Choose Us?</h2>
                            <p className="mt-4 text-lg text-muted-foreground">Everything you need to succeed on your driving test and beyond.</p>
                        </div>
                        <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                            <div className="flex flex-col items-center text-center p-6 rounded-2xl bg-background border border-border/50 shadow-sm transition-all hover:shadow-md hover:border-primary/20 hover:-translate-y-1">
                                <div className="h-14 w-14 rounded-full bg-primary/10 flex items-center justify-center mb-6 text-primary">
                                    <ShieldCheck size={28} />
                                </div>
                                <h3 className="text-xl font-bold mb-3">High Pass Rate</h3>
                                <p className="text-muted-foreground">Our tailored instruction methods ensure you are fully prepared to pass both theory and practical exams.</p>
                            </div>
                            <div className="flex flex-col items-center text-center p-6 rounded-2xl bg-background border border-border/50 shadow-sm transition-all hover:shadow-md hover:border-primary/20 hover:-translate-y-1">
                                <div className="h-14 w-14 rounded-full bg-accent/10 flex items-center justify-center mb-6 text-accent">
                                    <Car size={28} />
                                </div>
                                <h3 className="text-xl font-bold mb-3">Modern Vehicles</h3>
                                <p className="text-muted-foreground">Learn in safe, easy-to-drive, eco-friendly vehicles equipped with the latest safety features.</p>
                            </div>
                            <div className="flex flex-col items-center text-center p-6 rounded-2xl bg-background border border-border/50 shadow-sm transition-all hover:shadow-md hover:border-primary/20 hover:-translate-y-1">
                                <div className="h-14 w-14 rounded-full bg-primary/10 flex items-center justify-center mb-6 text-primary">
                                    <Clock size={28} />
                                </div>
                                <h3 className="text-xl font-bold mb-3">Flexible Scheduling</h3>
                                <p className="text-muted-foreground">Book your driving and theory lessons at times that suit your busy lifestyle via our online portal.</p>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Packages Section */}
                <section id="packages" className="py-24 bg-background">
                    <div className="container mx-auto px-4 lg:px-8">
                        <div className="text-center mb-16">
                            <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">Our Packages</h2>
                            <p className="mt-4 text-lg text-muted-foreground">Transparent pricing with no hidden fees.</p>
                        </div>
                        <div className="grid gap-8 md:grid-cols-2 max-w-5xl mx-auto">
                            {offers.length > 0 ? offers.map((offer) => (
                                <div key={offer.id} className="relative flex flex-col p-8 rounded-3xl border border-border bg-card shadow-sm hover:shadow-lg transition-shadow">
                                    {offer.type === 'primary' && (
                                        <div className="absolute top-0 right-8 -translate-y-1/2 rounded-full bg-primary px-4 py-1 text-sm font-semibold text-primary-foreground inline-flex items-center gap-1">
                                            Most Popular
                                        </div>
                                    )}
                                    <div className="mb-6">
                                        <h3 className="text-2xl font-bold">{offer.name}</h3>
                                        <p className="text-muted-foreground mt-2">{offer.description}</p>
                                        <div className="mt-6 flex items-baseline text-5xl font-extrabold pb-2">
                                            {Number(offer.price).toLocaleString('da-DK')}
                                            <span className="ml-1 text-xl font-medium text-muted-foreground">DKK</span>
                                        </div>
                                        {offer.type === 'addon' && <p className="text-sm text-muted-foreground mt-2">Extra addon package</p>}
                                    </div>
                                    <ul className="mb-8 space-y-4 flex-1">
                                        {offer.theory_lessons > 0 && (
                                            <li className="flex items-center gap-3">
                                                <CheckCircle2 className="text-primary h-5 w-5 shrink-0" />
                                                <span>{offer.theory_lessons} Theory Lessons</span>
                                            </li>
                                        )}
                                        {offer.driving_lessons > 0 && (
                                            <li className="flex items-center gap-3">
                                                <CheckCircle2 className="text-primary h-5 w-5 shrink-0" />
                                                <span>{offer.driving_lessons} Driving Lessons (45 min)</span>
                                            </li>
                                        )}
                                        {offer.track_required && (
                                            <li className="flex items-center gap-3">
                                                <CheckCircle2 className="text-primary h-5 w-5 shrink-0" />
                                                <span>Track Driving (Maneuver Track)</span>
                                            </li>
                                        )}
                                        {offer.slippery_required && (
                                            <li className="flex items-center gap-3">
                                                <CheckCircle2 className="text-primary h-5 w-5 shrink-0" />
                                                <span>Slippery Track Course</span>
                                            </li>
                                        )}
                                    </ul>
                                    {offer.type === 'primary' ? (
                                        <Link href={String(login())} className="mt-auto inline-flex h-12 w-full items-center justify-center rounded-xl px-6 text-base font-medium transition-colors bg-primary text-primary-foreground hover:bg-primary/90">
                                            Sign Up Now <ArrowRight className="ml-2 h-5 w-5" />
                                        </Link>
                                    ) : (
                                        <a href="#contact" className="mt-auto inline-flex h-12 w-full items-center justify-center rounded-xl px-6 text-base font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground hover:border-accent">
                                            Contact Us
                                        </a>
                                    )}
                                </div>
                            )) : (
                                <div className="col-span-full text-center text-muted-foreground py-12">
                                    No packages currently available. Please check back later.
                                </div>
                            )}
                        </div>
                    </div>
                </section>

                {/* CTA Section */}
                <section className="py-24 bg-primary text-primary-foreground relative overflow-hidden">
                    <div className="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white to-transparent" />
                    <div className="container relative mx-auto px-4 lg:px-8 text-center max-w-3xl">
                        <h2 className="text-3xl font-bold tracking-tight sm:text-4xl mb-6">Ready to get Behind the Wheel?</h2>
                        <p className="text-lg text-primary-foreground/80 mb-10">Join hundreds of successful students who passed their driving tests with us. Start your structured learning journey today.</p>
                        <Link href={login()} className="inline-flex h-14 items-center justify-center rounded-full bg-white px-8 text-lg font-bold text-primary shadow-lg transition-transform hover:scale-105 active:scale-95">
                            Get Started Now
                        </Link>
                    </div>
                </section>
            </main>

            {/* Footer */}
            <footer id="contact" className="border-t border-border bg-card py-12">
                <div className="container mx-auto px-4 lg:px-8 grid gap-8 md:grid-cols-3">
                    <div>
                        <div className="flex items-center gap-2 mb-6">
                            <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                                <Car size={20} />
                            </div>
                            <span className="text-xl font-bold tracking-tight">Køreskole Pro</span>
                        </div>
                        <p className="text-muted-foreground text-sm max-w-xs">Connecting eager learners with expert instructors. We make driving easy, safe, and fun.</p>
                    </div>
                    <div>
                        <h4 className="font-semibold mb-6">Contact Us</h4>
                        <ul className="space-y-4 text-sm text-muted-foreground">
                            <li className="flex items-center gap-3">
                                <MapPin size={16} className="text-primary" />
                                <span>123 Driver Lane, Copenhagen</span>
                            </li>
                            <li className="flex items-center gap-3">
                                <Phone size={16} className="text-primary" />
                                <span>+45 12 34 56 78</span>
                            </li>
                            <li className="flex items-center gap-3">
                                <Mail size={16} className="text-primary" />
                                <span>hello@koereskole-pro.dk</span>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 className="font-semibold mb-6">Legal</h4>
                        <ul className="space-y-4 text-sm text-muted-foreground">
                            <li><a href="#" className="hover:text-primary transition-colors">Terms of Service</a></li>
                            <li><a href="#" className="hover:text-primary transition-colors">Privacy Policy</a></li>
                            <li><a href="#" className="hover:text-primary transition-colors">Cookie Policy</a></li>
                        </ul>
                    </div>
                </div>
                <div className="container mx-auto px-4 lg:px-8 mt-12 pt-8 border-t border-border text-center text-sm text-muted-foreground">
                    &copy; {new Date().getFullYear()} Køreskole Pro. All rights reserved.
                </div>
            </footer>
        </div>
    );
}
