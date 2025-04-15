<?php

namespace App\Exports;

use App\Models\VendorPayment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VendorPaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $month;
    protected $year;

    /**
     * Create a new export instance.
     *
     * @param int $month The month to export
     * @param int $year The year to export
     */
    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get start and end dates for the selected month
        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return VendorPayment::with(['vendor', 'invoice', 'clientPayment', 'creator', 'approver', 'payer'])
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->orderBy('paid_at', 'desc')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Payment ID',
            'Vendor',
            'Amount',
            'Currency',
            'Payment Date',
            'Payment Method',
            'Invoice Number',
            'Client',
            'Status',
            'Created By',
            'Approved By',
            'Approved Date',
            'Paid By',
            'Payment Date',
            'Transaction Reference',
            'Notes'
        ];
    }

    /**
     * @param mixed $payment
     * @return array
     */
    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->vendor->company_name,
            $payment->amount,
            $payment->currency,
            $payment->payment_date->format('Y-m-d'),
            $payment->getPaymentMethodLabel(),
            $payment->invoice ? $payment->invoice->invoice_number : 'N/A',
            $payment->clientPayment ? $payment->clientPayment->client_name : 'N/A',
            ucfirst($payment->status),
            optional($payment->creator)->name,
            optional($payment->approver)->name,
            $payment->approved_at ? $payment->approved_at->format('Y-m-d H:i:s') : 'N/A',
            optional($payment->payer)->name,
            $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : 'N/A',
            $payment->transaction_reference ?? 'N/A',
            $payment->notes ?? ''
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        $monthName = Carbon::createFromDate($this->year, $this->month, 1)->format('F');
        return "Vendor Payments - {$monthName} {$this->year}";
    }
}