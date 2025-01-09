<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusignessCategory extends Model
{
    protected $fillable = [ 'name'];
    use HasFactory;
}
