<?php

namespace App\Filament\Resources;

use App\Models\Channel;
use Filament\Forms\Components\{Checkbox, Section, TextInput, Textarea};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\{BulkActionGroup, DeleteAction, EditAction};
use Filament\Tables\Columns\{TextColumn, IconColumn};
use Filament\Tables\Table;

class ChannelResource extends Resource
{
    protected static ?string $model = Channel::class;
    protected static ?string $navigationIcon = 'heroicon-o-tv';
    protected static ?string $navigationGroup = 'Media';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Channel Details')->schema([
                TextInput::make('name')->required(),
                TextInput::make('stream_url')->url()->required(),
                TextInput::make('category')->nullable(),
                TextInput::make('quality')->nullable(),
                TextInput::make('epg_url')->url()->nullable(),
                Checkbox::make('is_active')->default(true),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category')->searchable(),
                TextColumn::make('quality'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteAction::make()])]);
    }
}
