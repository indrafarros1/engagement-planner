<?php

namespace App\Filament\Resources;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\Widgets\PaymentStatsOverview;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = 'Anggaran';

    protected static ?string $navigationLabel = 'DP & Pembayaran';

    protected static ?string $pluralModelLabel = 'Pembayaran';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getGloballySearchableAttributes(): array
    {
        return ['budgetItem.name', 'notes', 'amount'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Pembayaran')
                    ->schema([
                        Select::make('budget_item_id')
                            ->label('Item Anggaran')
                            ->relationship('budgetItem', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')->label('Nama Item')->required(),
                                TextInput::make('unit_price')->label('Harga Satuan (Rp)')->numeric()->required(),
                                TextInput::make('quantity')->label('Jumlah')->numeric()->required()->default(1),
                            ]),
                        Select::make('type')->label('Jenis')->options(PaymentType::options())
                            ->default(PaymentType::DP->value)->required()
                            ->helperText('Koreksi/Refund dipakai bila total melebihi kontrak — tanpa menghapus histori'),
                        TextInput::make('amount')->label('Nominal (Rp)')->numeric()->required()->minValue(1)
                            ->suffix('Rp')->inputMode('numeric')
                            ->helperText('Nominal harus > 0 · integer Rupiah'),
                    ])
                    ->columns(2),
                Section::make('Jadwal & Status')
                    ->description('Status dihitung otomatis dari nominal, tanggal bayar & jatuh tempo (aturan 5.5)')
                    ->schema([
                        DatePicker::make('due_date')->label('Jatuh Tempo')->native(false)->displayFormat('d M Y'),
                        DatePicker::make('paid_date')->label('Tanggal Dibayar')->native(false)->displayFormat('d M Y')
                            ->helperText('Kosong = belum dibayar · isi = pembayaran tercatat'),
                        TextInput::make('paid_amount')->label('Nominal Dibayar (Rp)')->numeric()->minValue(0)->suffix('Rp')
                            ->helperText('Untuk Sebagian Dibayar — bila kosong = amount saat paid_date terisi'),
                        Select::make('method')->label('Metode')->options(PaymentMethod::options())->searchable(),
                        FileUpload::make('proof_path')->label('Bukti Pembayaran')
                            ->image()
                            ->directory('bukti-pembayaran')
                            ->imageEditor()
                            ->imageResizeMode('contain')
                            ->maxSize(4096),
                        Toggle::make('cancelled')->label('Dibatalkan')->default(false),
                        Textarea::make('notes')->label('Catatan')->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('paid_date', 'desc')
            ->columns([
                TextColumn::make('budgetItem.name')->label('Item Anggaran')->searchable()->sortable()->weight('medium'),
                TextColumn::make('type')->label('Jenis')->badge()->formatStateUsing(fn ($state) => $state->label())->color(fn ($state) => match ($state) {
                    PaymentType::DP => 'info',
                    PaymentType::Installment => 'warning',
                    PaymentType::Full => 'success',
                    PaymentType::Refund => 'danger',
                    PaymentType::Correction => 'gray',
                }),
                TextColumn::make('amount')->label('Nominal')->money('IDR', locale: 'id')->sortable(),
                TextColumn::make('status')->label('Status')->badge()
                    ->state(fn (Payment $record) => $record->status()->value)
                    ->formatStateUsing(fn ($state) => PaymentStatus::from($state)->label())
                    ->color(fn (Payment $record) => match ($record->status()) {
                        PaymentStatus::Paid => 'success',
                        PaymentStatus::Overdue => 'danger',
                        PaymentStatus::Partial => 'warning',
                        PaymentStatus::Unpaid => 'gray',
                        PaymentStatus::Cancelled => 'gray',
                    }), // teks label selalu tampil — warna hanya penguat (PRD 13.2)
                TextColumn::make('due_date')->label('Jatuh Tempo')->date('d M Y')->sortable()
                    ->color(fn (Payment $record) => $record->status() === PaymentStatus::Overdue ? 'danger' : null),
                TextColumn::make('paid_date')->label('Dibayar')->date('d M Y')->placeholder('—'),
                TextColumn::make('method')->label('Metode')->formatStateUsing(fn ($state) => $state ? $state->label() : '—'),
                TextColumn::make('proof_path')->label('Bukti')
                    ->state(fn (Payment $record) => $record->proof_path ? 'v' : '')
                    ->icon('heroicon-o-paper-clip')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('type')->options(PaymentType::options())->label('Jenis'),
                SelectFilter::make('budget_item')->relationship('budgetItem', 'name')->label('Item Anggaran')->searchable()->preload(),
                Filter::make('status')->label('Status')
                    ->form([
                        Forms\Components\Select::make('value')
                            ->options(PaymentStatus::options())
                            ->label('Status Otomatis'),
                    ])
                    ->query(function ($query, array $data) {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return match ($data['value']) {
                            PaymentStatus::Cancelled->value => $query->where('cancelled', true),
                            PaymentStatus::Paid->value => $query->where('cancelled', false)
                                ->where(function ($q) {
                                    $q->whereColumn('paid_amount', '>=', 'amount')
                                        ->orWhere(function ($q2) {
                                            $q2->whereNotNull('paid_date')->whereNull('paid_amount');
                                        });
                                }),
                            PaymentStatus::Partial->value => $query->where('cancelled', false)
                                ->whereNull('paid_date')
                                ->orWhere(function ($q) {
                                    $q->whereNotNull('paid_amount')->whereColumn('paid_amount', '<', 'amount')->where('paid_amount', '>', 0);
                                }),
                            PaymentStatus::Unpaid->value => $query->where('cancelled', false)
                                ->whereNull('paid_date')->whereNull('paid_amount'),
                            PaymentStatus::Overdue->value => $query->where('cancelled', false)
                                ->whereNotNull('due_date')->whereDate('due_date', '<', today())
                                ->where(function ($q) {
                                    $q->whereColumn('paid_amount', '<', 'amount')
                                        ->orWhereNull('paid_amount');
                                }),
                            default => $query,
                        };
                    }),
                Filter::make('overdue')
                    ->label('Terlambat')
                    ->query(fn ($query) => $query->where('cancelled', false)
                        ->whereNotNull('due_date')->whereDate('due_date', '<', today())
                        ->where(fn ($q) => $q->whereColumn('paid_amount', '<', 'amount')->orWhereNull('paid_amount'))),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            PaymentStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
