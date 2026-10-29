<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(
        protected readonly ?int $categoryId = null,
        protected readonly ?bool $enabled = null,
    ) {
    }

    public function collection(): Collection
    {
        $query = Product::query()->withTrashed();

        if ($this->categoryId !== null) {
            $query->where('category_id', $this->categoryId);
        }

        if ($this->enabled !== null) {
            $query->where('enabled', $this->enabled);
        }

        return $query->get();
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
            $row->category?->name ?? '',
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
