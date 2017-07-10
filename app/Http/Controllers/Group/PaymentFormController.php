<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use JavaScript;
use redirect;
use DB;
use Storage;
use Log;
use Toast;
use Validator;
use App\Models\PaymentForm;
use App\Models\PaymentFormDetail;
use App\Models\Plant;
use App\Models\Department;
use App\Models\Subdepartment;
use App\Models\Currency;
use App\Models\Bank;
use Auth;
use Mail;
use File;
use Carbon\Carbon;
// use PDF;


class PaymentFormController extends Controller
{

    public function index()
    {
     if (! Gate::allows('payment-form.create')) {
            return abort(401);
        }
      $waiting = DB::table('prf_form')->select(DB::raw('count(id_prf) as id'))
                        ->wherein('is_processed', ['N', 'P'])
                        ->where('Flag', '1')
                        ->get();

      $datas = PaymentForm::UserLogin()->get();

        return view('site.group.payment-form.index', compact(['datas', 'waiting']));
    }

     public function reload()
    {
    
      $data = DB::table('prf_form')->get();

     return  json_encode($data);
    }

 

     public function admin()
    {
     if (! Gate::allows('payment-form.admin')) {
            return abort(401);
        }
        $datas = PaymentForm::admin()->get();

        return view('site.group.payment-form.admin', compact('datas'));
    }

     public function bulk()
    {
     if (! Gate::allows('payment-form.admin')) {
            return abort(401);
        }
        $datas = PaymentForm::admin()->get();

        return view('site.group.payment-form.bulk', compact('datas'));
    }

       public function report()
    {
      if (! Gate::allows('payment-form.admin')) {
            return abort(401);
        }
       
        $waiting = DB::table('prf_form')->select(DB::raw('count(id_prf) as id'))
                        ->wherein('is_processed', ['N', 'P'])
                        ->where('Flag', '1')
                        ->get();
                        
        return view('site.group.payment-form.report', compact(['waiting']));
    }

        public function FlowReport(Request $request)
    {
      if (! Gate::allows('payment-form.admin')) {
            return abort(401);
        }

      $from = Carbon::createFromFormat('d/m/Y',$request->from)->format('Y-m-d');
      $to = Carbon::createFromFormat('d/m/Y',$request->to)->format('Y-m-d');
       
      $data = DB::table('prf_form')->where('Flag', '1')->whereBetween('due_payment', [$from, $to])->get();

               
      if ($data == true ) {
             return $data ;
             return ['success' => true]; 
         } else{
                return ['success' => false]; 
      }

    }

    public function create()
    {
        if (! Gate::allows('payment-form.create')) {
            return abort(401);
        }
        $relations = [
            'plant0' => Plant::get()->pluck('name', 'id')->prepend('Please select', ''),
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
            'curr0' => Currency::get()->pluck('symbol', 'id')->prepend('Please select', ''),

        ];
        return view('site.group.payment-form.create', $relations);
    }

