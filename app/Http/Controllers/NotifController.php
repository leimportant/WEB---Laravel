<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Facade;
use DB;
use Illuminate\Http\Request;
use Auth;
use Log;

class NotifController extends Facade 
{
     protected static function getFacadeAccessor() {
       return 'getAssetAdmin, getAssetManager, getAssetDirector, getAssetFinanceDirector, getPayment, getGarmelia';
   }

   public static function getAssetUser() {

       $data = DB::table('asset_master_form')->select(DB::raw('count(asset_id) as asset_id'))
                        ->wherein('flag', ['100', '101'])
                        ->where('created_by', Auth::user()->id)
                        ->get();
            
       return $data;
   }

    public static function getPaymentUser() {

       $data = DB::table('prf_form')->select(DB::raw('count(id_prf) as id_prf'))
                        ->wherein('is_processed', ['N', 'P'])
                        ->where('created_by', Auth::user()->id)
                        ->get();
            
       return $data;
   }

   public static function getMastermaterialUser() {

       $data = DB::table('material_master_form')->select(DB::raw('count(id) as id'))
                        ->wherein('flag', ['1'])
                        ->where('created_by', Auth::user()->id)
                        ->get();
            
       return $data;
   }

   public static function getAssetAdmin() {

       $data = DB::table('asset_master_form')->select(DB::raw('count(asset_id) as asset_id'))
                        ->where('flag', ['400'])
                        ->get();
       return $data;
   }

    public static function getAssetManager() {

       $data = DB::table('asset_master_form')->select(DB::raw('count(asset_id) as asset_id'))
                        ->where('flag', ['101'])
                        ->where('manager_approved_by', Auth::user()->id)
                        ->get();
       return $data;
   }

   public static function getAssetDirector() {

       $data = DB::table('asset_master_form')->select(DB::raw('count(asset_id) as asset_id'))
                        ->where('flag', ['250'])
                        ->get();
       return $data;
   }

   public static function getAssetFinanceDirector() {

       $data = DB::table('asset_master_form')->select(DB::raw('count(asset_id) as asset_id'))
                        ->wherein('flag', ['200', '300'])
                        ->get();
       return $data;
   }

   public static function getPayment() {
      $data = DB::table('prf_form')->select(DB::raw('count(id_prf) as id'))
                        ->wherein('is_processed', ['N', 'P'])
                        ->where('Flag', '1')
                        ->get();
        return $data;
   }

   public static function getMaterial() {
         $data = DB::table('material_master_form')->select(DB::raw('count(id) as id'))
                        ->where('flag', ['1'])
                        ->get();
        return $data;
   }

   public static function getGarmelia() {
        $data = DB::table('cooperation_agreement')->select(DB::raw('count(doc_id) as id'))
                        ->wherein('company_type', ['G-AGEN', 'G-DIST'])
                        ->wherein('flag', ['0', '3'])
                        ->get();
        return $data;
   }

   public static function getPadimas() {
        $data = DB::table('cooperation_agreement')->select(DB::raw('count(doc_id) as id'))
                        ->wherein('company_type', ['P-AGEN', 'P-DIST'])
                        ->wherein('flag', ['0', '200'])
                        ->get();
        return $data;
   }

   public static function getAdendumGarmelia() {
         $data = DB::table('adendum_agreement')->select(DB::raw('count(id) as id'))
                        ->wherein('adendum_type', ['G-DIST', 'G-AGEN'])
                        ->wherein('flag', ['0', '3'])
                        ->get();
        return $data;

   }
   public static function getAdendumPadimas() {
         $data = DB::table('adendum_agreement')->select(DB::raw('count(id) as id'))
                        ->wherein('adendum_type', ['P-DIST'])
                        ->wherein('flag', ['0', '3'])
                        ->get();
        return $data;

   }

   public static function getProfile() {
         $user = Auth::id();
         $record =   DB::select("select id, Sum(isnull(cast(rPhone + rGender + rPhoto + rLocation + rDept + rsubDept as float),0))  as total from 
                ((select id, CASE  
                    WHEN phone IS null THEN 0
                    ELSE 1 
                END as rPhone,
                CASE  
                    WHEN gender IS null THEN 0
                    ELSE 1
                END as rGender,
                CASE  
                    WHEN photo IS null THEN 0
                    ELSE 1
                END as rPhoto,
                CASE  
                    WHEN plant IS null THEN 0
                    ELSE 1
                END as rLocation,
                CASE  
                    WHEN dept_id IS null THEN 0 
                    ELSE 1
                END as rDept,
                CASE  
                    WHEN sub_dept IS null THEN 0
                    ELSE 1
                END as rsubDept
                from profile)) ds  where id =  " . $user  . " group by id ");

           if (empty($record[0]->total)) {
              $data = 10;
            } else {
              $data = round(($record[0]->total * 100) / 6);
            }

        return $data;

   }




}
