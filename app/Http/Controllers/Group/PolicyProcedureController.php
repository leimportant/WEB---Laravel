<?php
use App\PolicyProcedure;
use DB;
use Log;
namespace App\Http\Controllers\Group;

use Illuminate\Http\Request;

class PolicyProcedureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

   

    public static function getMenu() {

        $data2 = array();
       
        $sql = "select id, parent, docID, url, label, stat, sort from policyandprocedureMenu where stat=1 AND parent IS NULL";
        $menu=  DB::select($sql);

        foreach ($menu as $models) {
            $row = array();
            $row['label'] = $models->label;
            $row['url'] = array($models->url);
            if (count(self::getMenu2(' =' . $models->id)) > 0) {
                $row['items'] = self::getMenu2(' =' . $models->id);
            }
            $data2[] = $row;
        }

        return $data2;
    }

    public static function getMenu2() {
        $data2 = array();
        
        $sql = "select id, parent, docID, url, label, stat, sort from policyandprocedureMenu where stat=1 AND parent  IS NULL";
        $menu=  DB::select($sql);

        foreach ($menu as $models) {
            $row = array();
            $row['label'] = $models->label;
            $row['url'] = array($models->url);
            if (count(self::getMenu2(' =' . $models->id)) > 0)
                $row['items'] = self::getMenu2(' =' . $models->id);
            $data2[] = $row;
        }

        return $data2;
    }

}
