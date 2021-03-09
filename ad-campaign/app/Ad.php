<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $table = 'campaigns';

    protected $fillable = ['partner_id','ad_content', 'duration', 'created_date'];
    
    //
}
