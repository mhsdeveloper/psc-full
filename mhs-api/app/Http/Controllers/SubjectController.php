<?php

namespace App\Http\Controllers;

use Models\Subject;
use Illuminate\Http\Request;
use App\Http\Resources\Link as LinkResource;
use App\Http\Resources\Subject as SubjectResource;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * @group Subjects
 *
 * APIs for managing subjects
 */
class SubjectController extends BaseController
{
    /**
     * Browse
     * 
     * Retrieve a list of Subjects
     *
     * @urlParam per_page optional Limit page results. Example: 5
     * @urlParam page optional Page number to load: Example: 2
     * 
     * @return Response
     */
    public function index(Request $request)
    {
        return SubjectResource::collection(Subject::paginate($request->query('per_page') ?? 10));
    }

    /**
     * Read 
     * 
     * Retrieve the specified Subject
     *
     * @param  int  $id
     * @urlParam id required The ID of the Subject. Example: 3
     * @return Response
     * 
     * @response {
     *   "id": "3",
     *   "subject_name": "grandchild",
     *   "display_name": "this is a grandchild",
     *   "children": []
     * }
     * 
     * @response 404 {
     *      "message": "No query results for model"
     * }
     */
    public function show($id)
    {
        return new SubjectResource(Subject::with('descendants')->findorfail($id));
    }

     /**
     * Edit 
     * 
     * Update the specified Subject
     *
     * @param  Request  $request
     * @param  string  $id
     * @urlParam id required The ID of the Subject. Example: 3
     * @bodyParam subject_name string optional The subject name of the Subject.
     * @bodyParam display_name string optional The display name of the Subject.
     * @bodyParam staff_notes string optional The staff notes of the Subject.
     * @bodyParam keywords string optional The keywords of the subject.
     * @bodyParam loc string optional The loc of the subject.
     * @bodyParam parent_id string optional The parent id of the subject.
     * @return Response
     */
    public function update(Request $request, $id)
    {
        Subject::findOrFail($id)->update(
            $request->only([
                'subject_name', 
                'display_name', 
                'staff_notes',
                'keywords',
                'loc',
                'parent_id'
            ])
        );

        return response(null, 204);
    }
    
    /**
     * Add 
     * 
     * Create a new Subject
     *
     * @param  Request  $request
     * @bodyParam subject_name string required The subject name of the Subject.
     * @bodyParam display_name string required The display name of the Subject.
     * @bodyParam staff_notes string optional The staff notes of the Subject.
     * @bodyParam keywords string optional The keywords of the subject.
     * @bodyParam loc string optional The loc of the subject.
     * @bodyParam parent_id string optional The parent id of the subject.
     * @return Response
     * 
     * @response {
     *   "id": "3",
     *   "subject_name": "grandchild",
     *   "display_name": "this is a grandchild"
     *   "staff_notes": null,
     *   "keywords": null,
     *   "loc": null
     * } 
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'subject_name' => 'required',
            'display_name' => 'required'
        ]);

        $subject = Subject::create(
            $request->only([
                'subject_name', 
                'display_name', 
                'staff_notes',
                'keywords',
                'loc',
                'parent_id'
            ])
        );

        $subject->update([
            'first_created_by' => $request->identity->username
        ]);

        $subject = $subject->fresh();
        return response(new SubjectResource($subject), 201);
    }

     /**
     * Delete
     * 
     * Remove the specified Subject
     *
     * @param  Request  $request
     * @param  string  $id
     * @urlParam id required The ID of the Subject. Example: 3
     * @return Response
     */
    public function delete($id)
    {
        Subject::findOrFail($id)->delete();

        return response(null, 204);
    }



    /**
     *      CUSTOM METHODS
     *         OUTSIDE
     *        BASIC CRUD
     */

    /**
     * Browse Links
     * 
     * Retrieve a list of links for a specific subject
     *
     * @param  int  $id
     * @urlParam id required The ID of the subject. Example: 3
     * 
     * @return Response
     * 
     * @response [{
     *      "id": "3",
     *      "linkable_id": "4",
     *      "linkable_type": "Models\\Subject",
     *      "type": "source",
     *      "authority": "snac",
     *      "authority_id": "12345",
     *      "display_title": "this is a link",
     *      "url": "www.yahoo.com",
     *      "notes": "n/a"
     * }]
     * 
     * @response 404 {
     *      "message": "No query results for model"
     * }
     */     
    public function getLinks($id)
    {
       return LinkResource::collection(Subject::findorfail($id)->links);
    }
}