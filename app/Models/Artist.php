<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model {

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'followers', 'genres', 'href', 'id', 'images', 'name', 'popularity', 'type', 'uri',
  ];

}

