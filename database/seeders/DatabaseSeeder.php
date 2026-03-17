<?php

namespace Database\Seeders;

use App\Enums\OfferType;
use App\Enums\UserRole;
use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\OfferPageQuizQuestion;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test Bruger',
            'email' => 'test@koreskole.dk',
            'role' => UserRole::Admin,
        ]);

        $this->seedOffers();
        $this->seedModules();
    }

    private function seedOffers(): void
    {
        $packages = [
            [
                'name' => 'Normal lovpakke',
                'description' => "Ubegrænset teori\nManøvrebane\nGlatbane\n16 kørelektioner",
                'price' => 15500.00,
                'type' => OfferType::Primary,
                'theory_lessons' => 29,
                'driving_lessons' => 16,
                'track_required' => true,
                'slippery_required' => true,
                'image_url' => 'https://picsum.photos/seed/normal/800/600',
            ],
            [
                'name' => 'Studenterpakken',
                'description' => "Ubegrænset teori\nManøvrebane\nGlatbane\nKør aften og weekend og når det passer dig.\nFørstehjælp\nOnline teori (Gælder i 4 måneder)",
                'price' => 14500.00,
                'type' => OfferType::Primary,
                'theory_lessons' => 29,
                'driving_lessons' => 16,
                'track_required' => true,
                'slippery_required' => true,
                'image_url' => 'https://picsum.photos/seed/student/800/600',
            ],
            [
                'name' => 'Lynpakken',
                'description' => "Ubegrænset teori\nManøvrebane\nGlatbane\n16 kørelektioner\nOnlineteori (Gælder i 4 måneder)",
                'price' => 17500.00,
                'type' => OfferType::Primary,
                'theory_lessons' => 29,
                'driving_lessons' => 16,
                'track_required' => true,
                'slippery_required' => true,
                'image_url' => 'https://picsum.photos/seed/lyn/800/600',
            ],
        ];

        foreach ($packages as $pkgData) {
            $imageUrl = $pkgData['image_url'];
            unset($pkgData['image_url']);

            $offer = Offer::create($pkgData);

            try {
                $offer->addMediaFromUrl($imageUrl)->toMediaCollection('default');
            } catch (\Exception $e) {
                // Ignore if download fails
            }
        }

        $addons = [
            ['name' => 'Manøvrebane', 'description' => 'Lukket øvelsesplads 4 lektioner a 45 min.', 'price' => 2400.00],
            ['name' => 'Glatbane', 'description' => 'Køretekniks anlæg 4 lektioner a 45 min.', 'price' => 2500.00],
            ['name' => 'Kørelektion på 45 minutter', 'description' => 'Kørelektion på offentlig vej', 'price' => 500.00],
            ['name' => 'Kørelektion weekend/aften', 'description' => 'Kørelektion på 45 minutter i weekend og efter kl. 17', 'price' => 550.00],
            ['name' => 'Førstehjælp', 'description' => 'Lovpligtigt førsthjælpskursus', 'price' => 800.00],
            ['name' => 'Praktisk køreprøve', 'description' => 'Inklusiv en opvarmnings lektion', 'price' => 1500.00],
            ['name' => 'Administrationsgebyr', 'description' => 'Vi skal stå med godkendelse af ansøgning og oprettelse. Samt booking af den 1. Teori prøve og 1.Køreprøve', 'price' => 200.00],
            ['name' => 'Beramning af prøve', 'description' => 'Hvis man ikke klare prøverne og der skal bookes flere prøver', 'price' => 100.00],
            ['name' => 'Prøvegebyr', 'description' => 'Betales cirka 5 uger frem i forløbet via www.koreprovebooking.dk', 'price' => 1450.00],
            ['name' => 'Prøvegebyr (generhverv)', 'description' => 'Prøvegebyr til færdselsstyrelsen ved generhverv', 'price' => 1510.00],
            ['name' => 'Teoribog', 'description' => 'Teoribog', 'price' => 395.00],
        ];

        foreach ($addons as $addonData) {
            Offer::create([
                'name' => $addonData['name'],
                'description' => $addonData['description'],
                'price' => $addonData['price'],
                'type' => OfferType::Addon,
                'theory_lessons' => 0,
                'driving_lessons' => 0,
                'track_required' => false,
                'slippery_required' => false,
            ]);
        }
    }

    private function seedModules(): void
    {
        $offer = Offer::where('type', OfferType::Primary)->first();

        if (! $offer) {
            return;
        }

        $modules = [
            [
                'title' => 'Introduktion til kørekortet',
                'pages' => [
                    [
                        'title' => 'Velkommen til kurset',
                        'body' => '<p>Velkommen til dit køreuddannelsesforløb. I dette kursus gennemgår vi alt, hvad du skal vide for at bestå køreprøven og blive en sikker trafikant.</p><p>Du vil møde teori, øvelser og quizzer undervejs. Tag det i dit eget tempo.</p>',
                        'video_url' => null,
                    ],
                    [
                        'title' => 'Lovgivning og regler',
                        'body' => '<p>Færdselsloven er grundlaget for al trafik i Danmark. Det er vigtigt at kende de vigtigste regler:</p><ul><li>Vigepligt og forkørselsret</li><li>Hastighedsgrænser</li><li>Alkohol og spiritus</li></ul>',
                        'video_url' => null,
                        'quiz' => [
                            [
                                'question' => 'Hvad er den generelle hastighedsgrænse på motorvej i Danmark?',
                                'options' => ['100 km/t', '110 km/t', '120 km/t', '130 km/t'],
                                'correct_option' => 2,
                                'explanation' => 'Den generelle hastighedsgrænse på motorvej i Danmark er 120 km/t, medmindre andet er skiltet.',
                            ],
                            [
                                'question' => 'Hvad er promillegrænsen for nye bilister (under 2 år)?',
                                'options' => ['0,5 promille', '0,2 promille', '0,8 promille', '0,0 promille'],
                                'correct_option' => 1,
                                'explanation' => 'For nye bilister med kørekort under 2 år er promillegrænsen 0,2 promille.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Trafikantadfærd',
                'pages' => [
                    [
                        'title' => 'Kryds og vigepligt',
                        'body' => '<p>I kryds gælder særlige regler. Høj- og venstre-reglen siger, at du skal give vigepligt til køretøjer, der kommer fra højre.</p><p>Undtagelser gælder ved skilte og afmærkning på vejen.</p>',
                        'video_url' => null,
                        'quiz' => [
                            [
                                'question' => 'Hvem har forkørselsret i et kryds uden skilte?',
                                'options' => ['Den, der kører hurtigst', 'Den, der kommer fra højre', 'Den, der er på hovdvejen', 'Den, der kom først'],
                                'correct_option' => 1,
                                'explanation' => 'Uden skilte gælder højrereglen: du skal give vigepligt til trafikanter fra højre.',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Motorvejskørsel',
                        'body' => '<p>Motorvejskørsel kræver særlig opmærksomhed. Husk:</p><ul><li>Tjek spejle og blinde vinkler ved ind- og udkørsel</li><li>Hold afstand til forankørende</li><li>Overhaling foregår altid i venstre side</li></ul>',
                        'video_url' => null,
                    ],
                ],
            ],
            [
                'title' => 'Praktisk forberedelse',
                'pages' => [
                    [
                        'title' => 'Bilen og dens betjening',
                        'body' => '<p>Inden du kører, skal du tjekke:</p><ul><li>Spejle justeret korrekt</li><li>Sæde og rat i rigtig position</li><li>Sikkerhedssele spændt</li><li>Instrumenter og advarselslamper</li></ul>',
                        'video_url' => null,
                    ],
                    [
                        'title' => 'Køreprøven – hvad sker der?',
                        'body' => '<p>Køreprøven består af to dele:</p><ol><li><strong>Teoriprøven</strong> – 25 spørgsmål, max 5 fejl</li><li><strong>Praktisk prøve</strong> – ca. 45 minutters kørsel med en censor</li></ol><p>Vær rolig og kør, som du har lært det. Censoren vurderer din adfærd i trafikken.</p>',
                        'video_url' => null,
                        'quiz' => [
                            [
                                'question' => 'Hvor mange spørgsmål indeholder teoriprøven?',
                                'options' => ['20', '25', '30', '40'],
                                'correct_option' => 1,
                                'explanation' => 'Teoriprøven består af 25 spørgsmål. Du må højst lave 5 fejl for at bestå.',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($modules as $sortOrder => $moduleData) {
            $module = OfferModule::create([
                'offer_id' => $offer->id,
                'title' => $moduleData['title'],
                'sort_order' => $sortOrder + 1,
            ]);

            foreach ($moduleData['pages'] as $pageSort => $pageData) {
                $quizQuestions = $pageData['quiz'] ?? [];
                unset($pageData['quiz']);

                $page = OfferPage::create([
                    'offer_module_id' => $module->id,
                    'title' => $pageData['title'],
                    'body' => $pageData['body'],
                    'video_url' => $pageData['video_url'],
                    'sort_order' => $pageSort + 1,
                ]);

                foreach ($quizQuestions as $questionSort => $questionData) {
                    OfferPageQuizQuestion::create([
                        'offer_page_id' => $page->id,
                        'question' => $questionData['question'],
                        'options' => $questionData['options'],
                        'correct_option' => $questionData['correct_option'],
                        'explanation' => $questionData['explanation'] ?? null,
                        'sort_order' => $questionSort + 1,
                    ]);
                }
            }
        }
    }
}
