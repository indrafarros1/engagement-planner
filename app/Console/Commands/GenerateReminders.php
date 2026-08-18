<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;

/**
 * Membuat notifikasi pengingat (reminder) untuk jatuh tempo pembayaran &
 * kegiatan, PER OWNER (data terisolasi per user).
 * Dijalankan harian via scheduler (Fase 2 Reminder + Fase 3 Notifikasi).
 */
class GenerateReminders extends Command
{
    protected $signature = 'reminders:generate';

    protected $description = 'Cek jatuh tempo & buat notifikasi pengingat per owner';

    public function handle(): int
    {
        $created = 0;

        $owners = User::where('role', 'owner')->get();

        foreach ($owners as $owner) {
            $created += $this->processOwner($owner);
        }

        $this->info("Notifikasi reminder dibuat: {$created}");

        return self::SUCCESS;
    }

    protected function processOwner(User $owner): int
    {
        $created = 0;
        $ownerId = $owner->id;

        // Pembayaran jatuh tempo hari ini / terlambat (milik owner ini)
        $payments = Payment::where('user_id', $ownerId)
            ->where('cancelled', false)
            ->with('budgetItem')
            ->whereNotNull('due_date')
            ->get();

        foreach ($payments as $p) {
            $dueToday = $p->due_date?->isToday() && $p->status() !== \App\Enums\PaymentStatus::Paid;
            $isOverdue = $p->status() === \App\Enums\PaymentStatus::Overdue;

            if ($dueToday) {
                $created += $this->notify($owner,
                    'Pembayaran jatuh tempo hari ini',
                    "{$p->budgetItem?->name} · {$p->type?->label()} Rp " . number_format($p->amount, 0, ',', '.'),
                    route('filament.admin.resources.payments.edit', ['record' => $p])
                );
            } elseif ($isOverdue) {
                $created += $this->notify($owner,
                    'Pembayaran terlambat',
                    "{$p->budgetItem?->name} · {$p->type?->label()} lewat jatuh tempo " . $p->due_date?->translatedFormat('d M Y'),
                    route('filament.admin.resources.payments.edit', ['record' => $p])
                );
            }
        }

        // Kegiatan deadline hari ini / sudah lewat (milik owner ini)
        $activities = Activity::where('user_id', $ownerId)
            ->where('archived', false)
            ->where('status', '!=', \App\Enums\ActivityStatus::Done->value)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', today())
            ->get();

        foreach ($activities as $a) {
            $label = $a->due_date->isBefore(today()) ? 'Kegiatan terlambat' : 'Deadline kegiatan hari ini';
            $created += $this->notify($owner,
                $label,
                $a->name . ' · jatuh tempo ' . $a->due_date->translatedFormat('d M Y'),
                route('filament.admin.resources.activities.edit', ['record' => $a])
            );
        }

        return $created;
    }

    protected function notify(User $user, string $title, string $body, string $url): int
    {
        // hindari duplikat reminder yang belum dibaca untuk kombinasi yang sama
        $exists = DatabaseNotification::where('notifiable_id', $user->id)
            ->where('notifiable_type', $user->getMorphClass())
            ->where('read_at', null)
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.title')) = ?", [$title])
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.body')) = ?", [$body])
            ->exists();

        if ($exists) {
            return 0;
        }

        $user->notifications()->create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'type' => 'App\\Notifications\\ReminderNotification',
            'data' => ['title' => $title, 'body' => $body, 'url' => $url],
        ]);

        return 1;
    }
}
