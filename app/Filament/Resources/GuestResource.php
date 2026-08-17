<?php

namespace App\Filament\Resources;

use App\Enums\GuestGroup;
use App\Enums\GuestStatus;
use App\Filament\Resources\GuestResource\Pages;
use App\Filament\Resources\GuestResource\Widgets\GuestStatsOverview;
use App\Models\Guest;
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

class GuestResource extends Resource
{
    protected static ?string $model = Guest::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Profil';

    protected static ?string $navigationLabel = 'Tamu & Keluarga';

    protected static ?string $pluralModelLabel = 'Tamu';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Tamu')
                    ->schema([
                        TextInput::make('name')->label('Nama Keluarga / Kelompok')->required()->maxLength(150),
                        Select::make('group')->label('Sisi Keluarga')->options(GuestGroup::options())->default(GuestGroup::CPW->value)->required(),
                        TextInput::make('total_people')->label('Jumlah Orang')->numeric()->required()->minValue(1)->default(1)->integer(),
                        Select::make('status')->label('Status Undangan')->options(GuestStatus::options())->default(GuestStatus::Invited->value),
                        TextInput::make('phone')->label('Telepon / WA')->tel()->maxLength(30),
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
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable()->weight('medium'),
                Tables\Columns\TextColumn::make('group')->label('Sisi')->badge()->formatStateUsing(fn ($state) => $state->label())->color(fn ($state) => match ($state) {
                    GuestGroup::CPP => 'info',
                    GuestGroup::CPW => 'coral',
                }),
                Tables\Columns\TextColumn::make('total_people')->label('Orang')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()->formatStateUsing(fn ($state) => $state->label())->color(fn ($state) => match ($state) {
                    GuestStatus::Confirmed => 'success',
                    GuestStatus::Declined => 'danger',
                    GuestStatus::Invited => 'info',
                    GuestStatus::Unknown => 'gray',
                }),
                Tables\Columns\TextColumn::make('phone')->label('Telepon')->placeholder('—')->icon('heroicon-m-phone'),
            ])
            ->filters([
                SelectFilter::make('group')->options(GuestGroup::options())->label('Sisi'),
                SelectFilter::make('status')->options(GuestStatus::options())->label('Status'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            GuestStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuests::route('/'),
            'create' => Pages\CreateGuest::route('/create'),
            'edit' => Pages\EditGuest::route('/{record}/edit'),
        ];
    }
}
