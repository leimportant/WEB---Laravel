<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';

    protected $primaryKey = 'id';

    protected $fillable = ['name'];

    public function submenu()
    {
        return $this->hasMany(SubMenu::class);
    }
	 
}
