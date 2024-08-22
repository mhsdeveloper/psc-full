<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix'=>'v1', 'middleware' => 'auth'], function() use($router){

    /*
        Projects
    */
    $router->get('projects', 'ProjectController@index');
    $router->get('projects/whoami', 'ProjectController@whoami');
    $router->get('projects/{id}', 'ProjectController@show');
    $router->patch('projects/{id}', 'ProjectController@update');
    $router->post('projects', 'ProjectController@store');
    $router->delete('projects/{id}', 'ProjectController@delete');

    /*
        Names
    */
    $router->get('names/name-key-available', 'NameController@checkNameKey');
    $router->get('names/name-key-suggest', 'NameController@suggestNameKey');
    $router->get('names', 'NameController@index');
    $router->get('names/{id}', 'NameController@show');
    $router->group(['middleware' => 'permission:edit'], function()  use($router){
        $router->patch('names/{id}', 'NameController@update');
        $router->post('names', 'NameController@store');
        $router->delete('names/{id}', 'NameController@delete');  
    });
    $router->get('names/{id}/links', 'NameController@getLinks');
    $router->get('names/{id}/description/{pid}', 'NameController@getDescription');
    $router->get('recent/names', 'NameController@recentNames');

    /*
        Links
    */
    $router->get('links', 'LinkController@index');
    $router->get('links/{id}', 'LinkController@show');
    $router->group(['middleware' => 'permission:edit'], function()  use($router){
        $router->patch('links/{id}', 'LinkController@update');
        $router->post('links', 'LinkController@store');
        $router->delete('links/{id}', 'LinkController@delete');
    });

    /*
        Aliases
    */
    $router->get('aliases', 'AliasController@index');
    $router->get('aliases/{id}', 'AliasController@show');
    $router->group(['middleware' => 'permission:edit'], function()  use($router){
        $router->patch('aliases/{id}', 'AliasController@update');
        $router->post('aliases', 'AliasController@store');
        $router->delete('aliases/{id}', 'AliasController@delete');
    });

    /*
        Subjects
    */
    $router->get('subjects', 'SubjectController@index');
    $router->get('subjects/{id}', 'SubjectController@show');
    $router->group(['middleware' => 'permission:edit'], function()  use($router){
        $router->patch('subjects/{id}', 'SubjectController@update');
        $router->post('subjects', 'SubjectController@store');
        $router->delete('subjects/{id}', 'SubjectController@delete');
    });
    $router->get('subjects/{id}/links', 'SubjectController@getLinks');

    /*
        Lists
    */
    $router->get('lists', 'ProjectListController@index');
    $router->get('lists/{id}', 'ProjectListController@show');
    $router->group(['middleware' => 'permission:edit'], function()  use($router){
        $router->patch('lists/{id}', 'ProjectListController@update');
        $router->post('lists', 'ProjectListController@store');
        $router->delete('lists/{id}', 'ProjectListController@delete');
        $router->post('lists/copy', 'ProjectListController@copy');
        $router->patch('lists/{id}/name', 'ProjectListController@nameToggle');
        $router->patch('lists/{id}/subject', 'ProjectListController@subjectToggle');
    });
    
    /*
        Documents
    */    
    $router->get('documents', 'DocumentController@index');
    $router->get('documents/{id}', 'DocumentController@show');
    $router->group(['middleware' => 'permission:edit'], function()  use($router){
        $router->patch('documents/{id}', 'DocumentController@update');
        $router->patch('documents/{id}/checkout', 'DocumentController@checkout');
        $router->patch('documents/{id}/checkin', 'DocumentController@checkin');
        $router->post('documents', 'DocumentController@store');
        $router->delete('documents/{id}', 'DocumentController@delete');
        $router->patch('documents/{id}/steps', 'DocumentController@updateDocumentStep');
    });

    /*
        Steps
    */
    $router->get('steps', 'StepController@index');
    $router->get('steps/{id}', 'StepController@show');
    $router->group(['middleware' => 'permission:edit'], function()  use($router){
        $router->patch('steps/{id}', 'StepController@update');
        $router->post('steps', 'StepController@store');
        $router->delete('steps/{id}', 'StepController@delete');
    });

    /*
        Document Steps
    */    
    $router->get('document-step/{id}', 'DocumentController@show');
    $router->group(['middleware' => 'permission:edit'], function()  use($router){
    });

    /*
        Relationship Endpoints
                  for 
                Subjects
    */
    // $router->get('subjects/{id}/projects', 'SubjectController@getProjects');

    /*
        Relationship Endpoints
                  for
                Projects
    */
    $router->get('projects/{id}/lists', 'ProjectController@getLists');
    $router->get('projects/{id}/names', 'ProjectController@getNames');
    $router->get('projects/{id}/documents', 'ProjectController@getDocuments');
    $router->get('projects/{id}/subjects', 'ProjectController@getSubjects');
    $router->group(['middleware' => 'permission:edit'], function()  use($router){
	$router->patch('projects/{id}/names', 'ProjectController@toggleNames');
        $router->post('projects/{id}/names', 'ProjectController@addName');
        $router->delete('projects/{id}/names', 'ProjectController@removeName');
        $router->post('projects/{id}/subjects', 'ProjectController@addSubject');
        $router->delete('projects/{id}/subjects', 'ProjectController@removeSubject');
    });

    
    /*
        Relationship Endpoints
                  for
                Aliases
    */
    // $router->get('aliases/{id}/name', 'AliasController@getName');

});
