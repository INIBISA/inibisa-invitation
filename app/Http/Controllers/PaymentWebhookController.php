<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $payload = $request->validate([
            'order_id' => ['required', 'string'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required'],
            'signature_key' => ['required', 'string'],
            'transaction_status' => ['required', 'string'],
            'fraud_status' => ['nullable', 'string'],
        ]);
        $signature = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('payments.midtrans.server_key'));
        abort_unless(config('payments.midtrans.server_key') && hash_equals($signature, $payload['signature_key']), 403);

        DB::transaction(function () use ($payload): void {
            $payment = Payment::query()->where('transaction_id', $payload['order_id'])->lockForUpdate()->firstOrFail();
            abort_unless($payment->payment_method === Payment::METHOD_MIDTRANS && (float) $payload['gross_amount'] === (float) $payment->amount, 422);
            $status = match ($payload['transaction_status']) {
                'settlement' => Payment::STATUS_PAID,
                'capture' => ($payload['fraud_status'] ?? 'accept') === 'accept' ? Payment::STATUS_PAID : Payment::STATUS_PENDING,
                'pending' => Payment::STATUS_PENDING,
                'expire' => Payment::STATUS_EXPIRED,
                'cancel' => Payment::STATUS_CANCELLED,
                'deny', 'failure' => Payment::STATUS_FAILED,
                default => null,
            };

            abort_unless($status, 422);

            if ($payment->status === Payment::STATUS_PAID || ($payment->status !== Payment::STATUS_PENDING && $status === Payment::STATUS_PENDING)) {
                return;
            }

            $payment->update(['status' => $status, 'paid_at' => $status === Payment::STATUS_PAID ? now() : $payment->paid_at]);
        }, attempts: 3);

        return response()->noContent();
    }
}
