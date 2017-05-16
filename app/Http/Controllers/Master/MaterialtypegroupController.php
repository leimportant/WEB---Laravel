<?php

namespace App\Http\Controllers\Master;

use App\Models\Materialtypegroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;


class MaterialtypegroupController extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = DB::table('material_type_group')->get();

        return view('site.master.materialtype-group.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        return view('site.master.materialtype-group.create');
    }

     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'id' => 'required|unique:material_type_group|max:10',
            'name' => 'required|max:50',
        ]);

        $data = Materialtypegroup::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('materialtypegroup.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }

        $data = Materialtypegroup::find($id);
             
        return view('site.master.materialtype-group.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = Materialtypegroup::find($id);
        $data->update($request->all());
        
        Toast::success('Data Successfull', 'info');

        return redirect()->route('materialtypegroup.index');
    }



    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = Materialtypegroup::findOrFail($id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('materialtypegroup.index');
    }



}
