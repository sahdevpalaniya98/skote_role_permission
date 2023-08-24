<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Permissions;

use DataTables;
use Validator;
use Session;
use Auth;

class PermissionController extends Controller{

    public function __construct(){
        $this->middleware('permission:permission-list', ['only' => ['index','show']]);
        $this->middleware('permission:permission-add', ['only' => ['create','store']]);
        $this->middleware('permission:permission-edit', ['only' => ['edit','store']]);
        $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        try {
            $data                       = [];
            $data['page_title']         = 'Permission List';
            
            if(Auth::user()->can('permission-add')){
                $data['btnadd'][]      = array(
                    'link'      => route('admin.permission.add'),
                    'title'     => 'Add Permission'
                );
            }
            
            $data['breadcrumb'][]       = array(
                    'link'      => route('admin.home'),
                    'title'     => 'Dashboard'
                );
            $data['breadcrumb'][]       = array(
                    'title'     => 'List'
                );
            return view('admin.permission.index',$data);
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    public function datatable(Request $request){
        $permission = Permissions::query();
        return Datatables::eloquent($permission)
            ->addColumn('action', function($permission) {
                $action      = '-';
                // if(Auth::user()->can('permission-list')){
                //     $action .= '<a data-toggle="tooltip" data-placement="top" title="View" data-original-title="View" class="btn btn-info btn-outline btn-circle m-r-5" href="'.route("admin.permission.view",$permission->id).'"><i class="ti-eye"></i></a>&nbsp;';
                // }
                
                // if(Auth::user()->can('permission-edit')){
                //     $action .= '<a data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Edit" class="btn btn-info btn-outline btn-circle m-r-5" href="'.route("admin.permission.edit",$permission->id).'"><i class="ti-pencil-alt"></i></a>&nbsp;';
                // }
                // if(Auth::user()->can('permission-delete')){
                //     $action .= '<a data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete" class="btn btn-info btn-outline btn-circle m-r-5" href="javascript:void(0);" id="permission_id_'.$permission->id.'" data-id="'.$permission->id.'" onclick="deleteRecord(this,'.$permission->id.');"><i class="ti-trash"></i></a>&nbsp;';
                // }
               
                return $action;
            })
            ->editColumn('created_at', function($permission) {
                return ($permission->created_at)?date('d-m-Y', strtotime($permission->created_at)):'';
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(created_at,'%d-%m-%Y') like ?", ["%$keyword%"]);
            })
            ->rawColumns(['action'])->addIndexColumn()
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        try {
            $data                       = [];
            $data['page_title']         = 'Add Permission';
            $data['breadcrumb'][]       = array(
                    'link'      => route('admin.home'),
                    'title'     => 'Dashboard'
                );
            if(Auth::user()->can('permission-list')){
                $data['breadcrumb'][]   = array(
                    'link'      => route('admin.permission.index'),
                    'title'     => 'Permission'
                );
            }
            $data['breadcrumb'][]       = array(
                    'title'     => 'Add'
                );
            return view('admin.permission.add',$data);
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function exists(Request $request){
        $slug           = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/','', $request->name);
        $slug           = Str::slug($slug, "-");
        $permissionId   = ($request->id)?$request->id:'';
        if($permissionId!=''){
            $result = Permissions::where('id', '!=' , $request->id)->where('name',$slug)->where('guard_name','web')->count();
        }else{
            $result = Permissions::where('name',$slug)->where('guard_name','web')->count();
        }
        if($result>0){
            return response()->json(false);
        }else{
            return response()->json(true);
        }
    }

    public function store(Request $request){
        try {
            $permissionId   = ($request->id)?$request->id:'';
            $rules          = ['name' => 'required|string|max:255'];
            $messages       = ['name.required' => 'The name field is required.'];
            $validator      = Validator::make($request->all(), $rules,$messages);
            if ($validator->fails()) {
                if ($permissionId!='') {
                    return redirect()->back()
                                ->withErrors($validator)
                                ->withInput();
                }else{
                    return redirect()->back()
                                ->withErrors($validator)
                                ->withInput();
                }
            }else{
                if ($permissionId!='') {
                    $slug       = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/','', $request->name);
                    $slug       = Str::slug($slug, "-");
                    $permission = Permission::findById($permissionId);
                    if($permission){
                        $permission->name           =   $slug;
                        $permission->display_name   =   $request->name;
                        $permission->updated_by     =   Auth::id();
                        $permission->updated_at     =   date("Y-m-d H:i:s");
                        if($permission->save()){
                            $userModel              =   Auth::user();
                            $permissionModel        =   Permissions::where('id',$permission->id)->first();
                            Session::flash('alert-message','Permission updated successfully.');
                            Session::flash('alert-class','success');
                            return redirect()->route('admin.permission.index');
                        }else{
                            Session::flash('alert-message','Permission updated unsuccessfully.');
                            Session::flash('alert-class','error');
                            return redirect()->route('admin.permission.edit',$permissionId);
                        }
                    }else{
                        Session::flash('alert-message','Permission record not found.');
                        Session::flash('alert-class','error');
                        return redirect()->route('admin.permission');
                    }
                } else {
                    $slug       =   preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/','', $request->name);
                    $slug       =   Str::slug($slug, "-");
                    $permission =  Permission::create([
                        'guard_name'        =>  'web',
                        'name'              =>  $slug,
                        'display_name'      =>  $request->name,
                        'created_by'        =>  Auth::id(),
                        'created_at'        =>  date("Y-m-d H:i:s"),
                        'updated_at'        =>  null
                    ]);
                    if($permission){
                        $userModel          =   Auth::user();
                        $permissionModel    =   Permissions::where('id',$permission->id)->first();
                        Session::flash('alert-message','Permission added successfully.');
                        Session::flash('alert-class','success');
                        return redirect()->route('admin.permission.index');
                    }else{
                        Session::flash('alert-message','Permission added unsuccessfully.');
                        Session::flash('alert-class','error');
                        return redirect()->route('admin.permission.add');
                    }
                }
            }
        }catch (\Exception $e) {
            Session::flash('alert-message',$e->getMessage());
            Session::flash('alert-class','error');
            return redirect()->route('admin.permission.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        try {
            $data                       = [];
            $data['page_title']         = 'View Permission';
            $data['breadcrumb'][]       = array(
                    'link'      => route('admin.dashboard'),
                    'title'     => 'Dashboard'
                );
            if(Auth::user()->can('permission-list')){
                $data['breadcrumb'][]   = array(
                    'link'      => route('admin.permission'),
                    'title'     => 'Permission'
                );
            }
            $data['breadcrumb'][]       = array(
                    'title'     => 'View'
                );
            $permission                 = Permissions::where('id',$id)->first();
            if($permission){
                $data['permission']     = $permission;
                return view('admin.permission.view',$data);
            }else{
                return abort(404);
            }
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        try {
            $data                       = [];
            $data['page_title']         = 'Edit Permission';
            $data['breadcrumb'][]       = array(
                    'link'      => route('admin.dashboard'),
                    'title'     => 'Dashboard'
                );
            if(Auth::user()->can('permission-list')){
                $data['breadcrumb'][]   = array(
                    'link'      => route('admin.permission'),
                    'title'     => 'Permission'
                );
            }
            $data['breadcrumb'][]       = array(
                    'title'     => 'Edit'
                );
            $permission                 = Permissions::where('id',$id)->first();
            if($permission){
                $data['permission']     = $permission;
                return view('admin.permission.edit',$data);
            }else{
                return abort(404);
            }
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request){
        if ($request->ajax()) {
            try {
                $permission = Permission::findById($request->id);
                if(!is_null($permission)){
                    $roles  = Role::all();
                    if(!is_null($roles)){
                        foreach ($roles as $key => $role) {
                            if($role->hasPermissionTo($permission->name)){
                                $role->revokePermissionTo($permission->name);
                            }
                        }
                    }
                    $permission                     = Permissions::where('id',$request->id)->first(); // activity log entries
                    $permission->disableLogging();
                    $permission->deleted_by         = Auth::id();
                    $permission->save();
                    $permission->enableLogging();
                    if($permission->delete()){
                        $response['success']        = true;
                        $response['message']        = "Permission deleted successfully.";
                    }else{
                        $response['success']        = false;
                        $response['message']        = "Permission deleted unsuccessfully.";
                    }
                }else{
                    $response['success']            = false;
                    $response['message']            = "Permission record not found.";
                }
            } catch (\Exception $e) {
                $response['success']                = false;
                $response['message']                = $e->getMessage();
            }
            return response()->json($response);
        }else{
            return abort(404);
        }
    }

}
