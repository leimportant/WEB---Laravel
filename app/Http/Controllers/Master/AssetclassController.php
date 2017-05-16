<?php

namespace App\Http\Controllers\Master;

use App\Models\Assetclass as asset_class;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;
use App\Models\Assetmaster;


class AssetclassController extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = asset_class::all();

        return view('site.master.asset-class.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        $relations = [
            'asset0' => Assetmaster::get()->pluck('asset_id', 'asset_id')->prepend('Please select', ''),
        ];

        return view('site.master.asset-class.create', $relations);
    
    }


     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'asset_id' => 'required|max:20',
            'class_id' => 'required|max:10',
            'class_name' => 'required|max:50',
        ]);

        $data = asset_class::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('asset-class.index');
    }

     public function edit($class_id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }

       $relations = [
            'asset0' => Assetmaster::get()->pluck('asset_id', 'asset_id')->prepend('Please select', ''),
        ];
     
        $data = asset_class::find($class_id);

        return view('site.master.asset-class.edit', compact('data') + $relations);
    }

    public function update(Request $request, $class_id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
          $this->validate($request, [
            'asset_id' => 'required|max:20',
            'class_id' => 'required|max:10',
            'class_name' => 'required|max:50',
        ]);

        $data = asset_class::find($class_id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('asset-class.index');
    }



    public function destroy($class_id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
       $data = asset_class::where('class_id', $class_id)->delete();
       Toast::success('Delete Successfull', 'info');

       return redirect()->route('asset-class.index');
    }



}
