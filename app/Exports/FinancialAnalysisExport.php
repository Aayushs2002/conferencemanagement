<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Transaction-level dump of whatever the Financial Analysis filters currently
 * select. Rows arrive pre-filtered and already carrying `resolved_currency`
 * from FinancialAnalysisController::transactionQuery().
 */
class FinancialAnalysisExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * @param  callable  $convert  ($amount, $currency, $date) => base-currency float.
     *                             Injected rather than calling finance_to_base()
     *                             directly so the export honours whichever rate
     *                             basis the page was viewed with — including the
     *                             per-payment-date one, which needs the row date.
     */
    public function __construct(
        public $registrations,
        public $convert,
        public string $rateBasis = 'Fixed rate',
    ) {}

    public function collection()
    {
        $credit = (int) config('finance.credit_payment_type');
        $convert = $this->convert;

        return $this->registrations->values()->map(function ($r, $index) use ($credit, $convert) {
            $currency = $r->resolved_currency ?? config('finance.base_currency');
            $amount = is_numeric($r->amount) ? (float) $r->amount : 0;

            return [
                'S.No.' => $index + 1,
                'Registration ID' => $r->registration_id ?? '-',
                'Name' => $r->user?->fullName($r->user) ?? 'Dummy registrant',
                'Email' => $r->user?->email ?? '-',
                'Country' => $r->user?->userDetail?->country?->country_name ?? '-',
                'Registrant Type' => $r->registrant_type_text,
                'Payment Method' => finance_payment_type_label($r->payment_type),
                'Status' => $r->payment_type == $credit ? 'Credit' : $r->verified_status_text,
                'Currency' => $currency,
                'Amount' => $amount,
                'Amount (base)' => round($convert($amount, $currency, $r->created_at?->toDateString()), 2),
                'Transaction ID' => $r->transaction_id ?? '-',
                'Registered On' => $r->created_at?->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'S.No.', 'Registration ID', 'Name', 'Email', 'Country', 'Registrant Type',
            'Payment Method', 'Status', 'Currency', 'Amount',
            'Amount ('.config('finance.base_currency').', '.$this->rateBasis.')',
            'Transaction ID', 'Registered On',
        ];
    }
}
