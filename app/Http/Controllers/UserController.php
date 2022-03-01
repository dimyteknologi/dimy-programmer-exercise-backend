<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User as Model;
use App\Http\Requests\RegisterRequest as ModelRequest;
use App\Http\Resources\UserResource as ModelResource;
use App\Http\Resources\SuccessResponseResource;
use App\Http\Resources\ErrorResponseResource;
use App\Exports\UserReport as ModelReport;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission.model:API_USER')->except([
            'assignPermissions', 'getPermission'
        ]);
    }

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
            'name' => \Illuminate\Validation\Rule::unique('users')->ignore($user),
            'email' => 'required',
            'email' => \Illuminate\Validation\Rule::unique('users')->ignore($user),
            'password' => 'required'
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
    
    /**
     * Assign permission to user
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function assignPermissions(Request $request, $id)
    {   
        try {
            $user = Model::findOrFail($id);
            $user->assignPermission($request->permission);

            return new SuccessResponseResource(__('user.permission.assign.success'));
        } catch (\Exception $e) {
            $error_http_code = 500;
            $error_message = __('user.permission.assign.failed');

            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                $error_http_code = 404;
                $error_message = __('user.permission.assign.empty');
            }

            return new ErrorResponseResource($error_message, $error_http_code);
        }
    }
    
    /**
     * Generate and download report
     *
     * @param  mixed $request
     * @return void
     */
    public function downloadReport(Request $request)
    {
        try {
            $report_filename = 'UserReport';

            $models = Model::getModelData($request->all(), true);

            $export_lists = $models->get();
            $export_summary = [];
            $export_summary['total'] = $models->count();

            if (isset($request->type) && $request->type == 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new ModelReport($export_lists, $export_summary), $report_filename . '.xlsx');
            } else {
                return (new ModelReport($export_lists, $export_summary))->download($report_filename . '.pdf', \Maatwebsite\Excel\Excel::MPDF);
            }
        } catch (\Exception $e) {
            return new ErrorResponseResource(__('general.report.failed'), 500);
        }
    }

    public function getPermission()
    {
        return new SuccessResponseResource(__('general.request.success'), 200, ['permissions' => Model::getPermissionLists()->toArray()]);
        
    }
}
