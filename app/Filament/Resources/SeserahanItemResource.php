<?php

namespace App\Filament\Resources;

use App\Enums\Payer;
use App\Enums\SeserahanStatus;
use App\Filament\Resources\SeserahanItemResource\Pages;
use App\Filament\Resources\SeserahanItemResource\Widgets\SeserahanStatsOverview;
use App\Models\SeserahanItem;
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

class SeserahanItemResource extends Resource
{
    protected static ?string $model = SeserahanItem::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-gift';

    protected static string|\UnitEnum|null $navigationGroup = 'Persiapan';

    protected static ?string $navigationLabel = 'Seserahan';

    protected static ?string $pluralModelLabel = 'Seserahan';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Item Seserahan')
                    ->schema([
                        TextInput::make('name')->label('Nama Item')->required()->maxLength(150),
                        TextInput::make('quantity')->label('Jumlah')->numeric()->required()->minValue(1)->default(1)->integer(),
                        TextInput::make('unit_price')->label('Perkiraan Harga Satuan (Rp)')->numeric()->minValue(0)->default(0)->suffix('Rp'),
                        Select::make('status')->label('Status')->options(SeserahanStatus::options())->default(SeserahanStatus::Planned->value),
                        Select::make('pic')->label('Penanggung')->options(Payer::options())->default(Payer::Bersama->value),
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
                Tables\Columns\TextColumn::make('name')->label('Item')->searchable()->sortable()->weight('medium'),
                Tables\Columns\TextColumn::make('quantity')->label('Jumlah')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('unit_price')->label('Harga Satuan')
                    ->formatStateUsing(fn ($state) => auth()->user()->canViewAmounts()
                        ? 'Rp ' . number_format($state, 0, ',', '.')
                        : '•••')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('total')->label('Total')
                    ->formatStateUsing(fn ($state) => auth()->user()->canViewAmounts()
                        ? 'Rp ' . number_format($state, 0, ',', '.')
                        : '•••'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()->formatStateUsing(fn ($state) => $state->label())->color(fn ($state) => match ($state) {
                    SeserahanStatus::Done => 'success',
                    SeserahanStatus::Preparing => 'warning',
                    SeserahanStatus::Planned => 'gray',
                }),
                Tables\Columns\TextColumn::make('pic')->label('Penanggung')->formatStateUsing(fn ($state) => $state->label())->badge()->color('navy'),
            ])
            ->filters([
                SelectFilter::make('status')->options(SeserahanStatus::options())->label('Status'),
                SelectFilter::make('pic')->options(Payer::options())->label('Penanggung'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            SeserahanStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeserahanItems::route('/'),
            'create' => Pages\CreateSeserahanItem::route('/create'),
            'edit' => Pages\EditSeserahanItem::route('/{record}/edit'),
        ];
    }
}
