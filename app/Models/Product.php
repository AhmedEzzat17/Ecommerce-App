<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'category_id', 'title', 'description', 'price', 'Budget_Range', 'note', 'date', 'images'];
    protected $casts    = ['images' => 'array'];

    public function category()  { return $this->belongsTo(Category::class); }
    public function user()      { return $this->belongsTo(User::class); }
    public function cartItems() { return $this->hasMany(CartItem::class); }
}
