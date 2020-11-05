<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'price',
        'weight',
        'image'
    ];

    public function user(){
        return $this->belongsTo(Product::class, 'id', 'user_id');
    }
}
