<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Profil';

    protected static ?string $navigationLabel = 'Activity Log';

    protected static ?string $pluralModelLabel = 'Aktivitas';

    protected static ?string $modelLabel = 'Aktivitas';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        // read-only — tidak ada form
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i')->sortable()->weight('medium'),
                Tables\Columns\TextColumn::make('user.name')->label('Pengguna')->placeholder('Sistem')->searchable(),
                Tables\Columns\TextColumn::make('action')->label('Aksi')->badge()->formatStateUsing(fn ($state) => ucfirst($state))->color(fn ($state) => match ($state) {
                    'created' => 'success',
                    'updated' => 'info',
                    'deleted' => 'danger',
                    default => 'gray',
                }),
                Tables\Columns\TextColumn::make('description')->label('Deskripsi')->searchable()->limit(60),
                Tables\Columns\TextColumn::make('model_type')->label('Entitas')->formatStateUsing(function ($state) {
                    $short = class_basename((string) $state);

                    return match ($short) {
                        'Activity' => 'Kegiatan',
                        'BudgetItem' => 'Anggaran',
                        'Payment' => 'Pembayaran',
                        'Vendor' => 'Vendor',
                        'SeserahanItem' => 'Seserahan',
                        'Guest' => 'Tamu',
                        'EventProfile' => 'Profil Acara',
                        'VendorOption' => 'Penawaran Vendor',
                        'User' => 'Pengguna',
                        default => $short,
                    };
                })->badge()->color('navy'),
                Tables\Columns\TextColumn::make('ip')->label('IP')->placeholder('—')->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options(['created' => 'Dibuat', 'updated' => 'Diperbarui', 'deleted' => 'Dihapus'])
                    ->label('Aksi'),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
