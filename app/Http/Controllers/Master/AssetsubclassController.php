<?php

namespace App\Http\Controllers\Master;

use App\Models\Assetsubclass as asset_subclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;
use App\Models\Assetmaster;


Class AssetsubclassController extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = asset_subclass::all();

        return view('site.master.asset-subclass.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        $relations = [
            'asset0' => Assetmaster::get()->pluck('asset_id', 'asset_id')->prepend('Please select', ''),
        ];

        return view('site.master.asset-subclass.create', $relations);
    
    }


     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'asset_id' => 'required|max:20',
            'sub_name' => 'required|max:50',
        ]);

        $data = asset_subclass::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('asset-subclass.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }

       $relations = [
            'asset0' => Assetmaster::get()->pluck('asset_id', 'asset_id')->prepend('Please select', ''),
        ];
     
        $data = asset_subclass::findOrFail($id);

        return view('site.master.asset-subclass.edit', compact('data') + $relations);
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
          $this->validate($request, [
            'asset_id' => 'required|max:20',
            'sub_name' => 'required|max:50',
        ]);

         $data = asset_subclass::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('asset-subclass.index');
    }



    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
       $data = asset_subclass::findOrFail($id);
       $data->delete();
       Toast::success('Delete Successfull', 'info');

       return redirect()->route('asset-subclass.index');
    }



}
