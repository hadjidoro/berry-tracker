<?php

namespace App\Filament\Resources\OperationResource\Pages;

use Carbon\Month;
use Filament\Actions;
use App\Models\Account;
use Filament\Forms\Get;
use App\Models\Category;
use Filament\Forms\Form;
use App\Models\Operation;
use Illuminate\Support\Arr;
use App\Enums\OperationType;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\OperationResource;

class ListOperations extends ListRecords
{
    protected static string $resource = OperationResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Radio::make('kind')
                        ->options([
                            'standard' => 'Standard',
                            'transfer' => 'Transfer',
                        ])
                        ->inline()
                        ->hiddenLabel()
                        ->default('standard')
                        ->live(),
                ]),

                Section::make()->schema([
                    Select::make('account_id')
                        ->label(__('Account'))
                        ->native(false)
                        ->options(Account::pluck('name', 'id')->toArray())
                        ->required(),
                    Select::make('type')
                        ->label(__('Type'))
                        ->native(false)
                        ->options(collect(OperationType::cases())
                            ->flatMap(fn ($type) => [$type->name => $type->name])
                            ->toArray())
                        ->required(),
                    Select::make('category_id')
                        ->label(__('Category'))
                        ->native(false)
                        ->options(Category::pluck('name', 'id')->toArray())
                        ->required(),
                    TextInput::make('description')
                        ->label(__('Description'))
                        ->required(),
                    TextInput::make('amount')
                        ->label(__('Amount'))
                        ->required(),
                    TextInput::make('fees')
                        ->label(__('Fees'))
                        ->required(),
                    Select::make('month')
                        ->label(__('Month'))
                        ->native(false)
                        ->options(collect(Month::cases())
                            ->flatMap(fn (Month $month) => [$month->value => $month->name])
                            ->toArray())
                        ->required(),
                    DatePicker::make('performed_at')
                        ->label(__('Performed at'))
                        ->required(),
                ])
                    ->columns(3)
                    ->hidden(fn (Get $get) => $get('kind') !== 'standard'),

                Section::make()->schema([
                    Select::make('account_from')
                        ->native(false)
                        ->label(__('Account From'))
                        ->options(Account::pluck('name', 'id')->toArray())
                        ->required(),
                    TextInput::make('amount')
                        ->label(__('Amount'))
                        ->required(),
                    TextInput::make('fees')
                        ->label(__('Fees'))
                        ->required(),

                    Select::make('account_to')
                        ->label(__('Account To'))
                        ->native(false)
                        ->options(Account::pluck('name', 'id')->toArray())
                        ->required(),

                    Select::make('month')
                        ->label(__('Month'))
                        ->native(false)
                        ->options(collect(Month::cases())
                            ->flatMap(fn (Month $month) => [$month->value => $month->name])
                            ->toArray())
                        ->required(),
                    DatePicker::make('performed_at')
                        ->label(__('Performed at'))
                        ->required(),
                ])
                    ->columns(3)
                    ->hidden(fn (Get $get) => $get('kind') !== 'transfer'),

            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->using(function (array $data) {
                    if (Arr::has($data, 'kind') && $data['kind'] === 'standard') {
                        Operation::create(Arr::except($data, ['kind']));
                    }

                    if (Arr::has($data, 'kind') && $data['kind'] === 'transfer') {
                        Operation::create([
                            'account_id'   => $data['account_from'],
                            'type'         => OperationType::Withdrawal,
                            'category_id'  => Category::firstOrCreate(['name' => 'Transfer Withdrawal'])->id,
                            'description'  => 'Withdrawal',
                            'amount'       => $data['amount'],
                            'fees'         => $data['fees'],
                            'month'        => $data['month'],
                            'performed_at' => $data['performed_at'],
                        ]);

                        Operation::create([
                            'account_id'   => $data['account_to'],
                            'type'         => OperationType::Deposit,
                            'category_id'  => Category::firstOrCreate(['name' => 'Transfer Deposit'])->id,
                            'description'  => 'Deposit',
                            'amount'       => $data['amount'] - $data['fees'],
                            'fees'         => 0,
                            'month'        => $data['month'],
                            'performed_at' => $data['performed_at'],
                        ]);
                    }
                }),
        ];
    }
}
