<?php

namespace App\Filament\Resources;

use App\Enums\ActivityCategory;
use App\Enums\ActivityStatus;
use App\Enums\Payer;
use App\Enums\Priority;
use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\Widgets\ActivityStatsOverview;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-check-badge';

    protected static string|\UnitEnum|null $navigationGroup = 'Persiapan';

    protected static ?string $navigationLabel = 'Kegiatan & Checklist';

    protected static ?string $pluralModelLabel = 'Kegiatan';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Kegiatan')
                    ->schema([
                        TextInput::make('name')->label('Nama Kegiatan')->required()->maxLength(150),
                        Textarea::make('description')->label('Deskripsi')->rows(3),
                        Select::make('category')->label('Kategori')->options(ActivityCategory::options())->default(ActivityCategory::Preparation->value)->required(),
                        Select::make('pic')->label('Penanggung Jawab (PIC)')->options(Payer::options())->default(Payer::Bersama->value),
                        DatePicker::make('due_date')->label('Deadline')->native(false)->displayFormat('d M Y'),
                        Select::make('priority')->label('Prioritas')->options(Priority::options())->default(Priority::Medium->value),
                        Select::make('status')->label('Status')->options(ActivityStatus::options())->default(ActivityStatus::NotStarted->value),
                        Toggle::make('archived')->label('Arsipkan (sembunyikan dari daftar aktif)')->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('due_date', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Kegiatan')->searchable()->sortable()->weight('medium'),
                Tables\Columns\TextColumn::make('category')->label('Kategori')->badge()->formatStateUsing(fn ($state) => $state->label())->color('coral'),
                Tables\Columns\TextColumn::make('pic')->label('PIC')->formatStateUsing(fn ($state) => $state->label())->badge()->color('navy'),
                Tables\Columns\TextColumn::make('due_date')->label('Deadline')->date('d M Y')->sortable()->color(fn (Activity $record) => $record->isOverdue() ? 'danger' : null),
                Tables\Columns\TextColumn::make('priority')->label('Prioritas')->badge()->formatStateUsing(fn ($state) => $state->label())->color(fn ($state) => match ($state) {
                    Priority::High => 'danger',
                    Priority::Medium => 'warning',
                    Priority::Low => 'gray',
                }),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()->formatStateUsing(fn ($state) => $state->label())->color(fn (Activity $record) => match (true) {
                    $record->status === ActivityStatus::Done => 'success',
                    $record->isOverdue() => 'danger',
                    $record->status === ActivityStatus::InProgress => 'warning',
                    default => 'gray',
                }),
                Tables\Columns\IconColumn::make('archived')->label('Arsip')->boolean(),
            ])
            ->filters([
                SelectFilter::make('status')->options(ActivityStatus::options())->label('Status'),
                SelectFilter::make('category')->options(ActivityCategory::options())->label('Kategori'),
                SelectFilter::make('pic')->options(Payer::options())->label('PIC'),
                Filter::make('due_date_range')
                    ->label('Rentang Deadline')
                    ->form([
                        DatePicker::make('due_from')->label('Dari')->native(false),
                        DatePicker::make('due_to')->label('Sampai')->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['due_from'], fn ($q) => $q->whereDate('due_date', '>=', $data['due_from']))
                            ->when($data['due_to'], fn ($q) => $q->whereDate('due_date', '<=', $data['due_to']));
                    }),
                Filter::make('overdue')
                    ->label('Terlambat')
                    ->query(fn ($query) => $query->where('status', '!=', ActivityStatus::Done->value)->whereNotNull('due_date')->whereDate('due_date', '<', today())),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('toggleArchive')
                    ->label(fn (Activity $record) => $record->archived ? 'Batalkan Arsip' : 'Arsipkan')
                    ->icon(fn (Activity $record) => $record->archived ? 'heroicon-o-arrow-uturn-up' : 'heroicon-o-archive-box')
                    ->requiresConfirmation()
                    ->action(fn (Activity $record) => $record->update(['archived' => ! $record->archived])),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            ActivityStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
