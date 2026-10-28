<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(
        protected ?int $categoryId = null
    ) {
    }

    public function query()
    {
        $query = Product::query()
            ->selectRaw('products.*, categories.name as category_name')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->withTrashed();

        if ($this->categoryId !== null) {
            $query->where('products.category_id', $this->categoryId);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Category',
            'Description',
            'Price',
            'Stock',
            'Enabled',
            'Deleted At',
            'Created At',
            'Updated At',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->category_name,
            $row->description,
            $row->price,
            $row->stock,
            $row->enabled ? 'Yes' : 'No',
            $row->deleted_at,
            $row->created_at,
            $row->updated_at,
        ];
    }
}
