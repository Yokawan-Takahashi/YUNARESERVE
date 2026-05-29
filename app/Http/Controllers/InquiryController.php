<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name'  => 'required|string|max:100',
            'contact_name'  => 'required|string|max:50',
            'email'         => 'required|email|max:200',
            'phone'         => 'nullable|string|max:20',
            'industry'      => 'nullable|string|max:50',
            'message'       => 'nullable|string|max:1000',
        ]);

        $inquiry = Inquiry::create($data);

        // 運営へ通知メール（失敗しても登録は通す）
        try {
            Mail::send(
                'emails.inquiry.received',
                ['inquiry' => $inquiry],
                fn ($m) => $m
                    ->to(config('mail.from.address'))
                    ->subject('【YUNARI RESERVE】新しいお問い合わせ：' . $inquiry->company_name)
            );
        } catch (\Exception) {
        }

        return redirect()->route('lp')->with('inquiry_sent', true);
    }
}
