<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, Sluggable, SoftDeletes;

    protected $guarded = [];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_hot' => 'boolean', 
        'is_featured' => 'boolean', 
        'is_offer' => 'boolean', 
        'is_deal' => 'boolean', 
        'is_active' => 'boolean', 
        'tags' => 'array', 
        'sizes' => 'array', 
        'colors' => 'array', 
        'qty' => 'float', 
        'price' => 'float', 
        'sale_price' => 'float', 
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function scopeSearch($query, $term)
    {
        $term = "%$term%";

        $query->where(function ($query) use ($term) {
            $query->where('name', 'like', $term)
                ->orWhere('slug', 'like', $term);
        });
    }

    public function scopeActive($query, $active)
    {   if(is_null($active)) return $query;
        $query->where('is_active', '=', $active);
    }
    
    public function setTagsAttribute($value)
    {   
        $tags_array = explode(",", $value);
        $this->attributes['tags'] = json_encode($tags_array);
    }

    public function setSizesAttribute($value)
    {   
        $tags_array = explode(",", $value);
        $this->attributes['sizes'] = json_encode($tags_array);
    }

    public function setColorsAttribute($value)
    {   
        $tags_array = explode(",", $value);
        $this->attributes['colors'] = json_encode($tags_array);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

}
