
<?php

Broadcast::channel('trip.{tripId}', function ($user, $tripId) {
    $trip = \App\Domain\Trip\Entities\Trip::find($tripId);
    return $user->id === $trip->passenger_id || $user->id === $trip->driver_id;
});
