@extends('layouts.app')
@section('title', 'Detail Pembayaran')
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">Detail Pembayaran</p>
            <h1>{{ $payment->template->name }}</h1>
            <p>Pantau status transaksi dan selesaikan pembayaran Anda.</p>
        </div>
        <a class="button secondary" href="{{ route('payments.index') }}">Kembali ke Pembayaran</a>
    </section>

    <div class="payment-detail-grid">
        <section class="panel payment-detail-main">
            <div class="payment-detail-title">
                <div>
                    <p class="overline">Status Transaksi</p>
                    <h2>{{ $payment->payment_method === 'manual_transfer' ? 'Transfer Manual' : 'Pembayaran Midtrans' }}</h2>
                </div>
                <span class="status-badge {{ $payment->status }}"><i></i>{{ $payment->statusLabel() }}</span>
            </div>

            @if ($payment->status === 'paid')
                <div class="payment-state payment-state-success">
                    <span class="payment-state-icon">✓</span>
                    <div>
                        <h3>Pembayaran berhasil</h3>
                        <p>Template siap digunakan untuk membuat undangan Anda.</p>
                    </div>
                </div>
                <div class="payment-actions">
                    @if (!$payment->invitation)
                        <a class="button success"
                            href="{{ route('invitations.create', ['template_id' => $payment->template_id]) }}">Mulai Isi Undangan</a>
                    @else
                        <a class="button gold" href="{{ route('invitations.edit', $payment->invitation) }}">Kelola Undangan</a>
                    @endif
                </div>
            @elseif($payment->payment_method === 'manual_transfer')
                @if ($payment->status === 'waiting_approval')
                    <div class="payment-state payment-state-review">
                        <span class="payment-state-icon">⌛</span>
                        <div>
                            <h3>Menunggu verifikasi admin</h3>
                            <p>Bukti transfer sudah diterima. Akses pembuatan undangan terbuka setelah pembayaran disetujui.</p>
                        </div>
                    </div>
                @endif

                @if ($payment->rejection_reason)
                    <div class="alert error"><strong>Pembayaran ditolak.</strong> {{ $payment->rejection_reason }}</div>
                @endif

                <div class="payment-section">
                    <span class="payment-section-label">Rekening tujuan</span>
                    <p class="bank-instructions">{{ $bank ?: 'Rekening transfer belum dikonfigurasi admin.' }}</p>
                </div>

                @if ($payment->proof_path)
                    <div class="payment-section">
                        <span class="payment-section-label">Bukti transfer</span>
                        <a class="proof-current" href="{{ route('payments.proof.show', $payment) }}" target="_blank"
                            rel="noopener">
                            <img src="{{ route('payments.proof.show', $payment) }}" alt="Bukti transfer" loading="lazy">
                            <span>Lihat ukuran penuh</span>
                        </a>
                    </div>
                @endif

                @if (in_array($payment->status, ['pending', 'rejected']))
                    <form class="payment-section payment-upload" method="POST"
                        action="{{ route('payments.proof.store', $payment) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="field">
                            <label for="payment-proof">{{ $payment->proof_path ? 'Ganti bukti transfer' : 'Unggah bukti transfer' }}</label>
                            <div class="upload-zone" data-upload-zone>
                                <input id="payment-proof" type="file" name="proof" accept="image/jpeg,image/png,image/webp"
                                    data-max-mb="5" required>
                                <span class="upload-icon">↑</span>
                                <strong>Tarik file ke sini atau klik untuk memilih</strong>
                                <small>JPG, PNG, WebP · maksimal 5 MB</small>
                                <img data-upload-preview alt="Pratinjau bukti transfer" hidden>
                                <span data-upload-info></span>
                                <button class="button small secondary" type="button" data-upload-remove hidden>Hapus file</button>
                            </div>
                        </div>
                        <button class="button gold" type="submit">{{ $payment->status === 'rejected' ? 'Kirim Ulang Bukti' : 'Kirim Bukti Transfer' }}</button>
                    </form>
                @endif
            @elseif($payment->status === 'pending' && $payment->payment_reference)
                <div class="payment-state payment-state-review">
                    <span class="payment-state-icon">↗</span>
                    <div>
                        <h3>Selesaikan pembayaran</h3>
                        <p>Status diperbarui otomatis setelah pembayaran Midtrans berhasil.</p>
                    </div>
                </div>
                <div class="payment-actions">
                    <button class="button gold" type="button" data-midtrans-token="{{ $payment->payment_reference }}">Bayar Sekarang</button>
                </div>
                <script
                    src="{{ config('payments.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
                    data-client-key="{{ config('payments.midtrans.client_key') }}"></script>
            @elseif($payment->status === 'pending')
                <div class="payment-state payment-state-review">
                    <span class="payment-state-icon">!</span>
                    <div>
                        <h3>Transaksi sedang disiapkan</h3>
                        <p>Kembali ke checkout untuk mencoba membuat transaksi lagi.</p>
                    </div>
                </div>
                <div class="payment-actions">
                    <a class="button secondary" href="{{ route('payments.create', $payment->template) }}">Coba Lagi</a>
                </div>
            @else
                <div class="payment-state">
                    <span class="payment-state-icon">!</span>
                    <div>
                        <h3>Transaksi {{ str_replace('_', ' ', $payment->status) }}</h3>
                        <p>Pilih template untuk membuat transaksi baru.</p>
                    </div>
                </div>
                <div class="payment-actions">
                    <a class="button secondary" href="{{ route('templates.index') }}">Lihat Template</a>
                </div>
            @endif
        </section>

        <aside class="panel payment-summary">
            <img src="{{ asset($payment->template->thumbnail) }}" alt="{{ $payment->template->name }}">
            <div class="payment-summary-title">
                <span>Template</span>
                <h2>{{ $payment->template->name }}</h2>
            </div>
            <dl>
                <div><dt>Total pembayaran</dt><dd>Rp {{ number_format($payment->amount, 0, ',', '.') }}</dd></div>
                <div><dt>Metode</dt><dd>{{ $payment->methodLabel() }}</dd></div>
                <div><dt>Tanggal transaksi</dt><dd>{{ $payment->created_at->format('d M Y, H:i') }}</dd></div>
                <div><dt>ID transaksi</dt><dd class="payment-transaction-id">{{ $payment->transaction_id }}</dd></div>
            </dl>
        </aside>
    </div>
@endsection
