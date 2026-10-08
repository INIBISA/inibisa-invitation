<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    public const METHOD_MIDTRANS = 'midtrans';

    public const METHOD_MANUAL_TRANSFER = 'manual_transfer';

    public const STATUS_PENDING = 'pending';

    public const STATUS_WAITING_APPROVAL = 'waiting_approval';

    public const STATUS_PAID = 'paid';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_FAILED = 'failed';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = ['user_id', 'template_id', 'invitation_id', 'approved_by', 'payment_method', 'amount', 'status', 'transaction_id', 'payment_reference', 'proof_path', 'note', 'rejection_reason', 'paid_at', 'approved_at'];

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Tertunda',
            self::STATUS_WAITING_APPROVAL => 'Menunggu Persetujuan',
            self::STATUS_PAID => 'Lunas',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_FAILED => 'Gagal',
            self::STATUS_EXPIRED => 'Kedaluwarsa',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function methodLabel(): string
    {
        return $this->payment_method === self::METHOD_MIDTRANS ? 'Midtrans' : 'Transfer Manual';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected function casts(): array
    {
        return ['paid_at' => 'datetime', 'approved_at' => 'datetime'];
    }
}
