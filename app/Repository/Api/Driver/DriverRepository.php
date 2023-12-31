<?php

namespace App\Repository\Api\Driver;

use App\Http\Resources\DriverDocumentResource;
use App\Http\Resources\DriverResource;
use App\Http\Resources\TripResource;
use App\Http\Resources\WalletResource;
use App\Interfaces\Api\Driver\DriverRepositoryInterface;
use App\Models\DriverDetails;
use App\Models\DriverDocuments;
use App\Models\DriverWallet;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\User;
use App\Repository\ResponseApi;
use App\Traits\PhotoTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DriverRepository extends ResponseApi implements DriverRepositoryInterface
{
    use PhotoTrait;

    public function registerDriver(Request $request): JsonResponse
    {
        try {

            $rules = [
                'bike_type' => 'required',
                'bike_model' => 'required',
                'bike_color' => 'required',
                'area_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            $user_id = Auth::user()->id;
            $storeNewDriverDetails = DriverDetails::query()->updateOrCreate([
                'driver_id' => $user_id
            ], [
                'bike_type' => $request->bike_type,
                'bike_model' => $request->bike_model,
                'bike_color' => $request->bike_color,
                'area_id' => $request->area_id,
                'driver_id' => $user_id
            ]);

            if (isset($storeNewDriverDetails)) {
                return self::returnResponseDataApi(new DriverResource($storeNewDriverDetails), "تم تسجيل بيانات السائق بنجاح", 200);
            } else {

                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500, 500);

            }
        } catch (\Exception $exception) {

            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }


    } // registerDriver

    public function registerDriverDoc(Request $request): JsonResponse
    {
        try {

            $rules = [
                'agency_number' => 'required|image',
                'bike_license' => 'required|image',
                'id_card' => 'required|image',
                'house_card' => 'required|image',
                'bike_image' => 'required|image',
            ];

            $msg = [
                'agency_number.image' => 'agency number must be image',
                'bike_license.image' => 'bike license must be image',
                'id_card.image' => 'id card must be image',
                'house_card.image' => 'house card must be image',
                'bike_image.image' => 'bike image must be image',
            ];
            $validator = Validator::make($request->all(), $rules, $msg);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            if ($request->hasFile('agency_number')) {
                $agency_number = $this->saveImage($request->agency_number, 'uploads/drivers/documents', 'photo');
            }
            if ($request->hasFile('bike_license')) {
                $bike_license = $this->saveImage($request->bike_license, 'uploads/drivers/documents', 'photo');
            }
            if ($request->hasFile('id_card')) {
                $id_card = $this->saveImage($request->id_card, 'uploads/drivers/documents', 'photo');
            }
            if ($request->hasFile('house_card')) {
                $house_card = $this->saveImage($request->house_card, 'uploads/drivers/documents', 'photo');
            }
            if ($request->hasFile('bike_image')) {
                $bike_image = $this->saveImage($request->bike_image, 'uploads/drivers/documents', 'photo');
            }


            $user_id = Auth::user()->id;
            $storeNewDriverDoc = DriverDocuments::query()->updateOrCreate([
                'driver_id' => $user_id
            ], [
                'agency_number' => $agency_number,
                'bike_license' => $bike_license,
                'id_card' => $id_card,
                'house_card' => $house_card,
                'bike_image' => $bike_image,
                'status' => false,
            ]);

            if (isset($storeNewDriverDoc)) {
                return self::returnResponseDataApi(new DriverDocumentResource($storeNewDriverDoc), "تم تسجيل بيانات التوكتوك بنجاح في انتظار موافقة المشرفين", 200);
            } else {

                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500, 500);

            }
        } catch (\Exception $exception) {

            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // registerDriverDoc

    public function checkDocument(Request $request): JsonResponse
    {
        $DriverDocuments = DriverDocuments::query()->where('driver_id', '=', Auth::user()->id)->first();
        if ($DriverDocuments->status == false) {
            return self::returnResponseDataApi(['status' => $DriverDocuments->status], "في انتظار قبول المستندات الخاصة بك حاول في وقت لاحق", 200);
        } else {
            return self::returnResponseDataApi(['status' => $DriverDocuments->status], "تم قبول مستنداتك بنجاح يمكنك البدا في العمل", 200);
        }
    } // check Document

    public function changeStatus(Request $request): JsonResponse
    {
        $user = User::find(Auth::user()->id);
        ($user->status == 1) ? $user->status = 0 : $user->status = 1;
        $user->save();
        if ($user->status == false) {
            return self::returnResponseDataApi(['status' => $user->status], "انت الان خارج الخدمة", 200);
        } else {
            return self::returnResponseDataApi(['status' => $user->status], "انت الان في الخدمة", 200);
        }
    } // change status

    public function updateDriverDetails(Request $request): JsonResponse
    {
        try {

            $rules = [
                'bike_type' => 'required',
                'bike_model' => 'required',
                'bike_color' => 'required',
                'area_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            $user_id = Auth::user()->id;
            $storeNewDriverDetails = DriverDetails::query()
                ->where('driver_id', $user_id)->first();
            $storeNewDriverDetails->update([
                'bike_type' => $request->bike_type,
                'bike_model' => $request->bike_model,
                'bike_color' => $request->bike_color,
                'area_id' => $request->area_id,
                'driver_id' => $user_id
            ]);

            if ($storeNewDriverDetails->update()) {
                return self::returnResponseDataApi(new DriverResource($storeNewDriverDetails), "تم تحديث بيانات السائق بنجاح", 200);
            } else {
                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500, 500);
            }
        } catch (\Exception $exception) {

            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // update driver details

    public function updateDriverDocument(Request $request): JsonResponse
    {
        try {
            $updateDriverDoc = DriverDocuments::query()
                ->where('driver_id', Auth::user()->id)
                ->firstOrFail();

            $rules = [
                'agency_number' => 'required|image',
                'bike_license' => 'required|image',
                'id_card' => 'required|image',
                'house_card' => 'required|image',
                'bike_image' => 'required|image',
            ];

            $msg = [
                'agency_number.image' => 'agency number must be image',
                'bike_license.image' => 'bike license must be image',
                'id_card.image' => 'id card must be image',
                'house_card.image' => 'house card must be image',
                'bike_image.image' => 'bike image must be image',
            ];
            $validator = Validator::make($request->all(), $rules, $msg);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            if ($request->hasFile('agency_number')) {
                $agency_number = $this->saveImage($request->agency_number, 'uploads/drivers/documents', 'photo');
                if (file_exists($updateDriverDoc->agency_number)) {
                    unlink($updateDriverDoc->agency_number);
                }
            }
            if ($request->hasFile('bike_license')) {
                $bike_license = $this->saveImage($request->bike_license, 'uploads/drivers/documents', 'photo');
                if (file_exists($updateDriverDoc->bike_license)) {
                    unlink($updateDriverDoc->bike_license);
                }
            }
            if ($request->hasFile('id_card')) {
                $id_card = $this->saveImage($request->id_card, 'uploads/drivers/documents', 'photo');
                if (file_exists($updateDriverDoc->id_card)) {
                    unlink($updateDriverDoc->id_card);
                }
            }
            if ($request->hasFile('house_card')) {
                $house_card = $this->saveImage($request->house_card, 'uploads/drivers/documents', 'photo');
                if (file_exists($updateDriverDoc->house_card)) {
                    unlink($updateDriverDoc->house_card);
                }
            }
            if ($request->hasFile('bike_image')) {
                $bike_image = $this->saveImage($request->bike_image, 'uploads/drivers/documents', 'photo');
                if (file_exists($updateDriverDoc->bike_image)) {
                    unlink($updateDriverDoc->bike_image);
                }
            }

            $updateDriverDoc->update([
                'agency_number' => $agency_number,
                'bike_license' => $bike_license,
                'id_card' => $id_card,
                'house_card' => $house_card,
                'bike_image' => $bike_image,
                'status' => false,
            ]);

            if (isset($updateDriverDoc)) {
                return self::returnResponseDataApi(new DriverDocumentResource($updateDriverDoc), "تم تحديث بيانات التوكتوك بنجاح في انتظار موافقة المشرفين", 200);
            } else {
                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500, 500);
            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // update Driver Document

    public function startQuickTrip(Request $request): JsonResponse
    {
        try {
            $rules = [
                'from_address' => 'required',
                'from_long' => 'required',
                'from_lat' => 'required',
                'to_address' => 'required',
                'to_long' => 'required',
                'to_lat' => 'required',
                'name' => 'required',
                'phone' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }
            $checkQuickTrip = Trip::query()
                ->where('phone', '=', $request->phone)
                ->where('ended', '=', 0)->latest()->first();
            if ($checkQuickTrip) {
                return self::returnResponseDataApi(null, 'هناك رحلة حالية لم تنتهي بعد علي نفس الرقم', 200, 200);
            }

            $createQuickTrip = Trip::query()
                ->create([
                    'from_address' => $request->from_address,
                    'from_long' => $request->from_long,
                    'from_lat' => $request->from_lat,
                    'to_address' => $request->to_address,
                    'to_long' => $request->to_long,
                    'to_lat' => $request->to_lat,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'driver_id' => Auth::user()->id,
                    'type' => 'new',
                    'trip_type' => 'quick',
                    'time_ride' => Carbon::now()
                ]);

            if (isset($createQuickTrip)) {
                return self::returnResponseDataApi(new TripResource($createQuickTrip), "تم بدأ الرحلة الفورية بنجاح", 201, 200);
            } else {
                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", false, 500);
            }


        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // start quick trip

    public function endQuickTrip(Request $request): JsonResponse
    {
        $settigs = Setting::first(['vat', 'km']);
        try {
            $rules = [
                'distance' => 'required',
                'time' => 'required',
                'phone' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }
            $checkQuickTrip = Trip::query()
                ->where('phone', '=', $request->phone)
                ->where('driver_id', '=', Auth::user()->id)
                ->where('ended', '=', 0)
                ->whereIn('type', ['new', 'accept'])
                ->first();

            if ($checkQuickTrip) {
                $checkQuickTrip->time_arrive = Carbon::now();
                $checkQuickTrip->distance = $request->distance;
                $checkQuickTrip->time = $request->time;
                $price = $checkQuickTrip->distance * $settigs->km; // Calculate the total price based on the distance
                $vatTotal = $price * ($settigs->vat / 100); // Calculate 15% of the total price as VAT
                $total = $price - $vatTotal; // Calculate the total after deducting the VAT
                $checkQuickTrip->price = $price;
                $checkQuickTrip->ended = true;
                $checkQuickTrip->type = 'complete';

                if ($checkQuickTrip->save()) {
                    $wallet = DriverWallet::query()
                        ->where('driver_id', '=', Auth::user()->id)
                        ->whereDay('day', '=', Carbon::now())
                        ->first();
                    if (!$wallet) {
                        $wallet = DriverWallet::query()
                            ->create([
                                'driver_id' => Auth::user()->id,
                                'day' => Carbon::now(),
                                'total' => $total,
                                'vat_total' => $vatTotal,
                            ]);
                    } else {
                        $wallet->total += $total;
                        $wallet->vat_total += $vatTotal;
                        $wallet->save();
                    }
                    return self::returnResponseDataApi(new TripResource($checkQuickTrip), "تم نهاية الرحلة الفورية بنجاح", 201, 200);
                } else {
                    return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", false, 500);
                }
            } else {
                return self::returnResponseDataApi(null, "لا يوجد رحلة حالية علي هذا الرقم", false, 500);
            }

        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // endQuickTrip

    public function acceptTrip(Request $request): JsonResponse
    {
        try {
            $rules = [
                'trip_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }
            $checkTrip = Trip::query()
                ->where('id', '=', $request->trip_id)
                ->where('ended', '=', 0)
                ->where('type', '=', 'new')
                ->where('driver_id', '=', null)
                ->first();
            if ($checkTrip) {
                $checkTrip->driver_id = Auth::user()->id;
                $checkTrip->type = 'accept';
                if ($checkTrip->save()) {
                    return self::returnResponseDataApi(new TripResource($checkTrip), "تم تاكيد الرحلة بنجاح", 201, 200);
                } else {
                    return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", false, 500);
                }
            } else {
                return self::returnResponseDataApi(null, "لا يوجد رحلة فارغه بهذا المعرف", false, 500);
            }

        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // accept trip

    public function cancelTrip(Request $request): JsonResponse
    {
        try {
            $rules = [
                'trip_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }
            $checkTrip = Trip::query()
                ->where('id', '=', $request->trip_id)
                ->where('ended', '=', 0)
                ->where('type', '=', 'accept')
                ->where('driver_id', '!=', null)
                ->first();
            if ($checkTrip) {
                $checkTrip->driver_id = Auth::user()->id;
                $checkTrip->type = 'reject';
                if ($checkTrip->save()) {
                    return self::returnResponseDataApi(new TripResource($checkTrip), "تم الغاء الرحلة بنجاح", 201, 200);
                } else {
                    return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", false, 500);
                }
            } else {
                return self::returnResponseDataApi(null, "لا يوجد رحلة فارغه بهذا المعرف", false, 500);
            }

        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // cancel trip

    public function startTrip(Request $request): JsonResponse
    {
        try {
            $rules = [
                'trip_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }
            $checkTrip = Trip::query()
                ->where('id', '=', $request->trip_id)
                ->where('ended', '=', 0)
                ->where('type', '=', 'accept')
                ->where('driver_id', '=', Auth::user()->id)
                ->first();
            if ($checkTrip) {
                if ($checkTrip->time_ride != null) {
                    return self::returnResponseDataApi(new TripResource($checkTrip), "تم بالفعل بدا الرحلة بنجاح", 201, 200);
                }
                $checkTrip->time_ride = Carbon::now();
                if ($checkTrip->save()) {
                    return self::returnResponseDataApi(new TripResource($checkTrip), "تم بدا الرحلة بنجاح", 201, 200);
                } else {
                    return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", false, 500);
                }
            } else {
                return self::returnResponseDataApi(null, "لا يوجد رحلة فارغه بهذا المعرف", false, 500);
            }

        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // start trip

    public function endTrip(Request $request): JsonResponse
    {
        $settigs = Setting::first(['vat', 'km']);
        try {
            $rules = [
                'trip_id' => 'required',
                'distance' => 'required',
                'time' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }
            $checkTrip = Trip::query()
                ->where('id', '=', $request->trip_id)
                ->where('driver_id', '=', Auth::user()->id)
                ->where('ended', '=', 0)
                ->where('type', 'accept')
                ->first();

            if ($checkTrip) {
                $checkTrip->time_arrive = Carbon::now();
                $checkTrip->distance = $request->distance;
                $checkTrip->time = $request->time;
                $price = $checkTrip->distance * $settigs->km; // Calculate the total price based on the distance
                $vatTotal = $price * ($settigs->vat / 100); // Calculate 15% of the total price as VAT
                $total = $price - $vatTotal; // Calculate the total after deducting the VAT
                $checkTrip->price = $price;
                $checkTrip->ended = true;
                $checkTrip->type = 'complete';

                if ($checkTrip->save()) {
                    $wallet = DriverWallet::query()
                        ->where('driver_id', '=', Auth::user()->id)
                        ->whereDay('day', '=', Carbon::now())
                        ->first();
                    if (!$wallet) {
                        $wallet = DriverWallet::query()
                            ->create([
                                'driver_id' => Auth::user()->id,
                                'day' => Carbon::now(),
                                'total' => $total,
                                'vat_total' => $vatTotal,
                            ]);
                    } else {
                        $wallet->total += $total;
                        $wallet->vat_total += $vatTotal;
                        $wallet->save();
                    }
                    return self::returnResponseDataApi(new TripResource($checkTrip), "تم نهاية الرحلة الفورية بنجاح", 201, 200);
                } else {
                    return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", false, 500);
                }
            } else {
                return self::returnResponseDataApi(null, "لا يوجد رحلة حالية علي هذا الرقم", false, 500);
            }

        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // end trip

    public function driverAllTrip(Request $request): JsonResponse
    {
        try {
            if ($request->has('type')) {
                if ($request->type == 'reject') {
                    $trips = Trip::query()
                        ->where('driver_id', '=', Auth::user()->id)
                        ->where('type', '=', 'reject')
                        ->where('ended', '=', 0)
                        ->orderBy('created_at', 'DESC')
                        ->latest()->get();
                    $data = $trips;
                } elseif ($request->type == 'complete') {
                    $trips = Trip::query()
                        ->where('driver_id', '=', Auth::user()->id)
                        ->where('type', '=', 'complete')
                        ->where('ended', '=', 1)
                        ->orderBy('created_at', 'DESC')
                        ->latest()->get();
                    $data = $trips;

                } else {
                    $trips = Trip::query()
                        ->where('type', '=', 'new')
                        ->where('ended', '=', 0)
                        ->whereDay('created_at', '=', Carbon::now())
                        ->orderBy('created_at', 'DESC')
                        ->latest()->get();
                    $data = $trips;
                }

                if (count($data) > 0) {
                    return self::returnResponseDataApi(TripResource::collection($data), 'تم الحصول علي جميع بيانات الرحلات بنجاح', 200, 200);
                } else {
                    return self::returnResponseDataApi($data, 'عفوا لا يوجد رحلات حاليا', 200, 200);
                }

            } else {
                return self::returnResponseDataApi(null, 'يرجي ادخال النوع', 422, 422);
            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // get all trip by filter

    public function driverWallet(): JsonResponse
    {
        $vat = Setting::select('vat')->first()->vat;
        $driver = Auth::user();
        $wallet['vat_total'] = DriverWallet::query()
            ->where('driver_id', '=', $driver->id)
            ->where('status', '=', false)
            ->sum('vat_total');

        $trips = Trip::query()
            ->select(['id', 'price', 'updated_at'])
            ->where('driver_id', '=', $driver->id)
            ->where('type', '=', 'complete')
            ->where('created_at', '>=', Carbon::now()->subDays(7)) // Get trips from the last 7 days
            ->where('ended', '=', true)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($trips as $key => $trip) {
            $wallet['trips'][$key]['id'] = $trip->id;
            $wallet['trips'][$key]['vat'] = $trip->price * ($vat / 100);
            $wallet['trips'][$key]['time_arrive'] = $trip->updated_at->format('Y-m-d H:i:s');
        }

        if (!$wallet['vat_total']) {
            return self::returnResponseDataApi(null, 'لم يتم انشاء محفظة حتي الان قم باتمام اول رحلة لفتح المحفظة', 200);
        }
        return self::returnResponseDataApi($wallet, 'تم الحصول علي بيانات المحفظة بنجاح', 200);
    } // driverWallet

    public function driverProfit(Request $request): JsonResponse
    {
        try {
            $setting = Setting::first(['vat', 'km']);
            $driver = Auth::user();
            $toDay = Carbon::now()->format('Y-m-d');
            $lastWeek = Carbon::now()->subDays(7)->format('Y-m-d');
            $from = Carbon::parse($request->from)->format('Y-m-d');
            $to = Carbon::parse($request->to)->format('Y-m-d');

            if ($request->type == null) {
                return self::returnResponseDataApi(null, 'يرجي ادخال النوع', 422, 422);
            } elseif ($request->type == 'day') {
                $trip = Trip::query()
                    ->where('driver_id', '=', $driver->id)
                    ->where('type', '=', 'complete')
                    ->where('ended', '=', true)
                    ->whereDate('updated_at', '=', $toDay);

                $wallet = DriverWallet::query()
                    ->where('driver_id', '=', $driver->id)
                    ->where('status', '=', false)
                    ->whereDate('day', '=', $toDay);

                $profit['trips_count'] = $trip->count();
                $profit['total_trips_price'] = $trip->sum('price');
                $profit['trips_distance'] = $trip->sum('distance');
                $profit['km_price'] = $setting->km;
                $profit['total'] = $profit['total_trips_price'];
                $profit['vat_total'] = $wallet->sum('vat_total');
                $profit['net_total'] = $profit['total'] - $profit['vat_total'];

            } elseif ($request->type == 'week') {
                $trip = Trip::query()
                    ->where('driver_id', '=', $driver->id)
                    ->where('type', '=', 'complete')
                    ->where('ended', '=', true)
                    ->whereDate('updated_at', '<=', $toDay)
                    ->whereDate('updated_at', '>=', $lastWeek);

                $wallet = DriverWallet::query()
                    ->where('driver_id', '=', $driver->id)
                    ->where('status', '=', false)
                    ->whereDate('day', '<=', $toDay)
                    ->whereDate('day', '>=', $lastWeek);

                $profit['trips_count'] = $trip->count();
                $profit['total_trips_price'] = $trip->sum('price');
                $profit['trips_distance'] = $trip->sum('distance');
                $profit['km_price'] = $setting->km;
                $profit['total'] = $profit['total_trips_price'];
                $profit['vat_total'] = $wallet->sum('vat_total');
                $profit['net_total'] = $profit['total'] - $profit['vat_total'];


                $walletDays = DriverWallet::query()
                    ->where('driver_id', '=', $driver->id)
                    ->where('status', '=', false)
                    ->whereDate('day', '<=', $toDay)
                    ->whereDate('day', '>=', $lastWeek)
                    ->groupBy('day')
                    ->select('day', DB::raw('SUM(total) as total_amount'))
                    ->get();

                foreach ($walletDays as $key => $walletDay) {
                    $profit['trips'][$key]['price'] = $walletDay->total_amount;
                    $profit['trips'][$key]['day'] = $walletDay->day;
                }

            } elseif ($request->type == 'custom') {
                if ($request->from == null || $request->to == null) {
                    return self::returnResponseDataApi(null, 'يرجي ادخال التاريخ من والى', 422, 422);
                } else {
                    $trip = Trip::query()
                        ->where('driver_id', '=', $driver->id)
                        ->where('type', '=', 'complete')
                        ->where('ended', '=', true)
                        ->whereDate('updated_at', '<=', $to)
                        ->whereDate('updated_at', '>=', $from);

                    $wallet = DriverWallet::query()
                        ->where('driver_id', '=', $driver->id)
                        ->where('status', '=', false)
                        ->whereDate('day', '<=', $to)
                        ->whereDate('day', '>=', $from);

                    $profit['trips_count'] = $trip->count();
                    $profit['total_trips_price'] = $trip->sum('price');
                    $profit['trips_distance'] = $trip->sum('distance');
                    $profit['km_price'] = $setting->km;
                    $profit['total'] = $profit['total_trips_price'];
                    $profit['vat_total'] = $wallet->sum('vat_total');
                    $profit['net_total'] = $profit['total'] - $profit['vat_total'];

                }

            }

            return self::returnResponseDataApi($profit, 'تم الحصول علي بيانات الارباح بنجاح', 200);
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // driverProfit

}
