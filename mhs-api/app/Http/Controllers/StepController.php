<?php

namespace App\Http\Controllers;

use Models\Step;
use Illuminate\Http\Request;
use App\Http\Resources\Step as StepResource;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * @group Step
 *
 * APIs for managing dopcuments
 */
class StepController extends BaseController
{
    /**
     * Browse
     * 
     * Retrieve a list of steps
     *
     * @urlParam per_page optional Limit page results. Example: 5
     * @urlParam page optional Page number to load: Example: 2
     * 
     * @return Response
     */
    public function index(Request $request)
    {
        return StepResource::collection(Step::paginate($request->query('per_page') ?? 10))->sortBy("order");
    }

    /**
     * Read
     * 
     * Retrieve a specific step 
     *
     * @param  int  $id
     * @urlParam id required The ID of the Step. Example: 3
     * @return Response
     * 
     * @response {
     *      "id": "3",
     *      "name": "Step 1",
     *      "order": "1",
     *      "project_id": "11-124-11246",
     *      "short_name": null,
     *      "description": null
     * }
     * 
     * @response 404 {
     *      "message": "No query results for model"
     * }
     */
    public function show($id)
    {
        return new StepResource(Step::findorfail($id));
    }        
    
     /**
     * Edit
     * 
     * Update the specified Step
     *
     * @param  Request  $request
     * @param  string  $id
     * @bodyParam project_id string required The project id of the list. Example: 123-456-789
     * @bodyParam name string required The name of the step. Example: Step 1
     * @bodyParam short_name string optional The short name of the step.
     * @bodyParam order int required The order of the step.
     * @bodyParam description string optional The description of the step.
     * @return Response
     */
    public function update(Request $request, $id)
    {
		if(!\Publications\StaffUser::isAdmin()){
			return response(["error" => "You must be an editor to do this"]);
		}

        $this->validate($request, [
            'project_id' => 'required|exists:projects,id',
            'name' => 'required',
            'short_name' => 'present|nullable',
            'order' => 'required',
            'description' => 'present|nullable'
        ]);

        Step::findOrFail($id)->update(
            $request->only([
              'project_id',
              'name',
              'short_name',
              'order',
              'description'
            ])
        );

        return response(null, 204);
    }    
        
    /**
     * Add
     *
     * @param  Request  $request
     * @bodyParam project_id string required The project id of the list. Example: 123-456-789
     * @bodyParam name string required The name of the step. Example: Step 1
     * @bodyParam short_name string optional The short name of the step.
     * @bodyParam order int required The order of the step.
     * @bodyParam description string optional The description of the step.
     * @return Response
     * 
     * @response {
     *      "id": "3",
     *      "name": "Step 1",
     *      "order": "1",
     *      "project_id": "11-124-11246",
     *      "short_name": null,
     *      "description": null
     * }
     */
    public function store(Request $request)
    {
		if(!\Publications\StaffUser::isAdmin()){
			return response(["error" => "You must be an editor to do this"]);
		}

        $this->validate($request, [
            'project_id' => 'required|exists:projects,id',
            'name' => 'required',
            'short_name' => 'present|nullable',
            'order' => 'required',
            'description' => 'present|nullable'
        ]);

        $step = Step::create(
            $request->only([   
                'project_id',
                'name',
                'short_name',
                'order',
                'description'
            ])
        );

        return response(new StepResource($step), 201);
    }    

     /**
     * Delete 
     * 
     * Remove a specific step
     * 
     * @param  Request  $request
     * @param  string  $id
     * @urlParam id required The ID of the step. Example: 3
     * @return Response
     */
    public function delete($id)
    {
		if(!\Publications\StaffUser::isAdmin()){
			return response(["error" => "You must be an editor to do this"]);
		}
		
        Step::findOrFail($id)->delete();

        return response(null, 204);
    }    
}