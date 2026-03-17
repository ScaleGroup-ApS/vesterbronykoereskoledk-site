<?php

namespace App\Http\Controllers\Student;

use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\StudentPageProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentLearnController extends Controller
{
    public function show(Request $request, Offer $offer, OfferModule $module, ?OfferPage $page = null): Response|RedirectResponse
    {
        $student = $request->user()->student;

        abort_unless($student, 404);

        $isEnrolled = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('offer_id', $offer->id)
            ->where('status', EnrollmentStatus::Completed)
            ->exists();

        abort_unless($isEnrolled, 403);

        if ($page === null || ! $page->exists) {
            $firstPage = $module->pages()->orderBy('sort_order')->first();

            if (! $firstPage) {
                abort(404);
            }

            return redirect()->route('student.learn.page', [$offer, $module, $firstPage]);
        }

        $modules = $offer->modules()->with('pages')->get()->map(function ($mod) {
            return [
                'id' => $mod->id,
                'title' => $mod->title,
                'sort_order' => $mod->sort_order,
                'pages' => $mod->pages->map(fn ($p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'sort_order' => $p->sort_order,
                ])->values()->all(),
            ];
        })->values()->all();

        $completedPageIds = $student->pageProgress()
            ->whereNotNull('completed_at')
            ->pluck('offer_page_id')
            ->all();

        $allPages = collect($modules)->flatMap(fn ($mod) => $mod['pages'])->values();
        $currentIndex = $allPages->search(fn ($p) => $p['id'] === $page->id);

        $prevPage = $currentIndex > 0 ? $allPages[$currentIndex - 1] : null;
        $nextPage = $currentIndex < $allPages->count() - 1 ? $allPages[$currentIndex + 1] : null;

        $page->load('quizQuestions');

        $attachments = $page->getMedia('attachments')->map(fn ($media) => [
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'size' => $media->human_readable_size,
            'url' => route('student.offers.materials.show', [$offer->id, $media->id]),
        ])->values()->all();

        $latestQuizAttempt = $student->quizAttempts()
            ->where('offer_page_id', $page->id)
            ->latest('attempted_at')
            ->first();

        return Inertia::render('student/learn/show', [
            'offer' => ['id' => $offer->id, 'name' => $offer->name],
            'module' => ['id' => $module->id, 'title' => $module->title],
            'modules' => $modules,
            'page' => array_merge($page->toArray(), [
                'quiz_questions' => $page->quizQuestions->map(fn ($q) => [
                    'id' => $q->id,
                    'question' => $q->question,
                    'options' => $q->options,
                    'correct_option' => $q->correct_option,
                    'explanation' => $q->explanation,
                    'sort_order' => $q->sort_order,
                ])->values()->all(),
                'attachments' => $attachments,
            ]),
            'completedPageIds' => $completedPageIds,
            'latestQuizAttempt' => $latestQuizAttempt ? [
                'answers' => $latestQuizAttempt->answers,
                'score' => $latestQuizAttempt->score,
                'total' => $latestQuizAttempt->total,
                'attempted_at' => $latestQuizAttempt->attempted_at->toIso8601String(),
            ] : null,
            'prevPage' => $prevPage,
            'nextPage' => $nextPage,
        ]);
    }

    public function markComplete(Request $request, Offer $offer, OfferModule $module, OfferPage $page): RedirectResponse
    {
        $student = $request->user()->student;

        abort_unless($student, 404);

        $isEnrolled = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('offer_id', $offer->id)
            ->where('status', EnrollmentStatus::Completed)
            ->exists();

        abort_unless($isEnrolled, 403);

        StudentPageProgress::updateOrCreate(
            ['student_id' => $student->id, 'offer_page_id' => $page->id],
            ['completed_at' => now()]
        );

        $allPagesWithModules = $offer->modules()->with('pages')->get()
            ->flatMap(fn ($mod) => $mod->pages->map(fn ($p) => ['page' => $p, 'module' => $mod]))
            ->values();

        $currentIndex = $allPagesWithModules->search(fn ($item) => $item['page']->id === $page->id);
        $nextItem = $currentIndex < $allPagesWithModules->count() - 1 ? $allPagesWithModules[$currentIndex + 1] : null;

        if ($nextItem) {
            return redirect()->route('student.learn.page', [$offer, $nextItem['module'], $nextItem['page']]);
        }

        return redirect()->route('student.learn.page', [$offer, $module, $page]);
    }
}
