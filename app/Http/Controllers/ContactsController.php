<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Http\Requests\ContactStoreRequest;
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
    public function store(ContactStoreRequest $request): RedirectResponse
    {
        Contact::create($request->all());
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
    public function update(ContactStoreRequest $request, Contact $contact): RedirectResponse
    {
        $contact->fill($request->all());
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
