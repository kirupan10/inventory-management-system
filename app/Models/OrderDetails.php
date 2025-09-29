<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetails extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'order_id',
        'product_id',
        'serial_number',
        'warranty_years',
        'quantity',
        'unitcost',
        'total',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'warranty_years' => 'integer',
    ];

    // Accessors to convert integer cents back to decimal currency
    public function getUnitcostAttribute($value)
    {
        return $value / 100;
    }

    public function getTotalAttribute($value)
    {
        return $value / 100;
    }

    protected $with = ['product'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
