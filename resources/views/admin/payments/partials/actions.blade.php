<div class="table-actions">
    @if ($payment->payment_method === 'manual_transfer' && $payment->status === 'waiting_approval')
        <form method="POST" action="{{ route('admin.payments.update', $payment) }}" data-confirm="Setujui pembayaran ini?">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="paid">
            <button class="button success small" type="submit">Setujui</button>
        </form>
        <button class="button danger small" type="button" data-reject-payment="{{ $payment->id }}">Tolak</button>
        <dialog class="app-dialog reject-dialog" data-reject-dialog="{{ $payment->id }}">
            <form method="POST" action="{{ route('admin.payments.update', $payment) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <h2>Alasan Penolakan</h2>
                <p>Pelanggan akan melihat alasan ini dan dapat mengunggah ulang bukti.</p>
                <textarea class="input" name="rejection_reason" rows="4" maxlength="1000" required placeholder="Jelaskan alasan penolakan"></textarea>
                <div class="actions"><button class="button secondary" type="button" data-dialog-close>Batal</button><button class="button danger" type="submit">Tolak Pembayaran</button></div>
            </form>
        </dialog>
    @else
        <span class="muted">Selesai</span>
    @endif
</div>
