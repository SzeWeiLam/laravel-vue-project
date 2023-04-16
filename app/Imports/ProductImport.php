<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class ProductImport implements ToArray
{
    private $data;

    public function __construct()
    {
        $this->data = [];
    }
    public function array(array $rows)
    {
        foreach ($rows as $key => $row) {
            if($key > 0){
                $this->data[] = array('product_id' => $row[0], 'status' => $row[5]);
            }
        }
    }

    public function getArray(): array
    {
        return $this->data;
    }
}