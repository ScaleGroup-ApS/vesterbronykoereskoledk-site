<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactInquiryRequest;
use App\Mail\ContactInquiryMail;
use App\Models\ContactInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactInquiryController extends Controller
{
    public function __invoke(StoreContactInquiryRequest $request): RedirectResponse
    {
        $inquiry = ContactInquiry::query()->create($request->validated());

        Mail::to(config('marketing.contact.email'))->send(new ContactInquiryMail($inquiry));

        return redirect()->route('marketing.contact')
            ->with('success', 'Tak for din henvendelse — vi vender tilbage hurtigst muligt.');
    }
}
