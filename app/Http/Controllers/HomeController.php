<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Offer;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
class HomeController extends Controller
{
    public function index(Request $request)
    {
        $referralCode = $request->query('ref');
        if ($referralCode) {
            session(['referral_code' => $referralCode]);
        }

$products = Product::with(['images', 'colors'])->orderBy('id', 'desc')->get();
        $trendyProducts = Product::where('status', 'active')
            ->whereNotNull('trend_type')
            ->orderByRaw("CASE
                WHEN trend_type = 'best-seller' THEN 1
                WHEN trend_type = 'hot-trend' THEN 2
                WHEN trend_type = 'featured' THEN 3
                ELSE 4
            END")
            ->limit(8)
            ->get();

        // Fetch offers
        try {
            $offers = Offer::where('active', true)->get();
        } catch (\Exception $e) {
            $offers = collect();
        }

        return view('index', compact('products', 'trendyProducts', 'offers'));
    }

   
    public function contact()
    {
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            Mail::to(config('mail.from.address'))->send(new ContactMail($validated));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Contact form email failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');

    }

}
