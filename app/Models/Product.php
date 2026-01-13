<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    protected $fillable = ['product_name', 'description', 'price', 'image_path'];
    public $timestamps = false;
        // Append computed URL to JSON output
        protected $appends = ['image_url'];
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }
    
        /**
         * Return a fully-qualified URL for the product image.
         * Normalizes paths coming from the database (Product_IMG/ -> images/),
         * supports absolute URLs, and falls back to a placeholder.
         */
        public function getImageUrlAttribute(): string
        {
            $path = $this->attributes['image_path'] ?? '';
        
            // If already an absolute URL, return as-is
            if (!empty($path) && preg_match('#^https?://#i', $path)) {
                return $path;
            }
        
            // Normalize Windows-style backslashes
            $path = str_replace('\\', '/', $path);
        
            // If path references the original Product_IMG folder, map to public/images
            $path = preg_replace('#(^/)?Product_IMG/#i', 'images/', $path);
            $path = preg_replace('#(^/)?Product_IMG$#i', 'images', $path);
        
            // Trim any leading slash so asset() builds correctly
            $path = ltrim($path, '/');
        
            if (empty($path)) {
                return asset('images/placeholder.png');
            }
        
            return asset($path);
        }
}
