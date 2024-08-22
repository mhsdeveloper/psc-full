<?php

namespace App\Http\Controllers;

use Models\Description;
use Illuminate\Http\Request;
use App\Http\Resources\Link as LinkResource;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * @group Description
 *
 * APIs for managing Descriptions
 */
class NoteController extends BaseController
{
    /**
     * Browse

     * @urlParam per_page optional Limit page results. Example: 5
     * @urlParam page optional Page number to load: Example: 2
     * 
     * @return Response
     */
    public function index(Request $request)
    {
        return NoteResource::collection(Note::paginate($request->query('per_page') ?? 10));
    }

    /**
     * Read
     * 
     * Retrieve a specific link 
     *
     * @param  int  $id
     * @urlParam id required The ID of the Link. Example: 3
     * @return Response
     * 
     * @response {

     * }
     * 
     * @response 404 {
     *      "message": "No query results for model"
     * }
     */
    public function show($id)
    {
        return new NoteResource(Note::findorfail($id));
    }

     /**
     * Edit
     *
     * @param  Request  $request
     * @param  string  $id

     * @return Response
     */
    public function update(Request $request, $id)
    {
		if(!\Publications\StaffUser::isAtLeastNamesEditor()){
			return response(["error" => "You must be an editor to do this"]);
		}

       Note::findOrFail($id)->update(
            $request->only([
                'id',
                'project_id',
                'name_id', 
                'notes',
            ])
        );

        return response(null, 204);
    }    
    
    /**
     * Add
     *
     * Create a new link
     * 
     * @param  Request  $request

     * @return Response
     * 
     * @response {
     * }
     */
    public function store(Request $request)
    {
		if(!\Publications\StaffUser::isAtLeastNamesEditor()){
			return response(["error" => "You must be an editor to do this"]);
		}

		$description = Note::create(
            $request->only([
                'id',
                'project_id',
                'name_id', 
                'notes',
            ])
        );

        return response(new NoteResource($description), 201);
    }

     /**
     * Delete 
     * 
     * Remove a specific link 
     * 
     * @param  Request  $request
     * @param  string  $id
     * @urlParam id required The ID of the Link. Example: 3
     * @return Response
     */
    public function delete($id)
    {
		if(!\Publications\StaffUser::isAtLeastNamesEditor()){
			return response(["error" => "You must be an editor to do this"]);
		}


        Note::findOrFail($id)->delete();

        return response(null, 204);
    }
}