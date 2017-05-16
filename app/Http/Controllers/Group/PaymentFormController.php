<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\Eloquent;
use JavaScript;
use redirect;
use DB;
use Storage;
use Log;
use Toast;
use App\Models\PaymentForm;
use App\Models\Plant;
use App\Models\Department;
use App\Models\Subdepartment;
use App\Models\Currency;
use App\Models\Bank;
use Auth;


class PaymentFormController extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = PaymentForm::all();

        return view('site.group.payment-form.index', compact('datas'));
    }

     public function show()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = PaymentForm::all();

        return view('site.group.payment-form.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
  // Log::info('test');
        $relations = [
            'plant0' => Plant::get()->pluck('name', 'id')->prepend('Please select', ''),
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
            'curr0' => Currency::get()->pluck('symbol', 'id')->prepend('Please select', ''),

        ];
        // Log::info($relations);
        return view('site.group.payment-form.create', $relations);
    }

     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'id_prf' => 'required|max:11', 
            'payment_to' => 'required|max:50',
            'id_dept' => 'required|max:10',
            'sub_dept' => 'required',
            'no_prf' => 'required|max:11',
            'plant' => 'required|max:10',
            'due_payment' => 'required|date',
            'due_settlement' => 'date|after:due_payment',
            'claim' => 'required|max:50',
            'payment' => 'required|max:3',
            'currency_id' => 'required',
            'payment_method' => 'required|max:1',
            'descriptions' => 'required|max:250',
            'amount' => 'required',
        ]);

        $data = PaymentForm::create($request->all());
        Toast::success('Data Successfull', 'info');

        return redirect()->route('PaymentForm.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $relations = [
            'plant0' => Plant::get()->pluck('name', 'id')->prepend('Please select', ''),
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
            'curr0' => Currency::get()->pluck('symbol', 'id')->prepend('Please select', ''),

        ];
        $data = PaymentForm::findOrFail($id);

        return view('site.group.payment-form.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
       
        $this->validate($request, [
            'id_prf' => 'required|max:11', 
            'payment_to' => 'required|max:50',
            'id_dept' => 'required|max:10',
            'sub_dept' => 'required',
            'no_prf' => 'required|max:11',
            'plant' => 'required|max:10',
            'due_payment' => 'required|date',
            'due_settlement' => 'date|after:due_payment',
            'claim' => 'required|max:50',
            'payment' => 'required|max:3',
            'currency_id' => 'required',
            'payment_method' => 'required|max:1',
            'descriptions' => 'required|max:250',
            'amount' => 'required',
            'is_processed' => 'required|max:1',
        ]);
        $data = PaymentForm::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('PaymentForm.index');
    }



    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = PaymentForm::findOrFail($id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('PaymentForm.index');
    }

    public function suborg(Request $request)
    {
       $dept_id = $request->input('dept_id');
    
       $data = DB::table('subOrganization')->where('id_organization', $dept_id)->get();

       return json_encode($data);           
    }

     public function findept()
    {

        $dept = DB::table('organization')->get();
                          
        return json_encode($dept);
    }

     public function findBank()
    {

        $dept = DB::table('bank')->get();
                          
        return json_encode($dept);
    }

}
