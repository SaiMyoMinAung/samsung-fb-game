<?php

namespace App\Exports;

use App\Models\GameUsedUser;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportUser implements FromCollection, WithHeadings
{

    public function headings(): array
    {
        return [
            'Name',
            'Email'
        ];
    } 

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return GameUsedUser::select('name','email')->cursor();
    }
}
