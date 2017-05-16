<?php

namespace App\Http\Controllers\Master;

use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;

class PlantController extends Controller
{
	public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = Plant::all();

        return view('site.master.plant.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        return view('site.master.plant.create');
    }

     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'id' => 'required|max:10',
            'name' => 'required|max:50',
        ]);

        $data = Plant::create($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('plant.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }
       
        $data = Plant::findOrFail($id);

        return view('site.master.plant.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = Plant::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('plant.index');
    }


    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }     
        $data = Plant::findOrFail($id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('plant.index');
    }


}
