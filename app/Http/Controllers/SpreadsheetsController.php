<?php

namespace App\Http\Controllers;

use App\Models\Spreadsheet;
use App\Http\Requests\SpreadsheetStoreRequest;
use App\Jobs\EnqueueSpreadsheetImportJob;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SpreadsheetsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): InertiaResponse
    {
        return Inertia::render(
            'Spreadsheets',
            ['spreadsheets' => Spreadsheet::with(['user'])->paginate()]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SpreadsheetStoreRequest $request): InertiaResponse
    {
        $file = $request->file('file');
        $spreadsheet = Spreadsheet::create([
            'user_id' => $request->user()->id,
            'name' => Spreadsheet::generateFilename($file),
        ]);
        $spreadsheet->storeFile($file);
        $spreadsheet->save();
        EnqueueSpreadsheetImportJob::dispatch($spreadsheet)->onQueue(Spreadsheet::getQueueConnection());
        return Inertia::render(
            'Spreadsheets',
            ['spreadsheets' => Spreadsheet::with(['user'])->paginate()]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
