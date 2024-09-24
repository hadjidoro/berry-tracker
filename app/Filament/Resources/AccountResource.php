<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Account;
use Filament\Forms\Form;
use App\Enums\AccountType;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\AccountResource\Pages;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->options([
                        AccountType::Cash->name   => AccountType::Cash->name,
                        AccountType::Bank->name   => AccountType::Bank->name,
                        AccountType::Wallet->name => AccountType::Wallet->name,
                    ])
                    ->required(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn ($state) => $state->name)
                    ->badge(),
                Tables\Columns\TextColumn::make('balance'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            //'create' => Pages\CreateAccount::route('/create'),
            //'edit'   => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
