<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest\StoreContactRequest;
use App\Http\Requests\ContactRequest\UpdateContactRequest;
use App\Models\ContactRequest;

class ContactRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ContactRequest::latest()->get(['id', 'name', 'email', 'phone', 'message', 'type', 'is_read']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {

        $contactRequest = ContactRequest::create($request->validated());

        return response()->json($contactRequest, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactRequest $contactRequest)
    {
        return $contactRequest->only(['id', 'name', 'email', 'phone', 'message', 'type', 'is_read']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, ContactRequest $contactRequest)
    {
        $contactRequest->update($request->validated());
        return response()->json($contactRequest, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactRequest $contactRequest)
    {
        $contactRequest->delete();

        return response()->noContent(204);
    }
}
