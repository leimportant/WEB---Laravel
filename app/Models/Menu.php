<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Menu extends Model
{
    protected $table = 'menus';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = ['name', 'access'];

    public function submenu()
    {
        return $this->hasMany(SubMenu::class)->select(array('name', 'menu_id', 'url', 'access', 'remark', 'parent_id', 'flag', 'sorting'));
    }
	 
}
