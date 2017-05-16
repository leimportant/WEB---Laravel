<?php

namespace App\Http\Controllers\Group;

use App\Models\AssetMasterForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;
use App\Models\Plant;
use App\Models\Department;
use App\Models\Subdepartment;


class AssetMasterFormController extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = AssetMasterForm::all();

        return view('site.group.asset-master-form.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

          $relations = [
            'plant0' => Plant::get()->pluck('name', 'id')->prepend('Please select', ''),
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),

        ];

        return view('site.group.asset-master-form.create', $relations);
    }

     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'symbol' => 'required|max:5',
        ]);

        $data = AssetMasterForm::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('AssetMasterForm.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }
       
        $data = AssetMasterForm::findOrFail($id);

        return view('site.group.asset-master-form.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = AssetMasterForm::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('AssetMasterForm.index');
    }



    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = AssetMasterForm::findOrFail($id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('AssetMasterForm.index');
    }



}
