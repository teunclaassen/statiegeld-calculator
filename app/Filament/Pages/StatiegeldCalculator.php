<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;

class StatiegeldCalculator extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $title = 'Statiegeld calculator';
    protected static ?string $slug = 'statiegeld-calculator';
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationLabel = 'Statiegeld calculator';
    protected static string $view = 'filament.pages.statiegeld-calculator';

    public ?array $data = [
        'bezorgkratten' => 0,
        'bierkratten' => 0,
        'bierflesjes' => 0,
        'pet_klein_blikje' => 0,
        'pet_groot' => 0,
    ];

    public function mount(): void
    {
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('bon_bedrag')
                            ->label('Bedrag op de bon')
                            ->minValue(0)
                            ->numeric()
                            ->prefix('€')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn () => $this->calculateTotal()),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('bezorgkratten')
                                    ->label('Bezorgkratten (€5)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->calculateTotal())
                                    ->suffixAction(
                                        Action::make('increment')
                                            ->icon('heroicon-m-plus')
                                            ->size('xl')
                                            ->action(fn ($state) => $this->increment('bezorgkratten'))
                                    )
                                    ->prefixAction(
                                        Action::make('decrement')
                                            ->icon('heroicon-m-minus')
                                            ->size('xl')
                                            ->action(fn ($state) => $this->decrement('bezorgkratten'))
                                    ),

                                TextInput::make('bierkratten')
                                    ->label('Bierkratten (€3.90)')
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->minValue(0)
                                    ->afterStateUpdated(fn () => $this->calculateTotal())
                                    ->suffixAction(
                                        Action::make('increment')
                                            ->icon('heroicon-m-plus')
                                            ->size('xl')
                                            ->action(fn ($state) => $this->increment('bierkratten'))
                                    )
                                    ->prefixAction(
                                        Action::make('decrement')
                                            ->icon('heroicon-m-minus')
                                            ->size('xl')
                                            ->action(fn ($state) => $this->decrement('bierkratten'))
                                    ),

                                TextInput::make('bierflesjes')
                                    ->label('Bierflesjes (€0.10)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->calculateTotal())
                                    ->suffixAction(
                                        Action::make('increment')
                                            ->icon('heroicon-m-plus')
                                            ->size('xl')
                                            ->action(fn ($state) => $this->increment('bierflesjes'))
                                    )
                                    ->prefixAction(
                                        Action::make('decrement')
                                            ->icon('heroicon-m-minus')
                                            ->size('xl')
                                            ->action(fn ($state) => $this->decrement('bierflesjes'))
                                    ),

                                TextInput::make('pet_klein_blikje')
                                    ->label('PET Klein/Blikjes (€0.15)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->calculateTotal())
                                    ->suffixAction(
                                        Action::make('increment')
                                            ->icon('heroicon-m-plus')
                                            ->size('xl')
                                            ->action(fn ($state) => $this->increment('pet_klein_blikje'))
                                    )
                                    ->prefixAction(
                                        Action::make('decrement')
                                            ->icon('heroicon-m-minus')
                                            ->size('xl')
                                            ->action(fn ($state) => $this->decrement('pet_klein_blikje'))
                                    ),

                                TextInput::make('pet_groot')
                                    ->label('PET Groot (€0.25)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->calculateTotal())
                                    ->suffixAction(
                                        Action::make('increment')
                                            ->icon('heroicon-m-plus')
                                            ->size('xl')
                                            ->action(fn ($state) => $this->increment('pet_groot'))
                                    )
                                    ->prefixAction(
                                        Action::make('decrement')
                                            ->icon('heroicon-m-minus')
                                            ->size('xl')
                                            ->action(fn ($state) => $this->decrement('pet_groot'))
                                    ),

                                TextInput::make('overig')
                                    ->label('Overig bedrag')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('€')
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->calculateTotal()),

                                TextInput::make('totaal')
                                    ->label('Totaal statiegeld')
                                    ->prefix('€')
                                    ->disabled()
                                    ->default(0),

                                TextInput::make('verschil')
                                    ->label('Te betalen (bon - statiegeld)')
                                    ->prefix('€')
                                    ->disabled()
                                    ->default(0),
                            ]),
                    ]),
                Actions::make([
                    Action::make('reset')
                        ->label('Reset')
                        ->action('resetFields')
                        ->color('danger')
                        ->icon('heroicon-o-arrow-path')
                        ->size('lg')
                        ->button(),
                ]),
            ]);
    }

    protected function calculateTotal(): void
    {
        $bezorgkratten = (float)($this->data['bezorgkratten']) * 5;
        $bierkratten = (float)($this->data['bierkratten']) * 3.90;
        $bierflesjes = (float)($this->data['bierflesjes']) * 0.10;
        $petKleinBlikje = (float)($this->data['pet_klein_blikje']) * 0.15;
        $petGroot = (float)($this->data['pet_groot']) * 0.25;
        $overig = (float)($this->data['overig']);

        $totaal = $bezorgkratten + $bierkratten + $bierflesjes + $petKleinBlikje + $petGroot + $overig;
        $this->data['totaal'] = number_format($totaal, 2);
        $this->data['verschil'] = number_format((float)($this->data['bon_bedrag']) - $totaal, 2);
    }
    protected function increment(string $field): void
    {
        $this->data[$field]++;
        $this->calculateTotal();
    }

    protected function decrement(string $field): void
    {
        if ($this->data[$field] > 0) {
            $this->data[$field]--;
            $this->calculateTotal();
        }
    }

    public function resetFields(): void
    {
        $this->data = [
            'bezorgkratten' => 0,
            'bierkratten' => 0,
            'bierflesjes' => 0,
            'pet_klein_blikje' => 0,
            'pet_groot' => 0,
        ];
        $this->form->fill($this->data);
    }
}