     public function store(Request $request)
    {

        if (! Gate::allows('payment-form.create')) {
            return abort(401);
        }
    
        $this->validate($request, [
            'payment_to' => 'required|max:50',
            'dept_id' => 'required|max:10',
            'sub_dept' => 'required',
            'plant' => 'required|max:10',
            'due_payment' => 'required|date',
            'due_settlement' => 'nullable|date|after:due_payment',
            'claim' => 'required|max:50',
            'currency_id' => 'required',
            'payment_method' => 'required|max:1',
        ]);

        if ($request->get('payment_method') == '2') {
             $this->validate($request, [
                    'bank_id' => 'required',
                    'bank_office' => 'required',
                    'rekening_no' => 'required',
                    'rekening_name' => 'required',
                ]);
        }

        if ($request->get('claim') == 'Advance') {
             $this->validate($request, [
                    'due_settlement' => 'required',
                ]);
        }

        DB::beginTransaction();

        try {

            $noId = $this->generateKode_Urut(); 
            $due_payment = date('Y-m-d', strtotime($request->input('due_payment')));
            if ($request->input('due_settlement') == null || $request->input('due_settlement') == '1970-01-01') {
                  $due_settlement = null;
              } else {
                 $due_settlement = date('Y-m-d', strtotime($request->input('due_settlement')));
              }
          
            $payment = implode(" ", $request->input('payment')); 

            $request->request->add(['id_prf' => $noId, 'no_prf' => $noId]);
            $request->request->add(['due_payment' => $due_payment]);
            $request->request->add(['due_settlement' => $due_settlement]);
            $request->request->add(['is_processed' => 'N']);
            $request->request->add(['Flag' => '1']);
            $amont = array_sum($request->input('amount'));

            if ($amont >= 500001 && $request->input('currency_id') == 1) {
                 $request->request->add(['payment' => 'B']);
            } else if ($amont <= 500001 && $request->input('currency_id') == 1) {
                $request->request->add(['payment' => 'C']);
            } else if ($request->input('currency_id') != 1) {
                $request->request->add(['payment' => 'B']);
            } else {
                $request->request->add(['payment' => 'C']);
            }
           
            $request->request->add(['total_amount' => $amont ]);
            $data = PaymentForm::create($request->all());

            $total = count($request->input('amount'));

            for ($i = 0; $i < $total; $i++) {
                    if (isset($request->input('amount')[$i])) {
                        $amount = $request->input('amount')[$i];
                        $descriptions = $request->input('descriptions')[$i];
                         $detail = new PaymentFormDetail(array(
                              'id' => $this->generateKode(),
                              'id_prf'  =>  $noId,
                              'descriptions' =>  $descriptions,
                              'amount'  => $amount,
                            ));
                         $detail->save();
                    }
            }

            $datas = ['payment_to' => $request->input('payment_to'), 'id_prf' => $noId ];

            DB::commit();

            if ($request == true) {
                    
                 if ($amont >= 500001 && $request->input('currency_id') == 1) {

                         Mail::send('site.group.payment-form.mail', $datas, function($message) {
   
                          $address = 'm.soleh@stanli.co.id'; // $'priyo.santoso@stanli.co.id';
                          $cc1 = 'no-reply@stanli.co.id'; //'erna.s@stanli.co.id';
                          $name = 'portalBiz Email - <no-reply@stanli.co.id>';
                          $message->from('no-reply@stanli.co.id', $name = null);
                          $message->to($address);
                          $message->sender('no-reply@stanli.co.id', $name);
                          $message->cc($cc1);
                          $message->subject('portalBiz Email - Payment Request Form');
                      });
                }else if ($request->input('currency_id') != 1) {

                    Mail::send('site.group.payment-form.mail', $datas, function($message) {
   
                          $address = 'm.soleh@stanli.co.id'; // $'priyo.santoso@stanli.co.id';
                          $cc1 = 'no-reply@stanli.co.id'; //'erna.s@stanli.co.id';
                          $name = 'portalBiz Email - <no-reply@stanli.co.id>';
                          $message->from('no-reply@stanli.co.id', $name = null);
                          $message->to($address);
                          $message->sender('no-reply@stanli.co.id', $name);
                          $message->cc($cc1);
                          $message->subject('portalBiz Email - Payment Request Form');
                      });
                }else {

                     Mail::send('site.group.payment-form.mail', $datas, function($message) {
   
                          $address = 'm.soleh@stanli.co.id'; // $'priyo.santoso@stanli.co.id';
                          $name = 'portalBiz Email - <no-reply@stanli.co.id>';
                          $message->from('no-reply@stanli.co.id', $name = null);
                          $message->to($address);
                          $message->sender('no-reply@stanli.co.id', $name);
                          $message->subject('portalBiz Email - Payment Request Form');
                      });
                }

            }
            Toast::success('Data Successfull', 'info');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('payment-form.index');
    }

    
     public function edit($id_prf)
    {
       if (! Gate::allows('payment-form.admin')) {
            return abort(401);
        }
        $relations = [
            'plant0' => Plant::get()->pluck('name', 'id')->prepend('Please select', ''),
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
            'subdept0' => SubDepartment::get()->pluck('name', 'id')->prepend('Please select', ''),
            'curr0' => Currency::get()->pluck('symbol', 'id')->prepend('Please select', ''),

        ];

        $data = PaymentForm::findOrFail($id_prf);
        $detail = DB::table('prf_form_detail')->where('id_prf', $id_prf)->get();

        $data['payment'] = explode(" ", $data['payment']);

        return view('site.group.payment-form.edit', compact(['data', 'detail']) + $relations);
    }

    public function update(Request $request, $id_prf)
    {
         if (! Gate::allows('payment-form.admin')) {
            return abort(401);
        }
       
        $this->validate($request, [
            'is_processed' => 'required',
           
        ]);

        $data = PaymentForm::findOrFail($id_prf);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('payment-form.admin');
    }

     public function vpdf($id_prf)
        {
           if (! Gate::allows('payment-form.create')) {
                return abort(401);
            }
            $relations = [
            'plant0' => Plant::get()->pluck('name', 'id')->prepend('Please select', ''),
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
            'subdept0' => SubDepartment::get()->pluck('name', 'id')->prepend('Please select', ''),
            'curr0' => Currency::get()->pluck('symbol', 'id')->prepend('Please select', ''),

        ];

        $data = PaymentForm::findOrFail($id_prf);
        $detail = DB::table('prf_form_detail')->where('id_prf', $id_prf)->get();

        $data['payment'] = explode(" ", $data['payment']);

        $detail = DB::table('prf_form_detail')->where('id_prf', $id_prf)->get();

        $html2pdf = base_path() . '\vendor\mpdf\mpdf\mpdf.php';
        File::requireOnce($html2pdf);
        $html2pdf = new \mPDF('utf-8','a4', 0, 'times', 
          15, //margin left
          15, // margin right
          10, // margin top
          5, //margin bottom 
          '', 14, 'P' );

        $content = view('site.group.payment-form.html2pdf', compact(['data', 'detail']) + $relations);

        $html2pdf->WriteHTML($content);
        $html2pdf->Output();

    }

    public function view($id_prf)
    {
       if (! Gate::allows('payment-form.admin')) {
            return abort(401);
        }
        $relations = [
            'plant0' => Plant::get()->pluck('name', 'id')->prepend('Please select', ''),
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
            'subdept0' => SubDepartment::get()->pluck('name', 'id')->prepend('Please select', ''),
            'curr0' => Currency::get()->pluck('symbol', 'id')->prepend('Please select', ''),

        ];

        $data = PaymentForm::findOrFail($id_prf);
        $detail = DB::table('prf_form_detail')->where('id_prf', $id_prf)->get();

        $data['payment'] = explode(" ", $data['payment']);

        return view('site.group.payment-form.view', compact(['data', 'detail']) + $relations);
    }

     public function cancel($id_prf)
    {
       if (! Gate::allows('payment-form.create')) {
            return abort(401);
        }
        $relations = [
            'plant0' => Plant::get()->pluck('name', 'id')->prepend('Please select', ''),
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
            'subdept0' => SubDepartment::get()->pluck('name', 'id')->prepend('Please select', ''),
            'curr0' => Currency::get()->pluck('symbol', 'id')->prepend('Please select', ''),

        ];

        $data = PaymentForm::findOrFail($id_prf);
        $detail = DB::table('prf_form_detail')->where('id_prf', $id_prf)->get();

        $data['payment'] = explode(" ", $data['payment']);

        return view('site.group.payment-form.cancel', compact(['data', 'detail']) + $relations);
    }

    public function gocancel(Request $request, $id_prf)
    {
         if (! Gate::allows('payment-form.create')) {
            return abort(401);
        }
        $request->request->add(['is_processed' => 'C']);
        $request->request->add(['Flag' => '0']);

        $data = PaymentForm::findOrFail($id_prf);
        $data->update($request->all());
        Toast::success('Cancel Form Successfull', 'info');
        return redirect()->route('payment-form.index');
    }

    
     public function gridsave(Request $request)
    {
        if (! Gate::allows('payment-form.admin')) {
            return abort(401);
        }
        $grid = json_decode($request->grids);
        foreach ($grid as $key => $value) {
           $id_prf = $value->no_prf;
           $setflag = $value->is_processed;
           if ($setflag == 'Rejected') {
                $setflag = 'R';
           } else  if ($setflag == 'Process') {
                $setflag = 'P';
           } else {
                 $setflag = 'Y';
           }
          $data = PaymentForm::where('id_prf', '=', $id_prf)->update(array('is_processed' => $setflag));
        }

        
       if ($data == true ) {
              return ['success' => true]; 
         } else{
                return ['success' => false]; 
        }

        return redirect()->route('payment-form.admin');
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

    public function getprf()
    {
        $data = DB::table('prf_form')
                ->wherein('is_processed', ['N', 'P'])
                ->get();
                          
        return json_encode($data);
    }


     public function findBank()
    {

        $dept = DB::table('bank')->get();
                          
        return json_encode($dept);
    }

     public function generateKode_Urut() {
        $_d = date("ymd");

        $last_id = DB::table('prf_form')
                ->select(DB::raw('max(id_prf) as id_prf'))  
                ->where('id_prf', 'LIKE', '%' . $_d.'%')
                ->orderBy('id_prf', 'DESC')
                ->get();
        
    
        $noId = $last_id[0]->id_prf;
        $new_code = "001";
 
        if ($noId == null || $noId == '') {     
            $no = $_d . '' .  $new_code;
        } else {
            $sort_num = substr($noId, 6);
            $sort_num++;
            $new_code = sprintf("%03s", $sort_num);
            $no = $_d . '' .  $new_code;
        }   

        return $no;
    }
    public function generateKode() {
        $_d = date("ymdH");

        $last_id = DB::table('prf_form_detail')
                ->select(DB::raw('max(id) as id'))  
                ->where('id', 'LIKE', '%' . $_d.'%')
                ->orderBy('id', 'DESC')
                ->get();
        
    
        $noId = $last_id[0]->id;
        $new_code = "01";
 
        if ($noId == null || $noId == '') {     
            $no = $_d . '' .  $new_code;
        } else {
            $sort_num = substr($noId, 8);
            $sort_num++;
            $new_code = sprintf("%02s", $sort_num);
            $no = $_d . '' .  $new_code;
        }   

        return $no;
    }

}
