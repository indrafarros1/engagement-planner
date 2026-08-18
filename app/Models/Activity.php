<?php

namespace App\Models;

use App\Enums\ActivityCategory;
use App\Enums\ActivityStatus;
use App\Enums\Payer;
use App\Enums\Priority;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\OwnedByUser;

class Activity extends Model
{
    use OwnedByUser;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'category' => ActivityCategory::class,
            'pic' => Payer::class,
            'priority' => Priority::class,
            'status' => ActivityStatus::class,
            'due_date' => 'date',
            'archived' => 'boolean',
        ];
    }

    /** Benar jika melewati deadline & belum selesai. */
    public function isOverdue(): bool
    {
        return $this->status !== ActivityStatus::Done
            && $this->due_date !== null
            && $this->due_date->isBefore(today());
    }

    public function isDueSoon(int $days = 7): bool
    {
        return $this->status !== ActivityStatus::Done
            && $this->due_date !== null
            && $this->due_date->gte(today())
            && $this->due_date->lte(today()->addDays($days));
    }
}
