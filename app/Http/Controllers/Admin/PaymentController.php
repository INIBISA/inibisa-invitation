<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewPaymentRequest;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.payments.index', ['paymentCount' => Payment::query()->count()]);
    }

    public function data(Request $request): JsonResponse
    {
        $request->validate([
            'method' => ['nullable', 'in:midtrans,manual_transfer'],
            'status' => ['nullable', 'in:pending,waiting_approval,paid,rejected,failed,expired,cancelled'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $payments = Payment::query()->with(['user:id,name,email', 'template:id,name'])
            ->when($request->filled('method'), fn ($query) => $query->where('payment_method', $request->string('method')->toString()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('customer'), fn ($query) => $query->whereHas('user', fn ($users) => $users->where('name', 'like', '%'.$request->string('customer')->toString().'%')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->string('from')->toString()))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->string('to')->toString()));

        return DataTables::eloquent($payments)
            ->addIndexColumn()
            ->addColumn('customer', fn (Payment $payment): string => '<strong>'.e($payment->user->name).'</strong><small>'.e($payment->user->email).'</small>')
            ->addColumn('order', fn (Payment $payment): string => '<strong>'.e($payment->template->name).'</strong><small>Rp '.number_format($payment->amount, 0, ',', '.').'</small>')
            ->addColumn('method_label', fn (Payment $payment): string => $payment->methodLabel())
            ->addColumn('status_label', fn (Payment $payment): string => '<span class="status-badge '.e($payment->status).'"><i></i>'.e($payment->statusLabel()).'</span>')
            ->addColumn('date', fn (Payment $payment): string => '<span class="admin-date-cell">'.$payment->created_at->translatedFormat('d M Y').'<small>'.$payment->created_at->format('H:i').'</small></span>')
            ->addColumn('proof', fn (Payment $payment): string => $payment->proof_path ? '<button class="proof-thumb" type="button" data-image-preview="'.e(route('payments.proof.show', $payment)).'" aria-label="Lihat bukti transfer '.e($payment->user->name).'"><img src="'.e(route('payments.proof.show', $payment)).'" alt="Bukti transfer" loading="lazy"></button>' : '<span class="muted">Tidak ada</span>')
            ->addColumn('action', fn (Payment $payment): string => view('admin.payments.partials.actions', ['payment' => $payment])->render())
            ->filterColumn('customer', fn ($query, string $keyword) => $query->whereHas('user', fn ($query) => $query->where('name', 'like', "%{$keyword}%")->orWhere('email', 'like', "%{$keyword}%")))
            ->filterColumn('order', fn ($query, string $keyword) => $query->whereHas('template', fn ($query) => $query->where('name', 'like', "%{$keyword}%")))
            ->rawColumns(['customer', 'order', 'status_label', 'date', 'proof', 'action'])
            ->toJson();
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
