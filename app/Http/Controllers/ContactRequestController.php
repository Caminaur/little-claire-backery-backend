<?php

namespace App\Http\Controllers;

use App\Enums\ContactRequestType;
use App\Models\ContactRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email',
            'phone' => 'required|string',
            'message' => 'string|nullable',
            'type' => [
                'required',
                'string',
                Rule::enum(ContactRequestType::class)
            ],
        ]);

        $contactRequest = ContactRequest::create($data);

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
    public function update(Request $request, ContactRequest $contactRequest)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|string|email',
            'phone' => 'sometimes|string',
            'message' => 'sometimes|string|nullable',
            'type' => [
                'required',
                'string',
                Rule::enum(ContactRequestType::class)
            ],
            'is_read' => 'sometimes|boolean',
        ]);

        $contactRequest->update($data);

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
