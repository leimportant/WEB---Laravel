<?php

namespace App\Http\Controllers\Master;

use App\Models\Assetmaster as asset_master;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;


class AssetmasterController extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = asset_master::all();

        return view('site.master.asset-master.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        return view('site.master.asset-master.create');
    }


     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'asset_id' => 'required|unique:m_asset_master|max:20',
        ]);

        $data = asset_master::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('asset-master.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }
       
        $data = asset_master::findOrFail($id);

        return view('site.master.asset-master.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = asset_master::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('asset-master.index');
    }



    public function destroy($asset_id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
       $data = asset_master::where('asset_id', '=', $asset_id)->delete();

        Toast::success('Delete Successfull', 'info');

        return redirect()->route('asset-master.index');
    }



}
