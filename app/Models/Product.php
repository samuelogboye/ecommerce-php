<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    protected $fillable = [
        'name', 'description', 'qty', 'price', 'category_id', 'subcategory_id', 'featured_image', 'rank', 'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function views()
    {
        return $this->hasMany(View::class);
    }
}
