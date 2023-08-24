<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Spatie\Permission\Models\Role;
use App\Models\Permissions;
use App\Models\User;

use DataTables;
use Validator;
use Session;
use Auth;

class RoleController extends Controller{

    public function __construct(){
        $this->middleware('permission:role-list', ['only' => ['index','show']]);
        $this->middleware('permission:role-add', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','store']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        try {
            $data                       = [];
            $data['page_title']         = 'Role List';
            if(Auth::user()->can('role-add')){
                $data['btnadd'][]      = array(
                    'link'      => route('admin.role.add'),
                    'title'     => 'Add Role'
                );
            }
            $data['breadcrumb'][]       = array(
                    'link'      => route('admin.home'),
                    'title'     => 'Dashboard'
                );
            $data['breadcrumb'][]       = array(
                'title'         => 'List'
            );
            return view('admin.role.index',$data);
        } catch (\Exception $e) {

            return abort(404);
        }
    }

    public function datatable(Request $request){
        $role = Role::query()->whereNull('deleted_at');
        return Datatables::eloquent($role)
            ->addColumn('action', function($role) {
                $action      = '';
                // if(Auth::user()->can('role-list')){
                //     $action .= '<a data-toggle="tooltip" data-placement="top" title="View" data-original-title="View" class="btn btn-outline-secondary btn-sm" href="'.route("admin.role.view",$role->id).'"><i class="fas fa-eye"></i></a>&nbsp;';
                // }
                if(Auth::user()->can('role-edit')){
                    $action .= '<a data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Edit" class="btn btn-outline-secondary btn-sm" href="'.route("admin.role.edit",$role->id).'"><i class="fas fa-pencil-alt"></i></a>&nbsp;';
                }
                /*
                if(Auth::user()->can('role-delete')){
                    $action .= '<a data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete" class="btn btn-info btn-outline btn-circle m-r-5" href="javascript:void(0);" id="role_id_'.$role->id.'" data-id="'.$role->id.'" onclick="deleteRecord(this,'.$role->id.');"><i class="ti-trash"></i></a>&nbsp;';
                }
                */
                return $action;
            })
            ->editColumn('created_at', function($role) {
                return ($role->created_at)?date('d-m-Y', strtotime($role->created_at)):'';
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
            $data['page_title']         = 'Add Role';
            $data['breadcrumb'][]       = array(
                    'link'      => route('admin.home'),
                    'title'     => 'Dashboard'
                );
            if(Auth::user()->can('role-list')){
                $data['breadcrumb'][]   = array(
                    'link'      => route('admin.role.index'),
                    'title'     => 'Role'
                );
            }
            $data['breadcrumb'][]       = array(
                    'title'     => 'Add'
                );
            $data['permissions']        = Permissions::get();
            return view('admin.role.add',$data);
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
        $slug       = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/','', $request->name);
        $slug       = Str::slug($slug, "-");
        $roleId     = ($request->id)?$request->id:'';
        if($roleId!=''){
            $result = Role::where('id', '!=' , $request->id)->where('name',$slug)->count();
        }else{
            $result = Role::where('name',$slug)->count();
        }
        if($result>0){
            return response()->json(false);
        }else{
            return response()->json(true);
        }
    }

    public function store(Request $request){
        try {
            $roleId         = ($request->id)?$request->id:'';
            $rules          = [
                'name'                      => 'required|string|max:255',
                "permissions"    			=> "required|array|min:1",
            ];
            $messages       = [
                'name.required'             => 'The name field is required.',
                'permissions.required'      => 'Please select at least 1 permission.'
            ];
            $validator      =   Validator::make($request->all(), $rules,$messages);
            if ($validator->fails()) {
                if ($roleId!='') {
                    return redirect()->back()
                                ->withErrors($validator)
                                ->withInput();
                }else{
                    return redirect()->back()
                                ->withErrors($validator)
                                ->withInput();
                }
            }else{
                if ($roleId!='') {
                    $role                   = Role::findById($roleId);
                    $properties             = $role->toArray();
                    foreach ($properties as $key => $value) {
                        if(!(in_array($key, ['name','description','updated_at','updated_by']))){
                            unset($properties[$key]);
                        }
                    }
                    $slug                   = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/','', $request->name);
                    $slug                   = Str::slug($slug, "-");
                    //$role->name           = $slug;
                    $role->display_name     = $request->name;
                    $role->description      = $request->description;
                    $role->updated_by       = Auth::id();
                    $role->updated_at       = date("Y-m-d H:i:s");
                    $role->save();
                    if(!is_null($role)){
                        $userModel          = Auth::user();
                        $roleModel          = Role::where('id',$role->id)->first();
                        $updatedproperties  = $roleModel->toArray();
                        foreach ($updatedproperties as $key => $value) {
                            if(!(in_array($key, ['name','description','updated_at','updated_by']))){
                                unset($updatedproperties[$key]);
                            }
                        }
                        activity('Role')->performedOn($roleModel)->causedBy($userModel)->withProperties(['attributes' => $updatedproperties,'old'=>$properties])->log('updated');
                        $role->syncPermissions($request->permissions);
                        Session::flash('alert-message','Role updated successfully.');
                        Session::flash('alert-class','success');
                        return redirect()->route('admin.role.index');
                    }else{
                        Session::flash('alert-message','Role updated unsuccessfully.');
                        Session::flash('alert-class','error');
                        return redirect()->route('admin.role.edit',$roleId);
                    }
                }else{
                    $slug   = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/','', $request->name);
                    $slug   = Str::slug($slug, "-");
                    $role   = Role::create([
                        'name'              => $slug,
                        'display_name'      => $request->name,
                        'description'       => $request->description,
                        'created_by'        => Auth::id(),
                        'created_at'        => date("Y-m-d H:i:s"),
                        'updated_at'        => null
                    ]);
                    if(!is_null($role)){
                        $userModel          = Auth::user();
                        $roleModel          = Role::where('id',$role->id)->first();
                        $properties         = $roleModel->toArray();
                        unset($properties['id']);
                        activity('Role')->performedOn($roleModel)->causedBy($userModel)->withProperties(['attributes' => $properties])->log('created');
                        if(count($request->permissions)>0){
                            foreach ($request->permissions as $key => $permission) {
                                $role->givePermissionTo($permission);
                            }
                        }
                        Session::flash('alert-message','Role added successfully.');
                        Session::flash('alert-class','success');
                        return redirect()->route('admin.role.index');
                    }else{
                        Session::flash('alert-message','Role added unsuccessfully.');
                        Session::flash('alert-class','error');
                        return redirect()->route('admin.role.add');
                    }
                }
            }
        }catch (\Exception $e) {
            Session::flash('alert-message',$e->getMessage());
            Session::flash('alert-class','error');
            return redirect()->route('admin.role.index');
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
            $data['page_title']         = 'View Role';
            $data['breadcrumb'][]       = array(
                    'link'      => route('admin.dashboard'),
                    'title'     => 'Dashboard'
                );
            if(Auth::user()->can('role-list')){
                $data['breadcrumb'][]   = array(
                    'link'      => route('admin.role.index'),
                    'title'     => 'Role'
                );
            }
            $data['breadcrumb'][]       = array(
                    'title'     => 'View'
                );
            $role                       = Role::where('id',$id)->whereNull('deleted_at')->first();
            if($role){
                $data['role']           = $role;
                $data['permissions']    = Permissions::get();
                return view('admin.role.view',$data);
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
            $data['page_title']         = 'Edit Role';
            $data['breadcrumb'][]       = array(
                    'link'      => route('admin.home'),
                    'title'     => 'Dashboard'
                );
            if(Auth::user()->can('role-list')){
                $data['breadcrumb'][]   = array(
                    'link'      => route('admin.role.index'),
                    'title'     => 'Role'
                );
            }
            $data['breadcrumb'][]       = array(
                    'title'     => 'Edit'
                );
            $role                       = Role::where('id',$id)->whereNull('deleted_at')->first();
            if($role){
                $data['role']           = $role;
                $data['permissions']    = Permissions::get();
                return view('admin.role.edit',$data);
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
                $role = Role::where('id',$request->id)->first();
                if(!is_null($role)){
                    if($role->name!=='super-admin'){
                        $spatie_role = Role::findById($role->id);
                        if(!is_null($spatie_role)){
                            $spatie_permissions  = Permissions::get();
                            if(!is_null($spatie_permissions)){
                                foreach ($spatie_permissions as $key => $permission) {
                                    if($spatie_role->hasPermissionTo($permission->name)){
                                        $spatie_role->revokePermissionTo($permission->name);
                                    }
                                }
                            }
                            $whereFilter                = function ($q) use ($role) {
                                $q->where('id', $role->id);
                            };
                            $users                      = User::with(['roles' => $whereFilter])->whereHas('roles', $whereFilter)->get();
                            if(!is_null($users)){
                                foreach ($users as $key => $user) {
                                    $user->removeRole($spatie_role->name);
                                    $user->disableLogging();
                                    $user->deleted_by   = Auth::id();
                                    $user->save();
                                    $user->enableLogging();
                                    $user->delete();
                                }
                            }
                        }
                        $role->disableLogging();
                        $role->deleted_by               = Auth::id();
                        $role->save();
                        $role->enableLogging();
                        $role->delete();
                        if($role){
                            $response['success']        = true;
                            $response['message']        = "Role deleted successfully.";
                        }else{
                            $response['success']        = true;
                            $response['message']        = "Role deleted unsuccessfully.";
                        }
                    }else{
                        $response['success']            = false;
                        $response['message']            = "Can't delete super admin.";
                    }
                }else{
                    $response['success']                = false;
                    $response['message']                = "Oops! Something went wrong..";
                }
            } catch (\Exception $e) {
                $response['success']                    = false;
                $response['message']                    = $e->getMessage();
            }
            return response()->json($response);
        }else{
            return abort(404);
        }
    }
}
