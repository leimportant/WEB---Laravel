<?php

namespace App\Http\Controllers\Admin;

use App\Models\RoleUsers;
use App\Models\Users;
use App\Models\Roles;
use App\Models\PemissionRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;

class RoleUsersController extends Controller
{
	public function index()
    {
     if (! Gate::allows('superadmin')) {
            return abort(401);
        }
        $datas = Users::all();

        return view('site.admin.role-users.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('superadmin')) {
            return abort(401);
        }
        $datas = PemissionRoles::all();
            
        return view('site.admin.role-users.create', compact('datas'));
    }

     public function store(Request $request)
    {
        if (! Gate::allows('superadmin')) {
            return abort(401);
        }
        $this->validate($request, [
            'role_id' => 'required',
        ]);

       $exist = DB::table('permission_role')
                ->where('permission_id', $request->input('permission_id'))
                ->where('role_id', $request->input('role_id'))
                ->get();


        if(count($exist) > 0) {
             Toast::success('Data is Available', 'info');
        } else {
             $data = PemissionRoles::create($request->all());
        }

        Toast::success('Data Successfull', 'info');
        return redirect()->route('role-users.create');
    }


 public function edit($id)
    {
      if (! Gate::allows('superadmin')) {
            return abort(401);
        }
       
        $data = Users::findOrFail($id);
        $datas = DB::table('roles')->select('roles.id', 'roles.name', DB::raw('count(roles.id) as rol'))
              ->join('role_user', 'role_user.role_id', '=', 'roles.id')
              ->groupBy(['roles.id', 'roles.name'])
              ->where('role_user.user_id', $id)
              ->get();
   
        return view('site.admin.role-users.edit', compact(['data', 'datas']));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('superadmin')) {
            return abort(401);
        }

        $exist = DB::table('role_user')
                ->where('user_id', $id)
                ->where('role_id', $request->input('role_id'))
                ->get();

        if(count($exist) > 0) {

             Toast::success('Data is Available', 'info');
        } else {
              $data=array('user_id' => $id, 'role_id' => $request->input('role_id'));

              DB::table('role_user')->insert($data);
        }

        Toast::success('Data Successfull', 'info');
        return redirect()->route('role-users.edit', $id);
    }


    public function destroy($id)
    {
        if (! Gate::allows('superadmin')) {
            return abort(401);
        }     

        $data = RoleUsers::where('id', '=', $id)->delete();
        Toast::success('Delete Successfull', 'info');
        return redirect()->route('role-users.index');
    }

     public function getRoles()
    {
        $data = DB::table('users')->select('users.id', 'role_user.role_id', 'roles.name')
              ->join('role_user', 'users.id', '=', 'role_user.user_id')
              ->join('roles', 'roles.id', '=', 'role_user.role_id')
              ->get();

        return $data;
    }

      public function findRole()
    {
        $data = DB::table('roles')->select('roles.id', 'roles.name', DB::raw('count(roles.id) as rolesf'))
              ->join('role_user', 'role_user.role_id', '=', 'roles.id')
              ->groupBy(['roles.id', 'roles.name'])
              ->get();
           
       return json_encode($data);           
    }

     public function finduser()
    {
        $data = DB::table('users')->get();
                          
        return json_encode($data);
    }

      public function findPermission()
    {
        $data = DB::table('permissions')->get();
                          
        return json_encode($data);
    }


}
