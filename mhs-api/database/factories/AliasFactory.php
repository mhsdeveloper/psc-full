<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Models\Alias;
use Faker\Generator as Faker;


$factory->define(Alias::class, function (Faker $faker) {
    return [
        'family_name' => $faker->lastName,
        'given_name' => $faker->firstName,
        'type' => 'role',
        'suffix' => $faker->suffix,
        'public_notes' => $faker->text($maxNbChars = 200),
        'staff_notes' => $faker->text($maxNbChars = 200)
    ];
});
