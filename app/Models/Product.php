<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    protected $fillable = [
        'product_name',
        'description',
        'price',
        'image_path',
        'category',
        'is_featured',
        'stock',
        'stock_unlimited',
        'stock_updated_at',
    ];
    public $timestamps = true;

    // Append computed fields to JSON output
    protected $appends = ['image_url', 'has_variants', 'price_range'];

    protected $casts = [
        'is_featured' => 'boolean',
        'stock_unlimited' => 'boolean',
        'stock_updated_at' => 'datetime',
    ];

    public function stockAudits()
    {
        return $this->hasMany(ProductStockAudit::class, 'product_id', 'product_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'product_id')
            ->where('is_active', true)
            ->orderBy('name');
    }

    public function getEffectiveStockUnlimitedAttribute(): bool
    {
        if ($this->has_variants) {
            if (array_key_exists('variants_unlimited_count', $this->attributes)) {
                return (int) ($this->attributes['variants_unlimited_count'] ?? 0) > 0;
            }

            if ($this->relationLoaded('variants')) {
                return $this->variants->contains(fn ($v) => (bool) ($v->stock_unlimited ?? false));
            }

            return $this->variants()->where('stock_unlimited', true)->exists();
        }

        return (bool) ($this->stock_unlimited ?? false);
    }

    public function getEffectiveStockAttribute(): int
    {
        if ($this->has_variants) {
            if ($this->effective_stock_unlimited) {
                return 0;
            }

            if (array_key_exists('variants_stock_sum', $this->attributes)) {
                return (int) ($this->attributes['variants_stock_sum'] ?? 0);
            }

            if ($this->relationLoaded('variants')) {
                return (int) $this->variants->sum(fn ($v) => (int) ($v->stock ?? 0));
            }

            return (int) $this->variants()->sum('stock');
        }

        return (int) ($this->stock ?? 0);
    }

    public function getHasVariantsAttribute(): bool
    {
        if (array_key_exists('variants_count', $this->attributes)) {
            return (int) $this->attributes['variants_count'] > 0;
        }

        if ($this->relationLoaded('variants')) {
            return $this->variants->isNotEmpty();
        }

        return $this->variants()->exists();
    }

    public function getPriceRangeAttribute(): array
    {
        if (!$this->has_variants) {
            $p = (float) ($this->price ?? 0);
            return ['min' => $p, 'max' => $p];
        }

        $variants = $this->relationLoaded('variants') ? $this->variants : $this->variants()->get();
        if ($variants->isEmpty()) {
            $p = (float) ($this->price ?? 0);
            return ['min' => $p, 'max' => $p];
        }

        $prices = $variants->map(fn ($v) => (float) ($v->effective_price ?? $this->price ?? 0));
        return ['min' => (float) $prices->min(), 'max' => (float) $prices->max()];
    }

    public function getStockStateAttribute(): string
    {
        if ($this->effective_stock_unlimited) return 'unlimited';
        $qty = (int) ($this->effective_stock ?? 0);
        if ($qty <= 0) return 'out_of_stock';
        if ($qty <= 5) return 'low_stock';
        return 'in_stock';
    }

    public function getStockDisplayTextAttribute(): string
    {
        return match ($this->stock_state) {
            'unlimited' => 'In stock',
            'out_of_stock' => 'Out of stock',
            'low_stock' => 'Only ' . (int) ($this->effective_stock ?? 0) . ' left',
            default => 'In stock (' . (int) ($this->effective_stock ?? 0) . ')',
        };
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return !$this->effective_stock_unlimited && (int) ($this->effective_stock ?? 0) <= 0;
    }
    
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
                // Return a relative placeholder so the host origin is always correct
                return '/images/placeholder.png';
            }

            // Return a relative path (not absolute URL) so browsers request
            // the image from the same origin. This avoids incorrect hosts when
            // `APP_URL` is set to a developer machine.
            return '/' . ltrim($path, '/');
        }
}
