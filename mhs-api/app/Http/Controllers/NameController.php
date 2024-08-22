<?php

namespace App\Http\Controllers;

use Models\Name;
use Models\Project;
use Models\ProjectMetadata;
use Models\Note;
use Illuminate\Http\Request;
use App\Http\Resources\Name as NameResource;
use App\Http\Resources\Link as LinkResource;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\QueryParams as QueryParams;

define("SNIFF_OUT_FILE", SERVER_WWW_ROOT . "/html/sniff_out.txt");
require_once(SERVER_WWW_ROOT . "/html/publications/mhs/classes/mhs/sniff.php");
require_once(SERVER_WWW_ROOT . "/html/publications/lib/classes/publications/datahelpers.php");

/**
 * @group Names
 *
 * APIs for managing names
 */
class NameController extends BaseController
{
    /**
    * Retrieve a list of names
     */
    public function index(Request $request) {

		//find any fields
		$queryStrArr = $request->query();

        // //handle specific required fields
		// foreach($queryStrArr as $param => $value){
		// 	if(Schema::hasColumn('names', $param)){
        //         $op = QueryParams::getOperator($value);
        //         $value = QueryParams::cleanValue($value);
        //         if($op == "like") $value = '%' . $value . '%';
		// 		$query->where($param, $op, $value);
		// 	}
		// }
        //handle general fuzzy search
        if(0 && $request->exists("q")){
		   $query->orWhere(function($query) use($request){
                $query->where('family_name', 'like', '%'.$request->q.'%')
                ->orWhere('given_name', 'like', '%'.$request->q.'%')
                ->orWhere('maiden_name', 'like', '%'.$request->q.'%')
                ->orWhere('middle_name', 'like', '%'.$request->q.'%')
                ->orWhere('variants', 'like', '%'.$request->q.'%')
                ->orWhere('professions', 'like', '%'.$request->q.'%')
                ->orWhere('title', 'like', '%'.$request->q.'%')
                ->orWhere('date_of_birth', 'like', '%'.$request->q.'%')
                ->orWhere('date_of_death', 'like', '%'.$request->q.'%')
                ->orWhere('name_key', 'like', '%'.$request->q.'%');
//                ->orWhere("notes", "like", '%'.$request->q.'%');
            });
        }


        // //handle notes search
        // if($request->exists("notes")){
        //     $query->where("notes", "like", '%'.$request->notes.'%');
        // } 

        // //handle name search
        // if($request->exists("name")){
        //     $name = trim($request->name);

        //     //handle last, first
        //     if(strpos($name, ",") !== false) {
        //         $parts = explode(",", $name);
        //         $family_name = trim($parts[0]);
        //         $given_name = trim($parts[1]);
        //         $query->where(function($query) use($family_name, $given_name){
        //             $query->where('family_name', 'like', '%'.$family_name.'%')
        //             ->Where('given_name', 'like', '%'.$given_name.'%');
        //         });
        //     //just a name
        //     } else {
        //         $query->where(function($query) use($name){
        //             $query->where('family_name', 'like', '%'.$name.'%')
        //             ->orWhere('given_name', 'like', '%'.$name.'%')
        //             ->orWhere('maiden_name', 'like', '%'.$name.'%')
        //             ->orWhere('middle_name', 'like', '%'.$name.'%')
        //             ->orWhere('variants', 'like', '%'.$name.'%');
        //         });
        //     }
        // }

        
        // if($request->exists("order") && $request->order == "date"){
        //     $query->orderBy("sort_birth", 'asc');
        //     $query->orderBy("sort_name", 'asc');
        // } else {
        //     $query->orderBy("sort_name", 'asc');
        //     $query->orderBy("sort_birth", 'asc');
        // }

//		return NameResource::collection($query->paginate($request->query('per_page') ?? 10));
        return $query;
    }


    public function recentNames(Request $request){
    	//find any fields
		$queryStrArr = $request->query();
		$query = Name::query();

        $query->orderBy("updated_at", 'desc');

		return NameResource::collection($query->paginate($request->query('per_page') ?? 10));
    }


    public function checkNameKey(Request $request)
    {
	    return Name::where('name_key', $request->q)->count();
    }



    public function suggestNameKey(Request $request)
    {
    	return self::createUniqueNameKey($request->q);
    }





    
    public function show($id)
    {
		if(is_numeric($id)) return new NameResource(Name::findorfail($id));

		$name_key = $id;

		//treat $id as name_key
		return new NameResource(Name::where('name_key', "=", $name_key)->first());
    }
    


    static function testDate($date){
        if(empty($date)) return true;
        preg_match("/^(\d{4}-\d{2}-\d{2})$|^(\d{4}-\d{2})$|^(\d{1,4})$/",$date, $matches);
        if(count($matches) == 0) return false;
        return true;
    }








