<?php

namespace App\Traits;

use App\Http\Resources\TripResource;
use App\Models\Notification;
use App\Models\PhoneToken;
use App\Models\Trip;
use App\Models\User;
use App\Models\UserLocation;

trait FirebaseNotification
{

    private string $serverKey = 'AAAAWOla850:APA91bEN_EHuUvHkUIynXTYTXe2QinEsduSoWTn15b9T4lN4laXQ5SuFgDHkM33YPNnAT2oijshaYwIDyZKE5JN-WWiH8hU8fmPTso7rOFKwe8gN2aim1wETZCqDPvHHvctJUatqTQ7p';

    public function sendFirebaseNotification($data, $user_id = null, $type = 'user', $create = true)
    {

        $url = 'https://fcm.googleapis.com/fcm/send';

        if ($user_id != null && $type == 'user') {
            $userIds = User::where('id', '=', $user_id)->pluck('id')->toArray();
            $tokens = PhoneToken::whereIn('user_id', $userIds)->pluck('token')->toArray();
        } elseif ($user_id != null && $type == 'acceptTrip') {
            $userIds = User::where('id', '=', $user_id)->pluck('id')->toArray();
            $tokens = PhoneToken::whereIn('user_id', $userIds)->pluck('token')->toArray();
        } elseif ($user_id !== null && $type === 'nearDrivers') {
            $user = UserLocation::where('user_id', $user_id)->first();

            if ($user) {
                $userLatitude = $user->lat;
                $userLongitude = $user->long;

                $distanceLimit = 500.0;
                $increment = 500.0;
                $maxDistance = 3000.0;

                do {
                    $nearbyDrivers = UserLocation::whereNotNull('driver_id')
                        ->select('driver_id', 'lat', 'long')
                        ->get()
                        ->filter(function ($driver) use ($userLatitude, $userLongitude, $distanceLimit) {
                            $driverDistance = $this->calculateDistance($userLatitude, $userLongitude, $driver->lat, $driver->long);
                            return $driverDistance <= $distanceLimit;
                        });

                    if ($nearbyDrivers->isEmpty()) {
                        $distanceLimit += $increment;
                    }
                } while ($nearbyDrivers->isEmpty() && $distanceLimit <= $maxDistance);

                if ($nearbyDrivers->isEmpty()) {
                    echo "لم يتم العثور على سائقين قريبين ضمن الحد الأقصى للمسافة {$maxDistance} متر.";
                } else {
                    foreach ($nearbyDrivers as $driver) {
                        $driverId = $driver->driver_id;
                        $tokens = PhoneToken::where('user_id', $driverId)->pluck('token')->toArray();
                    }
                }
            }



            // dd($nearbyDrivers['driver_id']);
            // Fetch tokens of nearby drivers
            // $driverIds = $nearbyDrivers->pluck('id')->toArray();
            // $tokens = PhoneToken::whereIn('user_id', $driverIds)->pluck('token')->toArray();
        } elseif ($user_id != null && $type == 'driver') {
            $userIds = User::where('id', '=', $user_id)->pluck('id')->toArray();
            $tokens = PhoneToken::whereIn('user_id', $userIds)->pluck('token')->toArray();
        } elseif ($user_id == null && $type == 'all_user') {
            $usersIds = User::where('type', '=', 'user')->pluck('id')->toArray();
            $tokens = PhoneToken::whereIn('user_id', $usersIds)->pluck('token')->toArray();
        } elseif ($user_id == null && $type == 'all_driver') {
            $usersIds = User::where('type', '=', 'driver')
                ->where('status', '=', true)->pluck('id')->toArray();
            $tokens = PhoneToken::whereIn('user_id', $usersIds)->pluck('token')->toArray();
        } else {
            $userIds = User::pluck('id')->toArray();
            $tokens = PhoneToken::whereIn('user_id', $userIds)->pluck('token')->toArray();
        }

        if (!isset($data['trip_id'])) {
            $data['trip_id'] = null;
        }


        if ($create === true) {
            //start notification store
            Notification::query()
                ->create([
                    'title' => $data['title'],
                    'description' => $data['body'],
                    'user_id' => $user_id ?? null,
                    'type' => $type,
                    'trip_id' => $data['trip_id']
                ]);
        }

        if (isset($data['trip_id'])) {
            $trip = Trip::query()->find($data['trip_id']);
        }

        $trip = $data['trip_id'];

        $fields = array(
            // 'registration_ids' => $tokens,
            'registration_ids' => $tokens,
            'notification' => $data,
            'data' => $trip != null ? ["trip" => $trip] : [],
        );

        $fields = json_encode($fields);

        $headers = array(
            'Authorization: key=' . $this->serverKey,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a =
            sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;
        return $distance;
    }
}
