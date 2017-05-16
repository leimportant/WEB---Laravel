<?php

namespace App\Http\Controllers\Master;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;


class CurrencyController extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = Currency::all();

        return view('site.master.currency.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        return view('site.master.currency.create');
    }

     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'symbol' => 'required|max:5',
        ]);

        $data = Currency::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('currency.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }
       
        $data = Currency::findOrFail($id);

        return view('site.master.Currency.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = Currency::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('currency.index');
    }



    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = Currency::findOrFail($id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('currency.index');
    }



}
