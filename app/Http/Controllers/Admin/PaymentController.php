<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewPaymentRequest;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::query()->with(['user', 'template', 'approver'])
            ->when($request->filled('method'), fn ($query) => $query->where('payment_method', $request->string('method')->toString()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('customer'), fn ($query) => $query->whereHas('user', fn ($users) => $users->where('name', 'like', '%'.$request->string('customer')->toString().'%')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->latest()->paginate(30)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function update(ReviewPaymentRequest $request, Payment $payment): RedirectResponse
    {
        $status = $request->string('status')->toString();
        DB::transaction(function () use ($payment, $status, $request): void {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            abort_unless($payment->payment_method === Payment::METHOD_MANUAL_TRANSFER && $payment->status === Payment::STATUS_WAITING_APPROVAL && $payment->proof_path, 422);
            $payment->update([
                'status' => $status,
                'rejection_reason' => $status === Payment::STATUS_REJECTED ? $request->string('rejection_reason')->trim()->toString() : null,
                'approved_by' => $status === Payment::STATUS_PAID ? $request->user()->id : null,
                'approved_at' => $status === Payment::STATUS_PAID ? now() : null,
                'paid_at' => $status === Payment::STATUS_PAID ? now() : null,
            ]);
        });

        return back()->with('success', 'Status pembayaran diperbarui.');
    }
}
