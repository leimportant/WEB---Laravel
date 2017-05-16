<?php

namespace App\Http\Controllers\Master;

use App\Models\Costcenter as cost_center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;
use App\Models\Department;


class CostcenterController extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = cost_center::all();

        return view('site.master.cost-center.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        $relations = [
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
        ];

        return view('site.master.cost-center.create', $relations);
    
    }


     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
         $this->validate($request, [
            'cost_center_id' => 'required|max:10',
            'cost_center_name' => 'required|max:50',
        ]);
        $flag = 'Y'; 
        $request->request->add(['flag' => $flag]);
        $data = cost_center::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('cost-center.index');
    }

     public function edit($cost_center_id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }

        $relations = [
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
        ];
     
        $data = cost_center::find($cost_center_id);

        return view('site.master.cost-center.edit', compact('data') + $relations);
    }

    public function update(Request $request, $cost_center_id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
       $this->validate($request, [
            'cost_center_id' => 'required|max:10',
            'cost_center_name' => 'required|max:50',
        ]);

        $data = cost_center::find($cost_center_id);
         $flag = 'Y'; 
        $request->request->add(['flag' => $flag]);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('cost-center.index');
    }



    public function destroy($cost_center_id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
       $data = cost_center::where('cost_center_id', $cost_center_id)->delete();
       Toast::success('Delete Successfull', 'info');

       return redirect()->route('cost-center.index');
    }



}
