<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\OwnedByUser;

class PaymentAttachment extends Model
{
    use OwnedByUser;
    protected $guarded = [];

    public function payment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
        ];
    }
}