<?php

use App\Enums\FoodUnits;

test('it returns the correct base factor', function () {
    expect(FoodUnits::G->toBaseFactor())->toBe(1)
        ->and(FoodUnits::DKG->toBaseFactor())->toBe(10)
        ->and(FoodUnits::KG->toBaseFactor())->toBe(1000)
        ->and(FoodUnits::L->toBaseFactor())->toBe(1000);
});

test('it returns the correct base unit string', function () {
    expect(FoodUnits::KG->baseUnit())->toBe('g')
        ->and(FoodUnits::ML->baseUnit())->toBe('ml')
        ->and(FoodUnits::L->baseUnit())->toBe('ml');
});

test('it provides all available unit values', function () {
    expect(FoodUnits::values())->toContain('g', 'kg', 'ml', 'l');
});
