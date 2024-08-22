<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Models\Name;
use Faker\Generator as Faker;


$factory->define(Name::class, function (Faker $faker) {
    return [
        'family_name' => $faker->lastName,
        'given_name' => $faker->firstName,
        'suffix' => $faker->suffix,
        'date_of_birth' => $faker->dateTimeBetween($startDate = '-175 years', $endDate = '-100 years', $timezone = null)->format('Y-m-d'),
        'date_of_death' => $faker->dateTimeBetween($startDate = '-175 years', $endDate = '-100 years', $timezone = null)->format('Y-m-d'),
        'public_notes' => $faker->text($maxNbChars = 200),
        'staff_notes' => $faker->text($maxNbChars = 200)
    ];
});
