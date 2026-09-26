<?php

namespace App\Filament\Resources;

use App\Models\ChannelPackage;
use Filament\Forms\Components\{Checkbox, Section, TextInput, Repeater, Select};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\{BulkActionGroup, DeleteAction, EditAction};
use Filament\Tables\Columns\{TextColumn, IconColumn};
use Filament\Tables\Table;

class ChannelPackageResource extends Resource
{
    protected static ?string $model = ChannelPackage::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Media';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Package Info')->schema([
                TextInput::make('name')->required(),
                TextInput::make('monthly_price')->numeric()->prefix('৳')->required(),
                Checkbox::make('is_active')->default(true),
            ]),
            Section::make('Channels')->schema([
                Select::make('channels')
                    ->relationship('channels', 'name')
                    ->multiple()
                    ->searchable(),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('monthly_price')->money('BDT'),
                TextColumn::make('channels_count')->counts('channels'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteAction::make()])]);
    }
}
