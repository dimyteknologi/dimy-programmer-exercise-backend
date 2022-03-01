<?php

namespace App\Http\Controllers;

use App\Http\Resources\ErrorResponseResource;
use App\Http\Resources\SuccessResponseResource;
use Illuminate\Http\Request;
use App\Models\Employees as Model;
use App\Http\Requests\EmployeeRequest as ModelRequest;
use App\Http\Resources\EmployeeResource as ModelResource;

class EmployeeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ModelResource::collection(Model::getModelData($request->all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ModelRequest $request, Model $model)
    {
        if ($model->saveModel($request->all())) {
            return new SuccessResponseResource(__('general.crud.store.success'));
        } else {
            return new ErrorResponseResource(__('general.crud.store.failed'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return new ModelResource(Model::findOrFail($id));            
        } catch (\Exception $e) {
            return new ErrorResponseResource(__('general.crud.show.failed'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Model $user)
    {
        
        /* 
         * When updating unique column we must use different rules as store method.
         * The validation rules must exclude current data as unique data.
         */
        $validation_rules = [
            'name' => 'required',
            'employee_type_id' => 'required',
            'phone' => 'required'
        ];


        if ($request->validate($validation_rules) && $user->saveModel($request->all())) {
            return new SuccessResponseResource(__('general.crud.store.success'));
        } else {
            return new ErrorResponseResource(__('general.crud.store.failed'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if (Model::findOrFail($id)->delete()) {
                return new SuccessResponseResource(__('general.crud.destroy.success'));
            } else {
                return new ErrorResponseResource(__('general.crud.destroy.failed'), 500);
            }
        } catch (\Exception $e) {
            return new ErrorResponseResource(__('general.crud.destroy.empty'), 404);
        }
    }
    
}
