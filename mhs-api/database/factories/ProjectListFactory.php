<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Models\ProjectList;
use Faker\Generator as Faker;

$factory->define(ProjectList::class, function (Faker $faker) {
    return [
      'name' => $faker->words($nb = 2, $asText = true),
      'type' => 'subject'
    ];
});
