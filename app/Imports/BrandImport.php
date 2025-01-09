<?php

namespace App\Imports;

use App\Brand;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BrandImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Brand([
            'name' => $row[1],
            'slug' => Str::slug($row[1]),
            'description' => $row[2]
        ]);
    }
}
