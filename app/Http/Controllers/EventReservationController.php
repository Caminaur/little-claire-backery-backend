<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventReservation\StoreEventReservationRequest;
use App\Http\Requests\EventReservation\UpdateEventReservationRequest;
use App\Http\Resources\EventReservationResource;
use App\Mail\NewReservationAdmin;
use App\Mail\ReservationConfirmation;
use App\Models\EventReservation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EventReservationController extends Controller
{
    public function index()
    {
        return EventReservationResource::collection(
            EventReservation::latest()->paginate(20)
        );
    }

    public function store(StoreEventReservationRequest $request)
    {
        $reservation = EventReservation::create($request->validated());
        Log::info("a");
        Mail::to(config('services.admin.email'))->send(new NewReservationAdmin($reservation));
        Mail::to($reservation->email)->send(new ReservationConfirmation($reservation));

        return (new EventReservationResource($reservation))->response()->setStatusCode(201);
    }

    public function show(EventReservation $eventReservation)
    {
        return new EventReservationResource($eventReservation);
    }

    public function update(UpdateEventReservationRequest $request, EventReservation $eventReservation)
    {
        $eventReservation->update($request->validated());

        return new EventReservationResource($eventReservation);
    }

    public function destroy(EventReservation $eventReservation)
    {
        $eventReservation->delete();

        return response()->noContent(204);
    }
}
