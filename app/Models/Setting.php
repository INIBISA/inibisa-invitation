<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public const KEY_ACTIVE_PAYMENT_METHOD = 'active_payment_method';

    protected $fillable = ['key', 'value'];

    public static function activePaymentMethod(): string
    {
        return self::query()->where('key', self::KEY_ACTIVE_PAYMENT_METHOD)->value('value') ?: Payment::METHOD_MANUAL_TRANSFER;
    }
}
