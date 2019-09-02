<?php

namespace SertxuDeveloper\Lyra\Http\Controllers\CRUD;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use SertxuDeveloper\Lyra\Http\Controllers\DatatypesController;
use SertxuDeveloper\Lyra\Lyra;

class CreateController extends DatatypesController {

  /**
   * Get the Lyra resource creation form for the specific resource.
   *
   * @param Request $request
   * @param string $resource Resource slug
   *
   * @return HttpResponseException | array
   */
  public function create(Request $request, string $resource) {
    /** Check if the user has permission to create the $resource requested */
    if (config('lyra.authenticator') == 'lyra') if (!Lyra::auth()->user()->hasPermission('create_' . $resource)) abort(403);

    /** Get the Lyra resource from the global array and create a new model instance in the new Lyra resource instance*/
    $resourcesNamespace = Lyra::getResources()[$resource];
    $model = $resourcesNamespace::$model;
    $resourceCollection = new $resourcesNamespace(new $model);

    /** Return the Lyra resource collection with type 'create' as an array */
    return $resourceCollection->getCollection($request, 'create');
  }

  /**
   * Create a new model instance and store it in the database.
   *
   * @param Request $request
   * @param string $resource Resource slug
   *
   * @return HttpResponseException | null
   */
  public function store(Request $request, string $resource) {
    /** Check if the user has permission to create the $resource requested */
    if (config('lyra.authenticator') == 'lyra') if (!Lyra::auth()->user()->hasPermission('create_' . $resource)) abort(403);

    /** Get the Lyra resource from the global array and create a new model instance */
    $resourcesNamespace = Lyra::getResources()[$resource];
    $model = $resourcesNamespace::$model;
    $resource = new $model;

    /** Get the data from the request and prepare it to be processed */
    $collection = $request->post('collection');
    $collection = json_decode($collection, true);
    $values = collect($collection);

    /** Get the Lyra resource fields to be able to call the method 'saveValue' and 'syncRelationship' at each field */
    $fields = $resourcesNamespace::getFields($resource);

    /** Filter the fields which should not be included in the database because were hidden or is the primary key */
    $fields = $fields->filter(function ($field) use ($resourcesNamespace) {
      $permission = $field->getPermissions();
      if ($field->isPrimary($resourcesNamespace)) return false;
      return !$permission['hideOnEdit'];
    })->values();

    /** Process first the common fields with a column in the database */
    $fields->each(function ($field, $key) use ($values, $resource) {
      if (method_exists($field, 'saveValue')) $field->saveValue($values[$key], $resource);
    });

    /** Save the model to get the $id */
    $resource->saveOrFail();

    /** Process the relationships fields and create it with the key $id obtained previously */
    $fields->each(function ($field, $key) use ($values, $resource) {
      if (method_exists($field, 'syncRelationship')) $field->syncRelationship($values[$key], $resource);
    });

    return null;
  }
}