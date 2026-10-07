<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = $request->user()->payments()->with('template')->latest()->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function create(Template $template): View|RedirectResponse
    {
        abort_unless($template->is_active, 404);
        $payment = auth()->user()->payments()->whereBelongsTo($template)->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_WAITING_APPROVAL, Payment::STATUS_REJECTED])->latest()->first();

        if ($payment) {
            return redirect()->route('payments.show', $payment);
        }

        $bank = Setting::query()->where('key', 'manual_transfer')->value('value');
        $activePaymentMethod = Setting::activePaymentMethod();

        return view('payments.create', compact('template', 'bank', 'activePaymentMethod'));
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $template = Template::query()->where('is_active', true)->findOrFail($request->integer('template_id'));
        $activePaymentMethod = Setting::activePaymentMethod();
        $payment = DB::transaction(function () use ($request, $template, $activePaymentMethod): Payment {
            $request->user()->newQuery()->whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $existing = $request->user()->payments()->whereBelongsTo($template)
                ->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_WAITING_APPROVAL, Payment::STATUS_REJECTED])
                ->lockForUpdate()->latest('id')->first();

            return $existing ?: $request->user()->payments()->create([
                'template_id' => $template->id,
                'payment_method' => $activePaymentMethod,
                'amount' => $template->price,
                'transaction_id' => (string) Str::uuid(),
                'note' => $request->string('note')->trim()->toString() ?: null,
            ]);
        });

        if ($payment->payment_method === Payment::METHOD_MIDTRANS && ! $payment->payment_reference) {
            abort_unless(config('payments.midtrans.server_key'), 503, 'Midtrans belum dikonfigurasi.');
            $url = config('payments.midtrans.is_production') ? 'https://app.midtrans.com/snap/v1/transactions' : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
            $response = Http::withBasicAuth(config('payments.midtrans.server_key'), '')->connectTimeout(3)->timeout(10)->post($url, [
                'transaction_details' => ['order_id' => $payment->transaction_id, 'gross_amount' => $payment->amount],
                'customer_details' => ['first_name' => $request->user()->name, 'email' => $request->user()->email],
            ])->throw();
            abort_unless($response->json('token'), 502, 'Token pembayaran tidak tersedia.');
            $payment->update(['payment_reference' => $response->json('token')]);
        }

        return redirect()->route('payments.show', $payment);
    }

    public function show(Request $request, Payment $payment): View
    {
        abort_unless($payment->user_id === $request->user()->id, 403);
        $payment->load('template');
        $bank = Setting::query()->where('key', 'manual_transfer')->value('value');

        return view('payments.show', compact('payment', 'bank'));
    }

    public function uploadProof(Request $request, Payment $payment): RedirectResponse
    {
        abort_unless($payment->user_id === $request->user()->id && $payment->payment_method === Payment::METHOD_MANUAL_TRANSFER, 403);
        abort_unless(in_array($payment->status, [Payment::STATUS_PENDING, Payment::STATUS_REJECTED], true), 422);
        $request->validate(['proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']], [
            'proof.max' => 'Ukuran bukti transfer maksimal 5 MB.',
            'proof.mimes' => 'Bukti transfer harus berupa JPG, PNG, atau WebP.',
        ]);
        $path = $request->file('proof')->store('payment-proofs', 'local');
        $oldPath = DB::transaction(function () use ($payment, $path): ?string {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            abort_unless(in_array($payment->status, [Payment::STATUS_PENDING, Payment::STATUS_REJECTED], true), 422);
            $oldPath = $payment->proof_path;
            $payment->update(['proof_path' => $path, 'status' => Payment::STATUS_WAITING_APPROVAL, 'rejection_reason' => null]);

            return $oldPath;
        });

        if ($oldPath && Storage::disk('local')->exists($oldPath)) {
            Storage::disk('local')->delete($oldPath);
        }

        return back()->with('success', 'Bukti pembayaran dikirim untuk diverifikasi.');
    }

    public function proof(Request $request, Payment $payment): StreamedResponse
    {
        abort_unless($request->user()->isAdmin() || $payment->user_id === $request->user()->id, 403);
        abort_unless($payment->proof_path, 404);
        $disk = Storage::disk('local')->exists($payment->proof_path) ? 'local' : 'public';

        return Storage::disk($disk)->response($payment->proof_path);
    }
}
