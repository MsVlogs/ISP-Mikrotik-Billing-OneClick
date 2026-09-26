<?php

namespace App\Filament\Resources;

use App\Models\Subscription;
use Filament\Forms\Components\{DatePicker, Section, Select, Textarea};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\{BulkActionGroup, DeleteAction, EditAction};
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationGroup = 'Billing';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Subscription Details')->schema([
                Select::make('customer_unique_id')
                    ->relationship('customer', 'customer_name')
                    ->searchable()
                    ->required(),
                Select::make('package_id')
                    ->relationship('package', 'name')
                    ->required(),
                DatePicker::make('starts_at')->required(),
                DatePicker::make('ends_at'),
                Select::make('status')
                    ->options(['active'=>'Active','past_due'=>'Past Due','suspended'=>'Suspended','churn'=>'Churned'])
                    ->required(),
                Textarea::make('suspension_reason')->columnSpanFull(),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.customer_name')->searchable(),
                TextColumn::make('package.name'),
                TextColumn::make('starts_at')->date(),
                TextColumn::make('ends_at')->date(),
                BadgeColumn::make('status')
                    ->colors(['active'=>'success','past_due'=>'warning','suspended'=>'danger','churn'=>'error']),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(['active'=>'Active','past_due'=>'Past Due','suspended'=>'Suspended','churn'=>'Churned']),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteAction::make()])]);
    }
}
