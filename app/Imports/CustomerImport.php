<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToModel, WithHeadingRow
{
     /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Customer([
            'transid' => bin2hex(random_bytes(4)),
            'custno' => 'CUS-' . bin2hex(random_bytes(4)),
            'lname'     => trim(stripslashes($row['barcode'])),
            'fname'    => $row['customer_name'],
            'phone'    => $row['phone'],
            'mname'    => $row['notes'],
            'pob'    => $row['size'],
            'occupation'    => $row['photo1'],
            'landmark'    => $row['photo2'],
            'createuser' => 'admin',
            'createdate' => date("Y-m-d H:i:s"),
        ]);
    }
}
