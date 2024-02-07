<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('Contacts', [
            'contacts' => Contact::with(['spreadsheet'])->paginate()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Contacts', [
            'contacts' => Contact::with(['spreadsheet'])->paginate(),
            'showModalForm' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Contact::create($request->validate([
            'spreadsheet_id' => 'nullable',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|max:50',
            'document' => 'nullable|max:20',
        ]));
        return Redirect::route('contacts.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact): InertiaResponse
    {
        return Inertia::render('Contacts', [
            'contacts' => Contact::with(['spreadsheet'])->paginate(),
            'showModalForm' => true,
            'contact' => $contact
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $contact->fill(
            $request->validate([
                'spreadsheet_id' => 'nullable',
                'name' => 'required|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|max:50',
                'document' => 'nullable|max:20',
            ])
        );
        $contact->saveOrFail();
        return Redirect::route('contacts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->deleteOrFail();
        return Redirect::route('contacts.index');
    }
}
