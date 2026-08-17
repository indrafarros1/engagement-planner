<?php

namespace App\Filament\Resources\PaymentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Lampiran Bukti';

    protected static ?string $pluralModelLabel = 'Lampiran';

    protected static ?string $modelLabel = 'Lampiran';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()->schema([
                    FileUpload::make('file_path')
                        ->label('File')
                        ->directory('bukti-pembayaran')
                        ->image()
                        ->imageEditor()
                        ->maxSize(8192)
                        ->required()
                        ->acceptedFileTypes(['image/*', 'application/pdf']),
                    TextInput::make('original_name')->label('Nama File')->maxLength(255),
                    TextInput::make('description')->label('Keterangan')->maxLength(255),
                ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('original_name')->label('File')->searchable()->weight('medium'),
                Tables\Columns\TextColumn::make('description')->label('Keterangan')->limit(50),
                Tables\Columns\TextColumn::make('created_at')->label('Diunggah')->dateTime('d M Y H:i'),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()->label('+ Unggah Lampiran'),
            ])
            ->actions([
                \Filament\Actions\Action::make('download')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => \Illuminate\Support\Facades\Storage::url($record->file_path))
                    ->openUrlInNewTab(),
                \Filament\Actions\DeleteAction::make(),
            ]);
    }
}
