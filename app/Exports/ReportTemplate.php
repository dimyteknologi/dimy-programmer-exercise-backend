<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class ReportTemplate extends StringValueBinder implements FromView, ShouldAutoSize, WithEvents, WithCustomValueBinder
{
    use Exportable;

    protected $header_column_color = 'cbe6f7';
    protected $border_style_thickness = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM;
    protected $border_style_color = '000000';

    protected $title_cells = 'A1:D1';
    protected $title_font_size = 25;
    protected $title_font_align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;

    protected $summary_start_column = 'A';
    protected $summary_start_row = 3;
    protected $summary_end_column = 'B';    
    protected $summary_end_row = 4;

    protected  $lists_headers = [];

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $event->sheet->getPageSetup()->setHorizontalCentered(true);
                $event->sheet->getPageSetup()->setVerticalCentered(false);
                $highestRow = $event->sheet->getHighestDataRow();

                // REPORT TITLE
                $event->sheet->mergeCells($this->title_cells);
                $event->sheet->styleCells(
                    $this->title_cells,
                    [
                        'font' =>   [
                            'size' => $this->title_font_size,
                            'bold' => true,
                        ],
                        'alignment' => [
                            'horizontal' => $this->title_font_align,
                        ],
                    ]
                );

                // REPORT SUMMARY
                // SUMMARY HEADER COLUMN
                $event->sheet->styleCells(
                    $this->summary_start_column . $this->summary_start_row . ':' . $this->summary_start_column . $this->summary_end_row,
                    [
                        'fill' =>   [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => $this->header_column_color],
                        ],

                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        ],

                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => $this->border_style_thickness,
                                'color' => ['rgb' => $this->border_style_color],
                            ]
                        ],
                    ]
                );

                $summary_body_start_column = $this->summary_start_column;
                $summary_body_start_column++;

                $event->sheet->styleCells(
                    $summary_body_start_column . $this->summary_start_row . ':' . $this->summary_end_column . $this->summary_end_row,
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => $this->border_style_thickness,
                                'color' => ['rgb' => $this->border_style_color],
                            ]
                        ],
                        'alignment' => [
                            'horizontal' =>\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                        ],
                    ]
                );


                // LISTS
                // SET 1 ROW SPACE FROM SUMMARY ROWS
                $lists_header_start_row = $this->summary_end_row + 2;
                $lists_body_start_row = $lists_header_start_row + 1;
                $lists_body_end_row = $highestRow;

                if (count($this->lists_headers) > 0) {
                    foreach ($this->lists_headers as $header) {
                        // LIST HEADER
                        $event->sheet->styleCells(
                            $header['column'] . $lists_header_start_row,
                            [
                                'fill' =>   [
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'color' => ['rgb' => $this->header_column_color],
                                ],
    
                                'alignment' => [
                                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                ],

                                'borders' => [
                                    'allBorders' => [
                                        'borderStyle' => $this->border_style_thickness,
                                        'color' => ['rgb' => $this->border_style_color],
                                    ]
                                ],
                            ]
                        );
    
                        // LISTS BODY
                        $column_align = isset($header['align']) ? $header['align'] : \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                        $column_format = isset($header['format']) ? $header['format'] : \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL;
    
                        $event->sheet->styleCells(
                            $header['column'] . $lists_body_start_row . ':' . $header['column'] . $lists_body_end_row,
                            [
                                'borders' => [
                                    'allBorders' => [
                                        'borderStyle' => $this->border_style_thickness,
                                        'color' => ['rgb' => $this->border_style_color],
                                    ]
                                ],
    
                                'alignment' => [
                                    'horizontal' => $column_align,
                                ],
    
                                'NumberFormat' => [
                                    'formatCode' => $column_format,
                                ]
                            ]
                        );
                    }
                }
            }
        ];
    }

    public function view(): View {}
}
