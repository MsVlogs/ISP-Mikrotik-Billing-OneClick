<?php

namespace App\Filament\Resources;

use App\Models\Invoice;
use Filament\Forms\Components\{Section, Select, TextInput, DatePicker, Textarea};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\{BulkActionGroup, DeleteAction, EditAction};
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Billing';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Invoice Details')->schema([
                TextInput::make('invoice_no')->disabled(),
                Select::make('customer_unique_id')
                    ->relationship('customer', 'customer_name')
                    ->searchable()
                    ->required(),
                DatePicker::make('billing_period')->required(),
                TextInput::make('subtotal')->numeric()->readOnly(),
                TextInput::make('discount')->numeric()->default(0),
                TextInput::make('tax')->numeric()->default(0),
                TextInput::make('total')->numeric()->readOnly(),
                TextInput::make('paid')->numeric()->default(0),
                Select::make('status')
                    ->options(['unpaid'=>'Unpaid','partial'=>'Partial','paid'=>'Paid','overdue'=>'Overdue'])
                    ->required(),
                DatePicker::make('due_at'),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_no')->searchable()->sortable(),
                TextColumn::make('customer.customer_name')->searchable(),
                TextColumn::make('billing_period')->date()->sortable(),
                TextColumn::make('total')->money('BDT')->sortable(),
                TextColumn::make('paid')->money('BDT'),
                BadgeColumn::make('status')
                    ->colors(['unpaid'=>'danger','partial'=>'warning','paid'=>'success','overdue'=>'error']),
                TextColumn::make('due_at')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(['unpaid'=>'Unpaid','partial'=>'Partial','paid'=>'Paid','overdue'=>'Overdue']),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteAction::make()])]);
    }
}
