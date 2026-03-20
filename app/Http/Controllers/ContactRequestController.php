<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest\StoreContactRequest;
use App\Http\Requests\ContactRequest\UpdateContactRequest;
use App\Http\Resources\ContactRequestResource;
use App\Models\ContactRequest;

class ContactRequestController extends Controller
{
    public function index()
    {
        return ContactRequestResource::collection(ContactRequest::latest()->get());
    }

    public function store(StoreContactRequest $request)
    {
        $contactRequest = ContactRequest::create($request->validated());

        return (new ContactRequestResource($contactRequest))->response()->setStatusCode(201);
    }

    public function show(ContactRequest $contactRequest)
    {
        return new ContactRequestResource($contactRequest);
    }

    public function update(UpdateContactRequest $request, ContactRequest $contactRequest)
    {
        $contactRequest->update($request->validated());

        return new ContactRequestResource($contactRequest);
    }

    public function destroy(ContactRequest $contactRequest)
    {
        $contactRequest->delete();

        return response()->noContent(204);
    }
}
