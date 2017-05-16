<?php

namespace App\Http\Controllers\Master;

use App\Models\Assetlocation as asset_location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;
use App\Models\Plant;


class AssetlocationController extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = asset_location::all();

        return view('site.master.asset-location.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        $relations = [
            'plant0' => Plant::get()->pluck('name', 'id')->prepend('Please select', ''),
        ];

        return view('site.master.asset-location.create', $relations);
    
    }


     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'plant_id' => 'required|max:10',
            'loc_id' => 'required|max:10',
            'loc_name' => 'required|max:50',
        ]);

        $data = asset_location::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('asset-location.index');
    }

     public function edit($loc_id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }

        $relations = [
            'plant0' => Plant::get()->pluck('name', 'id')->prepend('Please select', ''),
        ];
     
        $data = asset_location::find($loc_id);

        return view('site.master.asset-location.edit', compact('data') + $relations);
    }

    public function update(Request $request, $loc_id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'plant_id' => 'required|max:10',
            'loc_id' => 'required|max:10',
            'loc_name' => 'required|max:50',
        ]);

        $data = asset_location::find($loc_id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('asset-location.index');
    }



    public function destroy($loc_id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
       $data = asset_location::where('loc_id', $loc_id)->delete();
       Toast::success('Delete Successfull', 'info');

       return redirect()->route('asset-location.index');
    }



}
