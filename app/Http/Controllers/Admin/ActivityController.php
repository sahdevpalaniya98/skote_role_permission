<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Activitylog\Models\Activity;
use DataTables;
use Auth;

class ActivityController extends Controller{

    public function __construct(){
        $this->middleware('permission:activity-list', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        try {
            $data               = [];
            $data['page_title'] = 'Activity Log';
            $data['breadcrumb'] = array(
                array(
                    'link'      => route('admin.home'),
                    'title'     => 'Dashboard'
                ),
                array(
                    'title'     => 'Log'
                )
            );
            return view('admin.activity.index',$data);
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function datatable(Request $request){
        if(Auth::user()->hasAnyRole('super-admin')){
            $activity = Activity::query();
        }else{
            $activity = Auth::user()->actions;
        }
        return Datatables::of($activity)
            ->addColumn('causer', function($activity) {
                if($activity->causer){
                    $causer = $activity->causer;
                    return @$causer->name;
                }else{
                    return "-";
                }
            })
            ->addColumn('subject_id', function($activity) {
                return $activity->subject_id;
            })
            ->editColumn('created_at', function($activity) {
                return date('d-m-Y h:i:s', strtotime($activity->created_at));
            })->addIndexColumn()
            ->make(true);
    }

}
