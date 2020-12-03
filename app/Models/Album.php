<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model {

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id','album_type','artists','genres','href','images','label','name','release_date','tracks','type','uri',
  ];

}
