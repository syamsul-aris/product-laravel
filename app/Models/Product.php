<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'Product',
    properties: [
        new Property(property: 'id', type: 'integer', example: 1),
        new Property(property: 'name', type: 'string', example: 'Wireless Mouse'),
        new Property(property: 'category_id', type: 'integer', example: 1),
        new Property(property: 'category', ref: '#/components/schemas/Category', nullable: true),
        new Property(property: 'description', type: 'string', nullable: true),
        new Property(property: 'price', type: 'number', format: 'float', example: 29.99),
        new Property(property: 'stock', type: 'integer', example: 100),
        new Property(property: 'enabled', type: 'boolean', example: true),
        new Property(property: 'created_at', type: 'string', format: 'date-time'),
        new Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'price',
        'stock',
        'enabled',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'enabled' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeDisabled($query)
    {
        return $query->where('enabled', false);
    }
}
