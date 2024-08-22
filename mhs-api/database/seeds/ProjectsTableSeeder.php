<?php

use Illuminate\Database\Seeder;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Models\Project::class, 5)->create()->each(function ($project) {
            $project->lists()->save(
                factory(Models\ProjectList::class)->make()
            );

            $project->subjects()->createMany(
                factory(Models\Subject::class, mt_rand(1,6))->make()->toArray()
            );            
        });
    }
}
