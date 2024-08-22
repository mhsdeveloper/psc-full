<?php

namespace App\Observers;

use \Models\Step;
use \Models\Document;
use \Models\DocumentStep;

class DocumentObserver
{
  /**
   * Handle the Documents "created" event.
   *
   * @param  \App\Document  $document
   * @return void
   */
  public function created(Document $document)
  {
    $steps = Step::where('project_id', 63)->get();
    foreach($steps as $step) {
      DocumentStep::create([
        'document_id' => $document->id,
        'step_id' => $step->id
      ]);
    }

  }

}