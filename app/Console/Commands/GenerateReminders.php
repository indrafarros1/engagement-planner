<?php

namespace App\Console\Commands;

use App\Enums\ActivityStatus;
use App\Enums\PaymentStatus;
use App\Models\Activity;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;

/**
 * Membuat notifikasi pengingat (reminder) untuk jatuh tempo pembayaran &
 * kegiatan. Dijalankan harian via scheduler (Fase 2 Reminder + Fase 3 Notifikasi).
 */
class GenerateReminders extends Command
{
    protected $signature = 'reminders:generate';

    protected $description = 'Cek jatuh tempo & buat notifikasi pengingat untuk semua pengguna';

    public function handle(): int
    {
        $created = 0;

        // Pembayaran yang jatuh tempo hari ini
        $dueToday = Payment::with('budgetItem')
            ->where('cancelled', false)
            ->whereDate('due_date', today())
            ->get()
            ->filter(fn ($p) => $p->status() !== PaymentStatus::Paid);

        foreach ($dueToday as $p) {
            $created += $this->notify(
                'Pembayaran jatuh tempo hari ini',
                "{$p->budgetItem?->name} · {$p->type?->label()} Rp " .
                number_format($p->amount, 0, ',', '.'),
                route('filament.admin.resources.payments.edit', ['record' => $p])
            );
        }

        // Pembayaran terlambat (belum lunas, lewat jatuh tempo)
        $overdue = Payment::with('budgetItem')
            ->where('cancelled', false)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->get()
            ->filter(fn ($p) => $p->status() === PaymentStatus::Overdue)
            ->take(10);

        foreach ($overdue as $p) {
            $created += $this->notify(
                'Pembayaran terlambat',
                "{$p->budgetItem?->name} · {$p->type?->label()} lewat jatuh tempo " .
                $p->due_date?->translatedFormat('d M Y'),
                route('filament.admin.resources.payments.edit', ['record' => $p])
            );
        }

        // Kegiatan deadline hari ini / sudah lewat
        $activityDue = Activity::where('archived', false)
            ->where('status', '!=', ActivityStatus::Done->value)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', today())
            ->get()
            ->take(10);

        foreach ($activityDue as $a) {
            $label = $a->due_date->isBefore(today()) ? 'Kegiatan terlambat' : 'Deadline kegiatan hari ini';
            $created += $this->notify(
                $label,
                $a->name . ' · jatuh tempo ' . $a->due_date->translatedFormat('d M Y'),
                route('filament.admin.resources.activities.edit', ['record' => $a])
            );
        }

        $this->info("Notifikasi reminder dibuat: {$created}");

        return self::SUCCESS;
    }

    protected function notify(string $title, string $body, string $url): int
    {
        $users = User::all();
        $count = 0;
        foreach ($users as $user) {
            // hindari duplikat reminder yang belum dibaca untuk kombinasi yang sama
            $exists = DatabaseNotification::where('notifiable_id', $user->id)
                ->where('notifiable_type', $user->getMorphClass())
                ->where('read_at', null)
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.title')) = ?", [$title])
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.body')) = ?", [$body])
                ->exists();

            if ($exists) {
                continue;
            }

            $user->notifications()->create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\ReminderNotification',
                'data' => ['title' => $title, 'body' => $body, 'url' => $url],
            ]);
            $count++;
        }

        return $count;
    }
}
