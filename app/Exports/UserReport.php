<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use \App\Exports\ReportTemplate;

class UserReport extends ReportTemplate
{
    public function __construct($models, $summary, $additionals = null)
    {
        $this->models = $models;
        $this->summary = $summary;
        $this->additionals = $additionals;

        $this->summary_start_row = 3;
        $this->summary_end_row = 3;

        $this->lists_headers = [
            ['column' => 'A'],
            ['column' => 'B'],
            ['column' => 'C']
        ];
    }

    public function view(): View
    {
        return view('exports.user_report', [
            'models' => $this->models,
            'summary' => $this->summary
        ]);
    }
}
