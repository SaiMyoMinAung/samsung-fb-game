<?php

namespace App\Exports;

use App\Models\GameUsedUser;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportUser implements FromCollection, WithHeadings, WithMapping
{

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Played Date'
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return GameUsedUser::select('name', 'email', 'created_at')->cursor();
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email ?? '',
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
