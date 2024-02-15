<?php

namespace App\Jobs;

use App\Imports\ContactsImport;
use App\Models\Spreadsheet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class EnqueueSpreadsheetImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Spreadsheet $spreadsheet)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Excel::queueImport(
            new ContactsImport($this->spreadsheet),
            $this->spreadsheet->name,
            Spreadsheet::getStorageDisk()
        )->allOnQueue(Spreadsheet::getQueueConnection());
    }
}
