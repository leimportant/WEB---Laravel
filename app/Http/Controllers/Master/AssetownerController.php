<?php

namespace App\Http\Controllers\Master;

use App\Models\Assetowner as asset_owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;
use Carbon;


class AssetownerController extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = asset_owner::where('flag', 'Y')->get();

        return view('site.master.asset-owner.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        return view('site.master.asset-owner.create');
    }


     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'owner_id' => 'required|unique:m_asset_owner|max:15',
            'owner_name' => 'required|max:50',

        ]);
        $flag = 'Y'; 
        $request->request->add(['flag' => $flag]);
        $data = asset_owner::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('asset-owner.index');
    }

     public function edit($owner_id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }
       
        $data = asset_owner::findOrFail($owner_id);

        return view('site.master.asset-owner.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }

        $this->validate($request, [
            'owner_name' => 'required|max:50',
        ]);


        $data = asset_owner::findOrFail($id);
        $flag = 'Y'; 
        $request->request->add(['flag' => $flag]);
        $data->update($request->all());

        Toast::success('Data Successfull', 'info');
        return redirect()->route('asset-owner.index');
    }



    public function destroy($owner_id)
    {
        $date = Carbon\Carbon::now();
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = asset_owner::where('owner_id', '=', $owner_id)->update(['flag' => 'N', 'updated_at' => $date]);

        Toast::success('Delete Successfull', 'info');
        return redirect()->route('asset-owner.index');
    }



}