    public function update(Request $request, $id)
    {
		if(!\Publications\StaffUser::isAtLeastNamesEditor()){
			return response(["error" => "You must be an editor to do this"]);
		}


        if(!self::testDate($request->date_of_birth)) return response(["error" => "Date of birth must be YYYY, or YYYY-MM or YYYY-MM-DD"], 422);

         if(!self::testDate($request->date_of_death)) return response(["error" => "Date of death must be YYYY, or YYYY-MM or YYYY-MM-DD"], 422);

        $nameSort = \Publications\DataHelpers::SortStringFromName($request);
        $birthSort = \Publications\DataHelpers::DateToSortInt($request->date_of_birth, $request->birth_era == "bce" ? true : false);

        $name = Name::find($id);

        $name->sort_birth = $birthSort;
        $name->sort_name = $nameSort;
        $name->update(
            $request->only([
                'family_name',
                'given_name',
                'maiden_name',
                'middle_name',
                'suffix',
                'date_of_birth',
                'date_of_death',
                'identifier',
                'first_mention',
                'verified',
				'variants',
				'professions',
				'title',
                'birth_ca',
                'death_ca',
                'birth_era',
                'death_era'
            ])
        );


        $note = $name->notes()->where("project_id", $request->identity->project_id)->first();
        if(!$note){
            $note = new Note();
            $note->project_id = $request->identity->project_id;
            $note->name_id = $id;
        }
        $note->notes = $request->staff_notes;
        $note->save();

        $metadata = $name->projectmetadata()->where("project_id", $request->identity->project_id)->first();
        if(!$metadata){
            $metadata = new ProjectMetadata();
            $metadata->project_id = $request->identity->project_id;
            $metadata->name_id = $id;
			$metadata->project_name = $request->identity->sitename;
        }
		$metadata->public = $request->visible;
        $metadata->notes = $request->public_notes;
        $metadata->save();

//        return response(null, 204);
    }    


    



    public function store(Request $request)
    {
		if(!\Publications\StaffUser::isAtLeastNamesEditor()){
			return response(["error" => "You must be an editor to do this"]);
		}

        if(!self::testDate($request->date_of_birth)) return response(["error" => "Date of birth must be YYYY, or YYYY-MM or YYYY-MM-DD"], 422);
        if(!self::testDate($request->date_of_death)) return response(["error" => "Date of death must be YYYY, or YYYY-MM or YYYY-MM-DD"], 422);


        if (is_null($request->name_key)) {
            $nameKey = strtolower($request->family_name) . "-" . strtolower($request->given_name);
        }else{
            $nameKey = $request->name_key;
        }
        $nameKey = self::createUniqueNameKey($nameKey);

        if($request->public_notes){

        }

        if($request->staff_notes){

        }



        $name = Name::create(
            $request->only([
                'family_name',
                'given_name',
                'maiden_name',
                'middle_name',
                'suffix',
                'date_of_birth',
                'date_of_death',
                'identifier',
                'first_mention',
                'verified',
				'variants',
				'professions',
				'title',
                'birth_ca',
                'death_ca',
                'birth_era',
                'death_era'
            ])
        );

        $nameSort = \Publications\DataHelpers::SortStringFromName($request);
        $birthSort = \Publications\DataHelpers::DateToSortInt($request->date_of_birth, $request->birth_era == "bce" ? true : false);

        $name->update([
            'sort_birth' => $birthSort,
            'sort_name' => $nameSort,
            'name_key' => $nameKey,
            'first_created_by' => $request->identity->username
        ]);

        $name = $name->fresh();

        //new names are ALWAYS associated with the user's project
        $project_id = $request->identity->project_id;
        Project::findorfail($project_id)->names()->attach($name->id);

        return response(new NameResource($name), 201);
    }


    static function createUniqueNameKey($nameKey){
        $nameKeyAppend = '';
        $nameKeyResults = Name::where('name_key', $nameKey . $nameKeyAppend)->first();
        $i = 1;
        while (!is_null($nameKeyResults)) {
            $i = $i + 1;
            $nameKeyAppend = $i;
            $nameKeyResults = Name::where('name_key', $nameKey . $nameKeyAppend)->first();
        }

        return $nameKey . $nameKeyAppend;
    }







    public function delete($id)
    {
        if(!\Publications\StaffUser::isAdmin()){
            return response(null, 403);
        }

        Name::findOrFail($id)->delete();
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
     * Retrieve a list of links for a specific name
     *
     * @param  int  $id
     * @urlParam id required The ID of the name. Example: 3
     * 
     * @return Response
     * 

     */     
     public function getLinks($id)
     {
        return LinkResource::collection(Name::findorfail($id)->links);
     }



    public function getProjectMetadata($id, $pid)
    {
        return ProjectMetadataResource::collection(Name::findorfail($id)->descriptions);
    }



    public function getNotes($id, $pid)
    {
        return NoteResource::collection(Name::findorfail($id)->notes);
    }

}