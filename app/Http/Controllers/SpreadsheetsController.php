<?php

namespace App\Http\Controllers;

use App\Imports\ContactsImport;
use App\Models\Spreadsheet;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Throwable;

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
    public function store(Request $request): InertiaResponse | RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx'
        ]);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator->errors())->withInput();
        }
        $file = $request->file('file')
            ->storeAs(
                '',
                now()->format('YmdHi') . "_{$request->file('file')->getClientOriginalName()}",
                'local'
            );
        if ($file === false) {
            return Inertia::render('ErrorPage', [
                'message' => "The file was not imported",
            ])->toResponse($request)->setStatusCode(HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        $spreadsheet = Spreadsheet::create([
            'user_id' => $request->user()->id,
            'path' => $file,
        ]);
        Excel::queueImport(new ContactsImport($spreadsheet), $file);
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
