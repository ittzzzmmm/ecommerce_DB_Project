<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * คะแนนเฉลี่ย / จำนวนรีวิว / ยอดขาย "คำนวณสด" จากตาราง reviews และ order_items
     * (ไม่เก็บเป็นคอลัมน์ใน products ให้ตรงกับ ER / DB Design)
     * ได้ attribute: reviews_avg_rating, reviews_count, sold
     */
    public function scopeWithStats(Builder $query): Builder
    {
        return $query->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withSum('orderItems as sold', 'quantity');
    }

    /** เหมือน withStats แต่ใช้กับ product ที่โหลดมาแล้ว (เช่นหน้า show) */
    public function loadStats(): static
    {
        return $this->loadAvg('reviews', 'rating')
            ->loadCount('reviews')
            ->loadSum('orderItems as sold', 'quantity');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}