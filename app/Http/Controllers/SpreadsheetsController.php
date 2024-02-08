<?php

namespace App\Http\Controllers;

use App\Models\Spreadsheet;
use App\Http\Requests\SpreadsheetStoreRequest;
use App\Jobs\EnqueueSpreadsheetImportJob;
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
        $file = $request->file('file')
            ->storeAs(
                '',
                now()->format('YmdHi') . "_{$request->file('file')->getClientOriginalName()}",
                'local'
            );
        $spreadsheet = Spreadsheet::create([
            'user_id' => $request->user()->id,
            'path' => $file,
        ]);

        EnqueueSpreadsheetImportJob::dispatch($spreadsheet);
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
