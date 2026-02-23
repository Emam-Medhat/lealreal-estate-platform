<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Company',
            'Status',
            'Source',
            'Estimated Value',
            'Created At',
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->first_name . ' ' . $lead->last_name,
            $lead->email,
            $lead->phone,
            $lead->company,
            $lead->status->name ?? 'N/A',
            $lead->source->name ?? 'N/A',
            $lead->estimated_value,
            $lead->created_at->format('Y-m-d H:i'),
        ];
    }
}
