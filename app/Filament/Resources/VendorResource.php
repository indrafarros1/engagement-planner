<?php

namespace App\Filament\Resources;

use App\Enums\BudgetCategory;
use App\Filament\Resources\VendorResource\Pages;
use App\Filament\Resources\VendorResource\RelationManagers\OptionsRelationManager;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|\UnitEnum|null $navigationGroup = 'Persiapan';

    protected static ?string $navigationLabel = 'Vendor';

    protected static ?string $pluralModelLabel = 'Vendor';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Vendor')
                    ->schema([
                        TextInput::make('name')->label('Nama Vendor / Toko')->required()->maxLength(150),
                        Select::make('category')->label('Kategori')->options(BudgetCategory::options())->default(BudgetCategory::Other->value)->required(),
                        TextInput::make('contact_name')->label('Nama Kontak')->maxLength(100),
                        TextInput::make('contact_phone')->label('Telepon / WA')->tel()->maxLength(30),
                        TextInput::make('contact_email')->label('Email')->email(),
                        Textarea::make('address')->label('Alamat')->rows(2),
                        Textarea::make('notes')->label('Catatan')->rows(3),
                        Toggle::make('archived')->label('Arsipkan')->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Vendor')->searchable()->sortable()->weight('medium'),
                Tables\Columns\TextColumn::make('category')->label('Kategori')->badge()->formatStateUsing(fn ($state) => $state->label())->color('coral'),
                Tables\Columns\TextColumn::make('contact_name')->label('Kontak')->placeholder('—'),
                Tables\Columns\TextColumn::make('contact_phone')->label('Telepon')->placeholder('—')->icon('heroicon-m-phone'),
                Tables\Columns\TextColumn::make('options_count')->label('Penawaran')->counts('options')->badge()->color('navy'),
                Tables\Columns\TextColumn::make('selected_option_price')->label('Harga Terpilih')
                    ->formatStateUsing(fn ($state, $record) => auth()->user()->canViewAmounts()
                        ? 'Rp ' . number_format($state, 0, ',', '.')
                        : '•••')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('selected_option_name')->label('Paket Terpilih')->placeholder('Belum dipilih'),
                Tables\Columns\IconColumn::make('archived')->label('Arsip')->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')->options(BudgetCategory::options())->label('Kategori'),
                Tables\Filters\TernaryFilter::make('archived')->label('Arsip')->trueLabel('Arsip')->falseLabel('Aktif'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('compare')
                    ->label('Bandingkan')
                    ->icon('heroicon-o-scale')
                    ->color('info')
                    ->url(fn (Vendor $record) => \App\Filament\Pages\VendorComparison::getUrl(['vendor' => $record])),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}
