<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'Category',
    properties: [
        new Property(property: 'id', type: 'integer', example: 1),
        new Property(property: 'name', type: 'string', example: 'Electronics'),
    ]
)]
class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
