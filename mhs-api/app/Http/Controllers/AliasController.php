<?php

namespace App\Http\Controllers;

use Models\Alias;
use Illuminate\Http\Request;
use App\Http\Resources\Alias as AliasResource;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * @group Aliases
 *
 * APIs for managing aliases
 */
class AliasController extends BaseController
{
    /**
     * Browse
     * 
     * Retrieve a list of aliases
     *
     * @urlParam per_page optional Limit page results. Example: 5
     * @urlParam page optional Page number to load: Example: 2
     * 
     * @return Response
     */
    public function index(Request $request)
    {
        return AliasResource::collection(Alias::paginate($request->query('per_page') ?? 10));
    }

    /**
     * Read
     * 
     * Retrieve a specific alias 
     *
     * @param  int  $id
     * @urlParam id required The ID of the Alias. Example: 3
     * @return Response
     * 
     * @response {
     *      "id": "3",
     *      "family_name": "Jefferson",
     *      "given_name": "Thomas",
     *      "middle_name": null,
     *      "maiden_name": null,
     *      "suffix": null,
     *      "title": null,
     *      "role": null,
     *      "type": "role",
     *      "public_notes": null,
     *      "staff_notes": null
     * }
     * 
     * @response 404 {
     *      "message": "No query results for model"
     * }
     */
    public function show($id)
    {
        return new AliasResource(Alias::findorfail($id));
    }

     /**
     * Edit
     *
     * @param  Request  $request
     * @param  string  $id
     * @urlParam id required The ID of the Alias. Example: 3
     * @bodyParam name_id int optional The id of the name. Example: 1
     * @bodyParam type string optional The type of alias. Example: role
     * @bodyParam family_name string optional The family name for the alias. Example: Buren
     * @bodyParam given_name string optional The given name for the alias. Example: Martin  
     * @bodyParam middle_name string optional The middle name for the alias. Example: Van 
     * @bodyParam suffix string optional The suffix for the alias. Example: Mr.
     * @bodyParam title string optional The title for the alias. Example: President
     * @bodyParam role string optional The role for the alias.
     * @bodyParam public_notes text optional The public notes for the alias. 
     * @bodyParam staff_notes text optional The staff notes for the alias. 
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name_id' => 'sometimes|exists:names,id',
            'type' => 'sometimes|in:spelling,role'
        ]);

        Alias::findOrFail($id)->update(
            $request->only([
                'name_id',
                'type',
                'family_name',
                'given_name',
                'middle_name',
                'maiden_name',
                'suffix',
                'title',
                'role',
                'public_notes',
                'staff_notes'
            ])
        );

        return response(null, 204);
    }

    /**
     * Add
     *
     * @param  Request  $request
     * @bodyParam name_id int required The id of the name. Example: 3
     * @bodyParam type string required The type of alias. Example: role
     * @bodyParam family_name string required The family name for the alias. Example: Buren
     * @bodyParam given_name string optional The given name for the alias. Example: Martin  
     * @bodyParam middle_name string optional The middle name for the alias. Example: Van 
     * @bodyParam suffix string optional The suffix for the alias. Example: Mr.
     * @bodyParam title string optional The title for the alias. Example: President
     * @bodyParam role string optional The role for the alias.
     * @bodyParam public_notes text optional The public notes for the alias. 
     * @bodyParam staff_notes text optional The staff notes for the alias. 
     * @return Response
     * 
     * @response {
     *      "id": "3",
     *      "family_name": "Jefferson",
     *      "given_name": "Thomas",
     *      "middle_name": null,
     *      "maiden_name": null,
     *      "suffix": null,
     *      "title": null,
     *      "role": null,
     *      "type": "role",
     *      "public_notes": null,
     *      "staff_notes": null
     * }
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name_id' => 'required|exists:names,id',
            'family_name' => 'required',
            'type' => 'required|in:spelling,role'
        ]);

        $alias = Alias::create(
            $request->only([    
                'name_id',
                'type',
                'family_name',
                'given_name',
                'middle_name',
                'maiden_name',
                'suffix',
                'title',
                'role',
                'public_notes',
                'staff_notes'
            ])
        );

        return response(new AliasResource($alias), 201);
    }

     /**
     * Delete 
     * 
     * Remove a specific alias 
     *
     * @param  Request  $request
     * @param  string  $id
     * @urlParam id required The ID of the Alias. Example: 3
     * @return Response
     */
    public function delete($id)
    {
        Alias::findOrFail($id)->delete();

        return response(null, 204);
    }


    /**
     *      CUSTOM METHODS
     *         OUTSIDE
     *        BASIC CRUD
     */


    /**
     * Retrieve the name for an Alias
     *
     * @param  int  $id
     * @urlParam id required The ID of the Alias. Example: 3
     * @return Response
     */   
    public function getName($id) 
    {
        return response()->json($this->model->findOrFail($id)->name);
    }
}