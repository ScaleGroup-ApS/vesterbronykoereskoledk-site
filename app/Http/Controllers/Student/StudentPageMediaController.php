<?php

namespace App\Http\Controllers\Student;

use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Offer;
use App\Models\OfferPage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentPageMediaController extends Controller
{
    public function __invoke(Offer $offer, OfferPage $page, Media $media): StreamedResponse
    {
        $student = auth()->user()->student;

        abort_unless($student, 404);

        $isEnrolled = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('offer_id', $offer->id)
            ->where('status', EnrollmentStatus::Completed)
            ->exists();

        abort_unless($isEnrolled, 403);

        abort_unless(
            $media->model_type === OfferPage::class
                && $media->model_id === $page->id
                && in_array($media->collection_name, ['attachments', 'images', 'video']),
            404
        );

        return $media->toInlineResponse($media->file_name);
    }
}
