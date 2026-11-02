<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<Factory<Product>> */
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
    /** @param Builder<Product> $query
     * @return Builder<Product>
     */
    public function scopeWithStats(Builder $query): Builder
    {
        return $query->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withSum(['orderItems as sold' => fn (Builder $items) => $items->whereHas(
                'order', fn (Builder $order) => $order->where('status', '!=', 'cancelled')
            )], 'quantity');
    }

    /** เหมือน withStats แต่ใช้กับ product ที่โหลดมาแล้ว (เช่นหน้า show) */
    public function loadStats(): static
    {
        return $this->loadAvg('reviews', 'rating')
            ->loadCount('reviews')
            ->loadSum(['orderItems as sold' => fn ($items) => $items->whereHas(
                'order', fn ($order) => $order->where('status', '!=', 'cancelled')
            )], 'quantity');
    }

    /** @return HasMany<OrderItem, $this> */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return HasMany<Wishlist, $this> */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    /** @return HasMany<ProductImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}
