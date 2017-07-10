<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;

class PermissionsController extends Controller
{
	public function index()
    {
     if (! Gate::allows('superadmin')) {
            return abort(401);
        }
        $datas = Permissions::all();

        return view('site.admin.permissions.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('superadmin')) {
            return abort(401);
        }

        return view('site.admin.permissions.create');
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

        $data = Permissions::create($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('permissions.index');
    }

    public function edit($id)
    {
      if (! Gate::allows('superadmin')) {
            return abort(401);
        }
       
        $data = Permissions::findOrFail($id);

        return view('site.admin.permissions.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('superadmin')) {
            return abort(401);
        }
        $data = Permissions::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('permissions.index');
    }


    public function destroy($id)
    {
        if (! Gate::allows('superadmin')) {
            return abort(401);
        }     
        $data = Permissions::where('id', '=', $id)->delete();
        Toast::success('Delete Successfull', 'info');
        return redirect()->route('permissions.index');
    }


}
