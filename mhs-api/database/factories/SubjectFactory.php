<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Models\Subject;
use Faker\Generator as Faker;

$factory->define(Subject::class, function (Faker $faker) {
    return [
      'subject_name' => $faker->words($nb = 2, $asText = true),
      'display_name' => $faker->words($nb = 2, $asText = true),
      'staff_notes' => null,
      'keywords' => null,
      'loc' => null
    ];
});
