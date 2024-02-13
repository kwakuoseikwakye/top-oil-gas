<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CylindersImport implements ToModel, WithHeadingRow
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
            'fname'    => $row['name'],
            'createuser' => 'admin',
            'createdate' => date("Y-m-d H:i:s"),
        ]);
    }
}
