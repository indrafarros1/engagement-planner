<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAttachment extends Model
{
    protected $guarded = [];

    public function payment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}