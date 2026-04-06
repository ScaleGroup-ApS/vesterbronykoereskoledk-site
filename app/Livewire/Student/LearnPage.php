<?php

namespace App\Livewire\Student;

use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\StudentPageProgress;
use App\Models\StudentQuizAttempt;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class LearnPage extends Component
{
    #[Locked]
    public Offer $offer;

    #[Locked]
    public OfferModule $module;

    #[Locked]
    public OfferPage $page;

    /** @var array<int, int> */
    public array $answers = [];

    public bool $submitted = false;

    /** @var array{score: int, total: int, answers: array<int, int>}|null */
    public ?array $attempt = null;

    public function mount(Offer $offer, OfferModule $module, OfferPage $page): void
    {
        $this->authorize('learnContent', $offer);

        $this->offer = $offer;
        $this->module = $module;
        $this->page = $page->load('quizQuestions');

        $student = auth()->user()->student;

        $latestAttempt = StudentQuizAttempt::query()
            ->where('student_id', $student->id)
            ->where('offer_page_id', $page->id)
            ->latest('attempted_at')
            ->first();

        if ($latestAttempt) {
            $this->attempt = [
                'score' => $latestAttempt->score,
                'total' => $latestAttempt->total,
                'answers' => $latestAttempt->answers,
            ];
            $this->submitted = true;
        }
    }

    /**
     * @return list<int>
     */
    public function getCompletedPageIdsProperty(): array
    {
        return StudentPageProgress::query()
            ->where('student_id', auth()->user()->student->id)
            ->whereNotNull('completed_at')
            ->pluck('offer_page_id')
            ->all();
    }

    /**
     * @return list<array{id: int, title: string, sort_order: int, pages: list<array{id: int, title: string, sort_order: int}>}>
     */
    public function getModulesWithPagesProperty(): array
    {
        return $this->offer
            ->modules()
            ->with('pages')
            ->get()
            ->map(fn ($mod) => [
                'id' => $mod->id,
                'title' => $mod->title,
                'sort_order' => $mod->sort_order,
                'pages' => $mod->pages->map(fn ($p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'sort_order' => $p->sort_order,
                ])->values()->all(),
            ])
            ->values()
            ->all();
    }

    public function getImagesProperty(): array
    {
        return $this->page->getMedia('banner')->map(fn ($media) => [
            'id' => $media->id,
            'url' => route('student.offers.pages.media.show', [$this->offer, $this->page, $media]),
            'file_name' => $media->file_name,
        ])->values()->all();
    }

    public function getVideosProperty(): array
    {
        return $this->page->getMedia('video')->map(fn ($media) => [
            'id' => $media->id,
            'url' => route('student.offers.pages.media.show', [$this->offer, $this->page, $media]),
            'file_name' => $media->file_name,
            'thumbnail_url' => $media->hasGeneratedConversion('thumbnail')
                ? $media->getUrl('thumbnail')
                : null,
        ])->values()->all();
    }

    public function getAttachmentsProperty(): array
    {
        return $this->page->getMedia('attachments')->map(fn ($media) => [
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'size' => $media->human_readable_size,
            'url' => route('student.offers.pages.media.show', [$this->offer, $this->page, $media]),
        ])->values()->all();
    }

    /**
     * @return array{module: OfferModule, page: OfferPage}|null
     */
    private function getNextPageItem(): ?array
    {
        $allItems = $this->offer
            ->modules()
            ->with('pages')
            ->get()
            ->flatMap(fn ($mod) => $mod->pages->map(fn ($p) => ['module' => $mod, 'page' => $p]))
            ->values();

        $currentIndex = $allItems->search(fn ($item) => $item['page']->id === $this->page->id);

        if ($currentIndex === false || $currentIndex >= $allItems->count() - 1) {
            return null;
        }

        return $allItems[$currentIndex + 1];
    }

    /**
     * @return array{module: OfferModule, page: OfferPage}|null
     */
    private function getPrevPageItem(): ?array
    {
        $allItems = $this->offer
            ->modules()
            ->with('pages')
            ->get()
            ->flatMap(fn ($mod) => $mod->pages->map(fn ($p) => ['module' => $mod, 'page' => $p]))
            ->values();

        $currentIndex = $allItems->search(fn ($item) => $item['page']->id === $this->page->id);

        if ($currentIndex === false || $currentIndex === 0) {
            return null;
        }

        return $allItems[$currentIndex - 1];
    }

    public function submitQuiz(): void
    {
        $this->authorize('learnContent', $this->offer);

        $questions = $this->page->quizQuestions()->orderBy('sort_order')->get();

        $score = 0;

        foreach ($questions as $index => $question) {
            if (isset($this->answers[$index]) && (int) $this->answers[$index] === $question->correct_option) {
                $score++;
            }
        }

        $student = auth()->user()->student;

        StudentQuizAttempt::create([
            'student_id' => $student->id,
            'offer_page_id' => $this->page->id,
            'answers' => array_map('intval', $this->answers),
            'score' => $score,
            'total' => $questions->count(),
            'attempted_at' => now(),
        ]);

        $this->attempt = [
            'score' => $score,
            'total' => $questions->count(),
            'answers' => array_map('intval', $this->answers),
        ];

        $this->submitted = true;
    }

    public function retryQuiz(): void
    {
        $this->answers = [];
        $this->submitted = false;
        $this->attempt = null;
    }

    public function markComplete(): void
    {
        $this->authorize('learnContent', $this->offer);

        $student = auth()->user()->student;

        StudentPageProgress::updateOrCreate(
            ['student_id' => $student->id, 'offer_page_id' => $this->page->id],
            ['completed_at' => now()]
        );

        $nextItem = $this->getNextPageItem();

        if ($nextItem) {
            $this->redirect(route('student.learn.page', [
                'offer' => $this->offer,
                'module' => $nextItem['module'],
                'page' => $nextItem['page'],
            ]), navigate: true);

            return;
        }

        $this->redirect(route('student.learn.page', [
            'offer' => $this->offer,
            'module' => $this->module,
            'page' => $this->page,
        ]), navigate: true);
    }

    public function render(): View
    {
        $nextItem = $this->getNextPageItem();
        $prevItem = $this->getPrevPageItem();

        return view('livewire.student.learn-page', [
            'nextPageUrl' => $nextItem
                ? route('student.learn.page', ['offer' => $this->offer, 'module' => $nextItem['module'], 'page' => $nextItem['page']])
                : null,
            'prevPageUrl' => $prevItem
                ? route('student.learn.page', ['offer' => $this->offer, 'module' => $prevItem['module'], 'page' => $prevItem['page']])
                : null,
        ])->layout('components.layouts.app');
    }
}
