<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResultsSelectedBulkExport implements FromArray, WithHeadings
{
    protected $results;

    public function __construct(array $results)
    {
        $this->results = $results;
    }

    public function array(): array
    {
        return $this->results;
    }

    public function headings(): array
    {
        return [
            'id',
            'ping',
            'download',
            'upload',
            'server id',
            'server url',
            'server name',
            'result url',
            'scheduled',
            'successful',
            'data',
            'created at',
        ];
    }
}
