<?php

namespace App\Http\Controllers\Admin;

use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;

class RolesController extends Controller
{
	public function index()
    {
     if (! Gate::allows('superadmin')) {
            return abort(401);
        }
        $datas = Roles::all();

        return view('site.admin.roles.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('superadmin')) {
            return abort(401);
        }

        return view('site.admin.roles.create');
    }

     public function store(Request $request)
    {
        if (! Gate::allows('superadmin')) {
            return abort(401);
        }
        $this->validate($request, [
            'name' => 'required|max:10',
            'display_name' => 'required|max:150',
        ]);

        $data = Roles::create($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('roles.index');
    }


 public function edit($id)
    {
      if (! Gate::allows('superadmin')) {
            return abort(401);
        }
       
        $data = Roles::findOrFail($id);

        return view('site.admin.roles.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('superadmin')) {
            return abort(401);
        }
        $data = Roles::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('roles.index');
    }


    public function destroy($id)
    {
        if (! Gate::allows('superadmin')) {
            return abort(401);
        }     
        $data = Roles::where('id', '=', $id)->delete();
        Toast::success('Delete Successfull', 'info');
        return redirect()->route('roles.index');
    }


}
