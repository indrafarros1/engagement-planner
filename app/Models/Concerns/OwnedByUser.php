<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Isolasi data per owner (FIX KRITIS).
 *
 * Semua tabel data dimiliki User (owner). Global scope otomatis membatasi
 * query ke data milik pemilik yang sedang login:
 *   - Owner  → data milik dirinya (user_id = auth id)
 *   - Partner → data milik owner yang menaunginya (user_id = owner_id-nya)
 * Data yang dibuat lewat model otomatis di-assign ke owner yang tepat.
 */
trait OwnedByUser
{
    public static function bootOwnedByUser(): void
    {
        static::addGlobalScope('owner', function (Builder $builder) {
            $user = auth()->user();
            if (! $user) {
                return; // CLI/console tanpa login → tanpa pembatasan
            }

            $ownerId = $user->isOwner() ? $user->getKey() : $user->owner_id;

            if (! $ownerId) {
                // Pasangan tanpa owner terhubung → tidak melihat data apa pun
                $builder->whereRaw('1 = 0');
                return;
            }

            $table = $builder->getModel()->getTable();
            $builder->where("{$table}.user_id", $ownerId);
        });

        // assign owner otomatis saat create melalui model
        static::creating(function (Model $model) {
            if (! is_null($model->getAttribute('user_id')) && $model->getAttribute('user_id') !== '') {
                return; // sudah diisi eksplisit
            }

            $user = auth()->user();
            if (! $user) {
                return;
            }

            $model->setAttribute('user_id', $user->isOwner() ? $user->getKey() : $user->owner_id);
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
