<?php

namespace App\Console\Commands;

use Faker\Generator as Faker;
use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;

class HotFixCommand extends Command
{

    protected $signature = "hotfix:do";
    protected $description = "";

    public function handle(Faker $faker)
    {
	echo $_SERVER['DOCUMENT_ROOT'];exit();


        $document = \Models\Document::find(11);
        $document->steps()->updateExistingPivot(5, ['status' => 2]);
        foreach($document->steps as $step) {
            dd($step);
            echo $step->pivot->status;exit();
            print_r();exit();
        }
        // dd($document->steps);
    }

}
