<?php
namespace Modules\Core\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TranslationTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $excelHeaderStyle;
    protected $evenRowStyle;
    protected $oddRowStyle;

    public function __construct(array $data = [])
    {
        // Data passed for export
        $this->data = $data ?: [
            ['human_resource', 'lang_greeting', 'Hello Example', 'مرحبا  مثال'], // Default row
        ];

        // Header row style
        $this->excelHeaderStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'd3d3d3'],
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => ['rgb' => 'd5d5d5'],
                'endColor' => ['rgb' => 'f8f8f8'],
            ],
        ];

        // Even row style
        $this->evenRowStyle = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'f0f0f0'],
            ],
        ];

        // Odd row style
        $this->oddRowStyle = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ffffff'],
            ],
        ];
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Module',           // Module name column
            'Key',              // Translation key column
            'phrase_en', // English translation column
            'phrase_ar',  // Arabic translation column
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply header style to the first row
        $sheet->getStyle('A1:D1')->applyFromArray($this->excelHeaderStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Apply alternating row styles for even and odd rows
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $styleArray = ($row % 2 === 0) ? $this->evenRowStyle : $this->oddRowStyle;
            $sheet->getStyle("A{$row}:D{$row}")->applyFromArray($styleArray);
            $sheet->getRowDimension($row)->setRowHeight(20); // Set consistent row height
        }
    }
}
