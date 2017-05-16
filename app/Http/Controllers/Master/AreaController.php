<?php

namespace App\Http\Controllers\Master;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;

class AreaController extends Controller
{
	public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = Area::all();

        return view('site.master.area.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        return view('site.master.area.create');
    }

     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'area' => 'required|max:30',
        ]);

        $data = Area::create($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('area.index');
    }



    public function destroy($area)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }     
        $data = Area::where('area', '=', $area)->delete();
        Toast::success('Delete Successfull', 'info');
        return redirect()->route('area.index');
    }


}
