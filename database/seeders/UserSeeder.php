<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// Reset cached roles and permissions
    	app()[PermissionRegistrar::class]->forgetCachedPermissions();

    	$role1  = Role::create([
    	    'name'          => Str::slug('Super Admin', "-"),
    	    'display_name'  => 'Super Admin',
    	    'description'   => 'Super Admin',
    	    'created_at'    => date("Y-m-d H:i:s"),
    	    'updated_at'    => null
    	]);
    	if(!is_null($role1)){
    	    $super                      = new User();
    	    $super->name                = 'Super Admin';
    	    $super->email               = 'super@gmail.com';
    	    $super->email_verified_at   = date("Y-m-d H:i:s");
    	    $super->password            = bcrypt('12345678');
    	    $super->status              = 1;
    	    $super->created_at          = date("Y-m-d H:i:s");
    	    if($super->save()){
    	        //Permission
    	        $permissionsArry        = [
    	            'User List','User Add','User Edit','User Delete',
    	            'Role List','Role Add','Role Edit','Role Delete',
    	            'Permission List','Permission Add','Permission Edit','Permission Delete',
    	        ];
    	        if(!empty($permissionsArry)){
    	            foreach ($permissionsArry as $key => $pname) {
    	                $permission             = Permission::create([
    	                    'name'          => Str::slug($pname, "-"),
    	                    'display_name'  => $pname,
    	                    'created_by'    => 1,
    	                    'created_at'    => date("Y-m-d H:i:s"),
    	                    'updated_at'    => null
    	                ]);
    	            }
    	        }
    	        $super->assignRole($role1);
    	        // Admin
    	        $role2      =  Role::create([
    	            'name'                  => Str::slug('Admin', "-"),
    	            'display_name'          => 'Admin',
    	            'description'           => 'Admin',
    	            'created_at'            => date("Y-m-d H:i:s"),
    	            'updated_at'            => null
    	        ]);
    	    }
    	}
    }
}
