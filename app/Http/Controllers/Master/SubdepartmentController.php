<?php

namespace App\Http\Controllers\Master;

use App\Models\Subdepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;
use App\Models\Department;


class SubdepartmentController extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = Subdepartment::all();

        return view('site.master.sub-department.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        $relations = [
            'dept' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
        ];

        return view('site.master.sub-department.create', $relations);
    
    }


     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'id_organization' => 'required|max:15',
            'name' => 'required|max:50',
        ]);

        $data = Subdepartment::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('subdepartment.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }

        $relations = [
            'dept' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
        ];
       
        $data = Subdepartment::findOrFail($id);

        return view('site.master.sub-department.edit', compact('data') + $relations);
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = Subdepartment::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('subdepartment.index');
    }



    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = Subdepartment::findOrFail($id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('subdepartment.index');
    }



}
