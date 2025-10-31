<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportStory implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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
            'Tên truyện',
            'Số chương',
            'Cập nhật lần cuối',
            'Đã hoàn thành',
            'Link',
            'Team',
            'Hình ảnh',
            'Trạng thái',
        ];
    }

    public function map($row): array {
        return [
            $row['id'],
            $row['name'],
            $row['total_chapter'],
            $row['last_update'],
            $row['is_full'],
            $row['link'],
            $row['team'],
            $row['thumbnail'],
            $row['status'],
        ];
    }
}
