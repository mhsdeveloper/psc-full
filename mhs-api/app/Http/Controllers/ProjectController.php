<?php

namespace App\Http\Controllers;

use Models\Name;
use Models\Project;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\Name as NameResource;
use App\Http\Resources\Document as DocumentResource;
use App\Http\Resources\Subject as SubjectResource;
use App\Http\Resources\Project as ProjectResource;
use App\Http\Resources\ProjectList as ProjectListResource;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * @group Projects
 *
 * APIs for managing projects
 */
class ProjectController extends BaseController
{

    public function whoami(Request $request)
    {
	return json_encode($request->identity);
    }

    /**
     * Browse
     * 
     * Retrieve a list of Projects
     *
     * @urlParam per_page optional Limit page results. Example: 5
     * @urlParam page optional Page number to load: Example: 2
     * 
     * @return Response
     */
    public function index(Request $request)
    {
        return ProjectResource::collection(Project::paginate($request->query('per_page') ?? 10));
    }

    /**
     * Read 
     * 
     * Retrieve the specified Project
     *
     * @param  int  $id
     * @urlParam id required The ID of the Project. Example: 3
     * @return Response
     * 
     * @response {
     *   "id": "10",
     *   "project_id": "111-5-585-156",
     *   "name": "another test",
     *   "description": "testing"
     * }
     * 
     * @response 404 {
     *      "message": "No query results for model"
     * }
     */
    public function show($id)
    {
        return new ProjectResource(Project::findorfail($id));
    }

     /**
     * Edit 
     * 
     * Update the specified Project
     *
     * @param  Request  $request
     * @param  string  $id
     * @urlParam id required The ID of the Project. Example: 3
     * @bodyParam project_id string optional The project id of the Project. Example: 111-5-585-1566
     * @bodyParam name string optional The name of the Project. Example: 1800s Project
     * @bodyParam description string optional The description of the Project. 
     * @return Response
     */
    public function update(Request $request, $id)
    {
		if(!\Publications\StaffUser::isAtLeastNamesEditor()){
			return response(["error" => "You must be an editor to do this"]);
		}

        $this->validate($request, [
            'project_id' => 'sometimes',
            'name' => 'sometimes'
        ]);

        Project::findOrFail($id)->update(
            $request->only([
                'project_id', 
                'name', 
                'description'
            ])
        );

        return response(null, 204);
    }
    
    /**
     * Add 
     * 
     * Create a new Project
     *
     * @param  Request  $request
     * @bodyParam project_id string required The project id of the Project. Example: 111-5-585-1566
     * @bodyParam name string required The name of the Project. Example: 1800s Project
     * @bodyParam description required optional The description of the Project. 
     * @return Response
     * 
     * @response {
     *   "id": "10",
     *   "project_id": "111-5-585-156",
     *   "name": "another test",
     *   "description": "testing"
     * }
     */
    public function store(Request $request)
    {
		if(!\Publications\StaffUser::isAtLeastNamesEditor()){
			return response(["error" => "You must be an editor to do this"]);
		}

        $this->validate($request, [
            'project_id' => 'required',
            'name' => 'required'
        ]);

        $project = Project::create(
            $request->only([             
                'project_id', 
                'name', 
                'description'
            ])
        );

        return response(new ProjectResource($project), 201);
    }

     /**
     * Delete
     * 
     * Remove the specified Project
     *
     * @param  Request  $request
     * @param  string  $id
     * @urlParam id required The ID of the Project. Example: 3
     * @return Response
     */
    public function delete($id)
    {
		if(!\Publications\StaffUser::isAtLeastNamesEditor()){
			return response(["error" => "You must be an editor to do this"]);
		}

        Project::findOrFail($id)->delete();

        return response(null, 204);
    }



    /**
     *      CUSTOM METHODS
     *         OUTSIDE
     *        BASIC CRUD
     */

    /**
     * Browse Lists
     * 
     * Retrieve lists for a Project
     *
     * @param  int  $id
     * @return Response
     */    
    public function getLists($id)
    {
        return ProjectListResource::collection(Project::findorfail($id)->lists);
    }

    /**
     * Browse Names
     * 
     * Retrieve names for a Project
     *
     * @param  int  $id
     * @return Response
     */    
    public function getNames($id, Request $request)
    {
	return Project::findorfail($id)->names()
		->where(
			function($query) use ($request) {
				return $query
					->where('family_name', 'like', '%'.$request->q.'%')
					->orWhere('given_name', 'like', '%'.$request->q.'%')
					->orWhere('maiden_name', 'like', '%'.$request->q.'%')
					->orWhere('middle_name', 'like', '%'.$request->q.'%')
					->orWhere('title', 'like', '%'.$request->q.'%')
					->orWhere('name_key', 'like', '%'.$request->q.'%')
					->orWhere('date_of_birth', 'like', '%'.$request->q.'%')
					->orWhere('date_of_death', 'like', '%'.$request->q.'%');
		})
		->orderBy('created_at', 'desc')->paginate($request->query('per_page') ?? 100);
    }

	    /**
     * Browse Documents
     * 
     * Retrieve Documents for a Project
     *
     * @param  int  $id
     * @return Response
     */    
    public function getDocuments($id)
    {
        return DocumentResource::collection(Project::findorfail($id)->documents);
    }

    /**
     * Browse Subjects
     * 
     * Retrieve subjects for a Project
     *
     * @param  int  $id
     * @return Response
     */    
    public function getSubjects($id)
    {
        return SubjectResource::collection(Project::findorfail($id)->subjects);
    }


    public function toggleNames(Request $request, $id)
    {
	Project::findorfail($id)->names()->toggle($request->input('name_ids'));
	
	return response(null, 200);
    }



    /**
     * Add Name
     * 
     * Add Name to a Project
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */        
    public function addName(Request $request, $id)
    {
        $this->validate($request, [
            'name_id' => 'required|exists:names,id'
        ]);

        Project::findorfail($id)->names()->attach($request->input('name_id'));

        return response(null, 201);
    }

    /**
     * Add Subject
     * 
     * Add Subject to a Project
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */        
    public function addSubject(Request $request, $id)
    {
        $this->validate($request, [
            'subject_id' => 'required|exists:subjects,id'
        ]);

        Project::findorfail($id)->subjects()->attach($request->input('subject_id'));

        return response(null, 201);
    }

    /**
     * Delete Name
     * 
     * Remove name from a Project
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */         
    public function removeName(Request $request, $id)
    {
        $this->validate($request, [
            'name_id' => 'required|exists:names,id'
        ]);

        Project::findorfail($id)->names()->detach($request->input('name_id'));

        return response(null, 204);
    }

    /**
     * Delete Subject
     * 
     * Remove subject from a Project
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */         
    public function removeSubject(Request $request, $id)
    {
        $this->validate($request, [
            'subject_id' => 'required|exists:subjects,id'
        ]);

        Project::findorfail($id)->subjects()->detach($request->input('subject_id'));

        return response(null, 204);
    }
}
