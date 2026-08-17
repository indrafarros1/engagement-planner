<?php

namespace App\Filament\Resources\VendorResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class OptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    protected static ?string $title = 'Pilihan / Penawaran';

    protected static ?string $pluralModelLabel = 'Pilihan';

    protected static ?string $modelLabel = 'Pilihan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')->label('Nama Paket / Penawaran')->required()->maxLength(150),
                    Textarea::make('description')->label('Deskripsi (isi penawaran)')->rows(2),
                    TextInput::make('price')->label('Harga (Rp)')->numeric()->required()->minValue(0)->suffix('Rp')->default(0),
                    Toggle::make('selected')->label('Dipilih untuk acara ini')->default(false),
                    Textarea::make('notes')->label('Catatan')->rows(2),
                ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Paket')->searchable()->weight('medium'),
                Tables\Columns\TextColumn::make('description')->label('Deskripsi')->limit(50),
                Tables\Columns\TextColumn::make('price')->label('Harga')->money('IDR', locale: 'id')->sortable(),
                Tables\Columns\IconColumn::make('selected')->label('Terpilih')->boolean(),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()->label('+ Tambah Pilihan'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('select')
                    ->label(fn ($record) => $record->selected ? 'Batalkan Pilihan' : 'Pilih Paket Ini')
                    ->icon(fn ($record) => $record->selected ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->selected ? 'gray' : 'success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // hanya satu paket terpilih per vendor
                        $this->getRelationship()->update(['selected' => false]);
                        $record->update(['selected' => ! $record->selected]);
                    }),
            ]);
    }
}
