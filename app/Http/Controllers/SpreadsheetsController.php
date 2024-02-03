<?php

namespace App\Http\Controllers;

use App\Models\Spreadsheet;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class SpreadsheetsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render(
            'Spreadsheets/Index',
            ['spreadsheets' => Spreadsheet::with(['user'])->paginate()]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        $file = $request->file('file')
            ->storeAs(
                '',
                now()->format('YmdHi') . "_{$request->file('file')->getClientOriginalName()}"
            );
        if ($file === false) {
            return response(
                [
                    "message" => "The file was not imported"
                ],
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        return response(null, HttpResponse::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
