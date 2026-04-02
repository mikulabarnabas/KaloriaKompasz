<?php

use App\Enums\WorkoutUnits;

test('workout units return correct base factors', function () {
    expect(WorkoutUnits::MINUTES->toBaseFactor())->toBe(1.0)
        ->and(WorkoutUnits::HOURS->toBaseFactor())->toBe(60.0)
        ->and(WorkoutUnits::M->toBaseFactor())->toBe(1.0)
        ->and(WorkoutUnits::KM->toBaseFactor())->toBe(1000.0)
        ->and(WorkoutUnits::STEPS->toBaseFactor())->toBe(1.0);
});

test('workout units provides correct static values', function () {
    expect(WorkoutUnits::values())->toContain('minutes', 'hours', 'm', 'km', 'steps')
        ->and(WorkoutUnits::baseValues())->toBe(['minutes', 'm']);
});

it('can be instantiated from a string value', function () {
    expect(WorkoutUnits::from('km'))->toBe(WorkoutUnits::KM);
});