<?php

namespace App\Http\Controllers\Group;

use App\Models\MaterialMasterForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;


class MaterialMasterFormController extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = MaterialMasterForm::all();

        return view('site.group.material-master-form.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        return view('site.group.material-master-form.create');
    }

     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'symbol' => 'required|max:5',
        ]);

        $data = MaterialMasterForm::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('MaterialMasterForm.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }
       
        $data = MaterialMasterForm::findOrFail($id);

        return view('site.group.material-master-form.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = MaterialMasterForm::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('MaterialMasterForm.index');
    }



    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = MaterialMasterForm::findOrFail($id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('MaterialMasterForm.index');
    }



}
