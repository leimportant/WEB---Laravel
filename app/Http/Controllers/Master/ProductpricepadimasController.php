<?php

namespace App\Http\Controllers\Master;

use App\Models\Productpricepadimas;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;
use App\Models\Area;

class ProductpricepadimasController  extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = DB::table('product_price_padimas')->get();
        
        return view('site.master.product-price-padimas.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        $relations = [
            'area0' => Area::get()->pluck('area', 'area')->prepend('Please select', ''),
        ];

        return view('site.master.product-price-padimas.create', $relations);
    }


     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'name' => 'required|max:20',
            'area' => 'required|max:30',
        ]);

        $data = Productpricepadimas::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('product-price-padimas.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }
        
        $relations = [
            'area0' => Area::get()->pluck('area', 'area')->prepend('Please select', ''),
        ];

        $data = Productpricepadimas::findOrFail($id);

        return view('site.master.product-price-padimas.edit', compact('data') + $relations);
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = Productpricepadimas::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|max:20',
            'area' => 'required|max:30',
        ]);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('product-price-padimas.index');
    }



    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = Productpricepadimas::findOrFail($id);
        $data->delete();

        Toast::success('Delete Successfull', 'info');

        return redirect()->route('product-price-padimas.index');
    }



}
