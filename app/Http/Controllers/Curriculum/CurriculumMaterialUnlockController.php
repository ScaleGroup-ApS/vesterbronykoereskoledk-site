<?php

namespace App\Http\Controllers\Curriculum;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurriculumMaterialUnlockController extends Controller
{
    public function __invoke(Request $request, Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);

        $request->validate([
            'materials' => ['present', 'array'],
            'materials.*.id' => ['required', 'integer'],
            'materials.*.unlock_at_lesson' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ($request->input('materials', []) as $item) {
            $media = $offer->getMedia('materials')->firstWhere('id', $item['id']);
            if ($media) {
                $lesson = filled($item['unlock_at_lesson']) ? (int) $item['unlock_at_lesson'] : null;
                $media->setCustomProperty('unlock_at_lesson', $lesson)->save();
            }
        }

        return back()->with('success', 'Materiale låsetrin opdateret.');
    }
}
