<?php

namespace App\Filament\Resources;

use App\Enums\Payer;
use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Profil';

    protected static ?string $navigationLabel = 'Akun Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Akun')
                    ->schema([
                        TextInput::make('name')->label('Nama')->required()->maxLength(100),
                        TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
                        TextInput::make('password')->label('Password')->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->helperText('Kosongkan bila tidak ingin mengubah'),
                        Select::make('role')->label('Peran')->options(UserRole::options())->default(UserRole::Owner->value)->required()
                            ->helperText('Owner = akses penuh · Partner = pasangan (akses dibatasi)'),
                        Select::make('partner_side')->label('Sisi Partner')->options(Payer::options())->nullable()
                            ->visible(fn (Get $get) => $get('role') === UserRole::Partner->value),
                        Toggle::make('can_view_amounts')->label('Lihat nominal anggaran (Rp)')->default(true)
                            ->visible(fn (Get $get) => $get('role') === UserRole::Partner->value)
                            ->helperText('Partner tanpa izin ini hanya melihat kegiatan & seserahan, tanpa angka uang'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable()->weight('medium'),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('role')->label('Peran')->badge()->formatStateUsing(fn ($state) => $state->label())->color(fn ($state) => match ($state) {
                    UserRole::Owner => 'coral',
                    UserRole::Partner => 'navy',
                }),
                Tables\Columns\TextColumn::make('partner_side')->label('Sisi')->placeholder('—')->formatStateUsing(fn ($state) => $state?->label()),
                Tables\Columns\IconColumn::make('can_view_amounts')->label('Lihat Nominal')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->date('d M Y')->sortable(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
