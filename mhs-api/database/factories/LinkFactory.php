<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Models\Link;
use Faker\Generator as Faker;

$factory->define(Link::class, function (Faker $faker) {
    return [
        'type' => 'source',
        'authority' => 'snac',
        'authority_id' => '12345',
        'display_title' => $faker->domainName,
        'url' => $faker->url,
        'notes' => $faker->text($maxNbChars = 25)
    ];
});
