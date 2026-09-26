<?php

namespace App\Filament\Resources;

use App\Models\Employee;
use Filament\Forms\Components\{DatePicker, Select, TextInput, Section};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\{BulkActionGroup, DeleteAction, EditAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'HR';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Employment Info')->schema([
                TextInput::make('employee_no')->unique(ignoreRecord: true)->required(),
                Select::make('user_id')->relationship('user', 'name'),
                TextInput::make('department')->required(),
                TextInput::make('job_title')->required(),
                TextInput::make('salary')->numeric()->prefix('৳'),
                DatePicker::make('joined_at')->required(),
                Select::make('status')
                    ->options(['active'=>'Active','on_leave'=>'On Leave','suspended'=>'Suspended','inactive'=>'Inactive'])
                    ->default('active'),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_no')->searchable()->sortable(),
                TextColumn::make('user.name')->searchable(),
                TextColumn::make('department')->searchable(),
                TextColumn::make('job_title'),
                TextColumn::make('salary')->money('BDT'),
                TextColumn::make('joined_at')->date(),
                TextColumn::make('status')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(['active'=>'Active','on_leave'=>'On Leave','suspended'=>'Suspended','inactive'=>'Inactive']),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteAction::make()])]);
    }
}
