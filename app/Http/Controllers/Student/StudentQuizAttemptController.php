<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreQuizAttemptRequest;
use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\StudentQuizAttempt;
use Illuminate\Http\RedirectResponse;

class StudentQuizAttemptController extends Controller
{
    public function store(StoreQuizAttemptRequest $request, Offer $offer, OfferModule $module, OfferPage $page): RedirectResponse
    {
        $this->authorize('learnContent', $offer);

        $student = $request->user()->student;

        $answers = $request->validated('answers');
        $questions = $page->quizQuestions()->orderBy('sort_order')->get();

        $score = 0;

        foreach ($questions as $index => $question) {
            if (isset($answers[$index]) && (int) $answers[$index] === $question->correct_option) {
                $score++;
            }
        }

        StudentQuizAttempt::create([
            'student_id' => $student->id,
            'offer_page_id' => $page->id,
            'answers' => $answers,
            'score' => $score,
            'total' => $questions->count(),
            'attempted_at' => now(),
        ]);

        return redirect()->back();
    }
}
