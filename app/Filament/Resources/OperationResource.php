<?php

namespace App\Filament\Resources;

use Carbon\Month;
use Filament\Tables;
use App\Models\Account;
use App\Models\Category;
use App\Models\Operation;
use Filament\Tables\Table;
use App\Enums\OperationType;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use App\Filament\Resources\OperationResource\Pages;

class OperationResource extends Resource
{
    protected static ?string $model = Operation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account.name'),
                Tables\Columns\TextColumn::make('description')->wrap(),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('type')->formatStateUsing(fn (OperationType $state) => __($state->name))
                    ->color('info')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn ($state) => Number::format($state, 2, locale: 'fr')),
                Tables\Columns\TextColumn::make('fees')
                    ->formatStateUsing(fn ($state) => Number::format($state, 2, locale: 'fr')),
                Tables\Columns\TextColumn::make('month')->formatStateUsing(fn (Month $state) => __($state->name))
                    ->badge(),
                Tables\Columns\TextColumn::make('performed_at'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('month')
                    ->options(fn () => collect(Month::cases())
                        ->flatMap(fn ($month) => [$month->value => $month->name])
                        ->toArray())
                    ->label(__('Month')),
                Tables\Filters\SelectFilter::make('category_id')
                    ->options(fn () => Category::pluck('name', 'id')->toArray())
                    ->label(__('Category')),
                Tables\Filters\SelectFilter::make('account_id')
                    ->options(fn () => Account::pluck('name', 'id')->toArray())
                    ->label(__('Account')),
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
            'index' => Pages\ListOperations::route('/'),
            //'create' => Pages\CreateOperation::route('/create'),
            //'edit'   => Pages\EditOperation::route('/{record}/edit'),
        ];
    }
}
