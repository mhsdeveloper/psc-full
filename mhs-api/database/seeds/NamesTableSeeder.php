<?php

use Illuminate\Database\Seeder;

class NamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Models\Name::class, 50)->create()->each(function ($name) {
            $name->aliases()->save(
                factory(Models\Alias::class)->make()
            );

            $name->links()->createMany(
                factory(Models\Link::class, mt_rand(1,6))->make()->toArray()
            );            
        });
    }
}
