<?php

use Faker\Generator as Faker;

$factory->define(App\AttendanceSummary::class, function (Faker $faker) {
    return [
        'employee_id' => $faker->numberBetween($min = 1,$max = 6),
        'first_timestamp_in' => $faker->dateTime($max = 'now', $timezone = null),
        'last_timestamp_out' => $faker->dateTime($max = 'now', $timezone = null),
        'date' => $faker->date($format = 'Y-m-d', $max = 'now')
    ];
});
