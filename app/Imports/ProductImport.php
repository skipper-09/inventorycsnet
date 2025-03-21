<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return Product::updateOrCreate(
            ['id' => $row['id']],
            [
                'unit_id' => $row['unit_id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'is_modem' => $row['is_modem'] ?? false,
            ]
        );
    }
}
