<?php

namespace App\Filament\Resources;

use App\Enums\BudgetCategory;
use App\Enums\Payer;
use App\Filament\Resources\BudgetItemResource\Pages;
use App\Filament\Resources\BudgetItemResource\Widgets\BudgetStatsOverview;
use App\Models\BudgetItem;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BudgetItemResource extends Resource
{
    protected static ?string $model = BudgetItem::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Anggaran';

    protected static ?string $navigationLabel = 'Item Anggaran';

    protected static ?string $pluralModelLabel = 'Item Anggaran';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Item')
                    ->schema([
                        TextInput::make('name')->label('Nama Item')->required()->maxLength(150),
                        Textarea::make('description')->label('Deskripsi')->rows(2),
                        Select::make('category')->label('Kategori')->options(BudgetCategory::options())->default(BudgetCategory::Other->value)->required(),
                        Select::make('payer')->label('Penanggung')->options(Payer::options())->default(Payer::Bersama->value),
                    ])
                    ->columns(2),
                Section::make('Nominal (Rupiah)')
                    ->description('Estimasi = harga satuan × jumlah · anggaran memakai nilai kontrak bila diisi')
                    ->schema([
                        TextInput::make('unit_price')->label('Harga Satuan (Rp)')->numeric()->required()->minValue(1)->default(0)
                            ->suffix('Rp')->inputMode('numeric'),
                        TextInput::make('quantity')->label('Jumlah')->numeric()->required()->minValue(1)->default(1)->integer(),
                        TextInput::make('contract_value')->label('Nilai Kontrak (Rp)')->numeric()->minValue(1)->helperText('Kosongkan bila belum ada kontrak — memakai estimasi')->suffix('Rp'),
                        Toggle::make('archived')->label('Arsipkan')->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Item')->searchable()->sortable()->weight('medium'),
                Tables\Columns\TextColumn::make('category')->label('Kategori')->badge()->formatStateUsing(fn ($state) => $state->label())->color('coral'),
                Tables\Columns\TextColumn::make('unit_price')->label('Harga Satuan')->money('IDR', locale: 'id')->sortable(),
                Tables\Columns\TextColumn::make('quantity')->label('Qty')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('estimate_total')->label('Estimasi')->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('contract_value')->label('Kontrak')->money('IDR', locale: 'id')->placeholder('—')->sortable(),
                Tables\Columns\TextColumn::make('total_paid')->label('Dibayar')->money('IDR', locale: 'id')->color(fn (BudgetItem $record) => $record->total_paid > 0 ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('remaining')->label('Sisa')->money('IDR', locale: 'id')->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('payer')->label('Penanggung')->formatStateUsing(fn ($state) => $state->label())->badge()->color('navy'),
            ])
            ->filters([
                SelectFilter::make('category')->options(BudgetCategory::options())->label('Kategori'),
                SelectFilter::make('payer')->options(Payer::options())->label('Penanggung'),
                Tables\Filters\TernaryFilter::make('archived')->label('Arsip')->trueLabel('Arsip')->falseLabel('Aktif'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('payments')
                    ->label('Pembayaran')
                    ->icon('heroicon-o-credit-card')
                    ->url(fn (BudgetItem $record) => PaymentResource::getUrl('index', ['tableFilters' => ['budget_item' => ['value' => $record->id]]])),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            BudgetStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgetItems::route('/'),
            'create' => Pages\CreateBudgetItem::route('/create'),
            'edit' => Pages\EditBudgetItem::route('/{record}/edit'),
        ];
    }
}
