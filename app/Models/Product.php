<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = []; // อนุญาตให้บันทึกข้อมูลทุกคอลัมน์ได้ทันที
}