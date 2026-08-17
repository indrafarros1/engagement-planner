<?php

namespace App\Filament\Resources;

use App\Enums\EventStatus;
use App\Filament\Resources\EventProfileResource\Pages;
use App\Models\EventProfile;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EventProfileResource extends Resource
{
    protected static ?string $model = EventProfile::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-heart';

    protected static string|\UnitEnum|null $navigationGroup = 'Profil';

    protected static ?string $navigationLabel = 'Profil Acara';

    protected static ?string $pluralModelLabel = 'Profil Acara';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Pasangan')
                    ->description('Data calon mempelai pria & wanita')
                    ->schema([
                        TextInput::make('couple_a_name')->label('Nama CPP (Pria)')->required()->maxLength(100),
                        TextInput::make('couple_b_name')->label('Nama CPW (Wanita)')->required()->maxLength(100),
                    ])
                    ->columns(2),
                Section::make('Waktu & Tempat')
                    ->schema([
                        DatePicker::make('event_date')->label('Tanggal Acara')->required()->native(false)->displayFormat('d M Y'),
                        TextInput::make('start_time')->label('Waktu Mulai')->placeholder('09:00')->helperText('Format 24 jam'),
                        TextInput::make('end_time')->label('Waktu Selesai')->placeholder('14:00'),
                        TextInput::make('venue_name')->label('Nama Lokasi')->maxLength(150),
                        Textarea::make('venue_address')->label('Alamat')->rows(3),
                    ])
                    ->columns(2),
                Section::make('Detail Lainnya')
                    ->schema([
                        TextInput::make('estimated_guests')->label('Estimasi Tamu')->numeric()->minValue(0),
                        Select::make('status')->options(EventStatus::options())->label('Status Persiapan')->default('planning'),
                        Textarea::make('notes')->label('Catatan')->rows(4),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('couple_a_name')->label('CPP')->searchable(),
                Tables\Columns\TextColumn::make('couple_b_name')->label('CPW')->searchable(),
                Tables\Columns\TextColumn::make('event_date')->label('Tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('start_time')->label('Mulai'),
                Tables\Columns\TextColumn::make('venue_name')->label('Lokasi')->limit(25),
                Tables\Columns\TextColumn::make('estimated_guests')->label('Tamu')->numeric(),
                Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => $state->label())->color(fn ($state) => match ($state) {
                    EventStatus::Ready => 'success',
                    EventStatus::Done => 'info',
                    EventStatus::Cancelled => 'danger',
                    default => 'warning',
                }),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventProfiles::route('/'),
            'create' => Pages\CreateEventProfile::route('/create'),
            'edit' => Pages\EditEventProfile::route('/{record}/edit'),
        ];
    }
}
