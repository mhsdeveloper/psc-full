<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Models\Project;
use Faker\Generator as Faker;


$factory->define(Project::class, function (Faker $faker) {
    return [
      'project_id' => mt_rand(100,999).'-'.mt_rand(0,9).'-'.mt_rand(100,999).'-'.mt_rand(1000,9999),
      'name' => $faker->words($nb = 3, $asText = true),
      'description' => $faker->words($nb = 8, $asText = true)
    ];
});
