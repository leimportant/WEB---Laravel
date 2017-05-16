<?php

namespace App\Http\Controllers\Master;

use App\Models\Productdisc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;
use App\Models\Area;

class ProductdiscController  extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = Productdisc::all();

        return view('site.master.product-disc.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $relations = [
            'area0' => Area::get()->pluck('area', 'area')->prepend('Please select', ''),
        ];

        return view('site.master.product-disc.create', $relations);
    }


     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'product' => 'required|max:50',
            'disc' => 'required|max:10',
            'area' => 'required|max:30',
        ]);

        $data = Productdisc::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('product-disc.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }
       
       $relations = [
            'area0' => Area::get()->pluck('area', 'area')->prepend('Please select', ''),
        ];

        $data = Productdisc::findOrFail($id);

        return view('site.master.product-disc.edit', compact('data') + $relations);
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = Productdisc::findOrFail($id);
        $this->validate($request, [
            'product' => 'required|max:50',
            'disc' => 'required|max:10',
            'area' => 'required|max:30',
        ]);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('product-disc.index');
    }



    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = Productdisc::findOrFail($id);
        $data->delete();

        Toast::success('Delete Successfull', 'info');

        return redirect()->route('product-disc.index');
    }



}
