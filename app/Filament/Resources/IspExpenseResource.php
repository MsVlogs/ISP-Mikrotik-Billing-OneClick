<?php

namespace App\Filament\Resources;

use App\Models\IspExpense;
use Filament\Forms\Components\{DatePicker, Section, Select, TextInput, Textarea};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\{BulkActionGroup, DeleteAction, EditAction};
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use Filament\Tables\Table;
use Filament\Tables\Filters\{SelectFilter, Filter};
use Illuminate\Database\Eloquent\Builder;

class IspExpenseResource extends Resource
{
    protected static ?string $model = IspExpense::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';
    protected static ?string $navigationGroup = 'Financial';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Expense Details')->schema([
                Select::make('category')
                    ->options(IspExpense::$categories)
                    ->required(),
                TextInput::make('title')->required(),
                Textarea::make('description')->columnSpanFull(),
                TextInput::make('amount')->numeric()->prefix('৳')->required(),
                DatePicker::make('expense_date')->required(),
                TextInput::make('reference_no')->unique(ignoreRecord: true),
            ]),
            Section::make('Links')->schema([
                Select::make('linked_user_id')->relationship('linkedUser', 'name')->nullable(),
                Select::make('linked_reseller_id')->relationship('linkedReseller', 'name')->nullable(),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                BadgeColumn::make('category_label')
                    ->colors(IspExpense::$categoryColors)
                    ->sortable(),
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('amount')->money('BDT')->sortable(),
                TextColumn::make('expense_date')->date()->sortable(),
                TextColumn::make('reference_no')->searchable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(IspExpense::$categories),
                Filter::make('expense_date')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q) => $q->whereDate('expense_date', '>=', $data['from']))
                            ->when($data['until'] ?? null, fn (Builder $q) => $q->whereDate('expense_date', '<=', $data['until']));
                    }),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteAction::make()])]);
    }
}
