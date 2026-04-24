<?php

namespace App\Http\Controllers;

use App\Enums\ContactRequestType;
use App\Http\Requests\ContactRequest\StoreContactRequest;
use App\Http\Requests\ContactRequest\UpdateContactRequest;
use App\Http\Resources\ContactRequestResource;
use App\Models\ContactRequest;
use Illuminate\Support\Facades\Log;

class ContactRequestController extends Controller
{
    public function index()
    {
        Log::info('ContactRequestController@index hit', [
            'authenticated' => auth()->check(),
            'user' => auth()->id(),
            'guard' => auth()->getDefaultDriver(),
        ]);

        return ContactRequestResource::collection(ContactRequest::latest()->get());
    }

    public function store(StoreContactRequest $request)
    {
        $contactRequest = ContactRequest::create(array_merge(
            $request->validated(),
            ['type' => ContactRequestType::General]
        ));

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
