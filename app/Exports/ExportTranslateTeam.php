<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportTranslateTeam implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
	use Exportable;

    public function __construct(array $arr)
    {
        $this->arr = $arr;
    }

    public function collection()
    {
        return collect($this->arr);
    }

    public function headings(): array {
        return [
            '#ID',
            'Tên hiển thị',
            'Username',
            'Email',
            'Lượt xem',
            'Xu quy đổi',
        ];
    }

    public function map($row): array {
        return [
            $row['id'],
            $row['name'],
            $row['username'],
            $row['email'],
            $row['user_view'],
            $row['convert_coin'],
        ];
    }
}
