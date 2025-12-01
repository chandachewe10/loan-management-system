<?php

namespace App\Filament\Exports;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EMIScheduleExporter implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $loan;
    protected $schedule;

    public function __construct(Loan $loan, array $schedule)
    {
        $this->loan = $loan;
        $this->schedule = $schedule;
    }

    public function array(): array
    {
        $data = [];
        
        foreach ($this->schedule as $installment) {
            $data[] = [
                $installment['installment_number'],
                $installment['payment_date'],
                number_format($installment['emi_amount'], 2),
                number_format($installment['principal_component'], 2),
                number_format($installment['interest_component'], 2),
                number_format($installment['outstanding_principal'], 2),
                $installment['is_paid'] ? 'Paid' : 'Pending',
                number_format($installment['remaining_balance'], 2),
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Installment #',
            'Payment Date',
            'EMI Amount (ZMW)',
            'Principal Component (ZMW)',
            'Interest Component (ZMW)',
            'Outstanding Principal (ZMW)',
            'Status',
            'Remaining Balance (ZMW)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Style data rows
        $lastRow = count($this->schedule) + 1;
        $sheet->getStyle('A2:H' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Add loan summary at the top
        $sheet->insertNewRowBefore(1, 6);
        $sheet->setCellValue('A1', 'Loan EMI Schedule Export');
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A2', 'Loan Number: ' . $this->loan->loan_number);
        $sheet->setCellValue('A3', 'Borrower: ' . $this->loan->borrower->first_name . ' ' . $this->loan->borrower->last_name);
        $sheet->setCellValue('A4', 'Principal Amount: ZMW ' . number_format($this->loan->principal_amount, 2));
        $sheet->setCellValue('A5', 'Monthly EMI: ZMW ' . number_format($this->loan->calculateEMI(), 2));
        $sheet->setCellValue('A6', 'Total Installments: ' . count($this->schedule));
        
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('A2:A6')->applyFromArray([
            'font' => ['bold' => true],
        ]);

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 18,
            'C' => 18,
            'D' => 22,
            'E' => 22,
            'F' => 22,
            'G' => 15,
            'H' => 20,
        ];
    }

    public function title(): string
    {
        return 'EMI Schedule';
    }
}

