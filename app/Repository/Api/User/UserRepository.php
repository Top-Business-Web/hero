<?php

namespace App\Repository\Api\User;

use App\Http\Resources\AreaResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\TripResource;
use App\Http\Resources\UserResource;
use App\Interfaces\Api\User\UserRepositoryInterface;
use App\Models\AddressFavorite;
use Carbon\Carbon;
use App\Models\Area;
use App\Models\City;
use App\Models\Trip;
use App\Models\User;
use App\Models\Slider;
use App\Models\Setting;
use App\Models\TripRates;
use App\Traits\PhotoTrait;
use Illuminate\Http\Request;
use App\Repository\ResponseApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\TripRateResource;
use Illuminate\Support\Facades\Validator;

class UserRepository extends ResponseApi implements UserRepositoryInterface
{
    use PhotoTrait;

    public function getAllCities(): JsonResponse
    {
        $cities = City::with('area')->get();
        return self::returnResponseDataApi(CityResource::collection($cities), "تم الحصول علي بيانات جميع المدن بنجاح", 200);
    } // getAllCities

    public function getAllAreas(): JsonResponse
    {
        $area = Area::with('city')->get();
        return self::returnResponseDataApi(AreaResource::collection($area), "تم الحصول علي بيانات جميع المدن بنجاح", 200);
    } // getAllAreas

    public function register(Request $request): JsonResponse
    {
        try {

            $rules = [
                'name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|numeric|unique:users,phone',
                'img' => 'required|image',
                'type' => 'required|in:user,driver',
                'birth' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules, [
                'email.unique' => 406,
                'phone.numeric' => 407,
                'phone.unique' => 408,
            ]);


            if ($validator->fails()) {
                $errors = collect($validator->errors())->flatten(1)[0];
                if (is_numeric($errors)) {
                    $errors_arr = [
                        406 => 'Failed,Email already exists',
                        407 => 'Failed,Phone number must be an number',
                        408 => 'Failed,Phone already exists',
                    ];
                    $code = collect($validator->errors())->flatten(1)[0];
                    return self::returnResponseDataApi(null, $errors_arr[$errors] ?? 500, $code);
                }
                return self::returnResponseDataApi(null, $validator->errors()->first(), 422);
            }

            if ($request->hasFile('img')) {
                $image = $this->saveImage($request->img, 'uploads/users', 'photo');
            }

            $storeNewUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('123456'),
                'phone' => $request->phone,
                'img' => $image ?? 'uploads/users/avatar.png',
                'type' => $request->type,
                'birth' => $request->birth,
                'status' => 1
            ]);

            if (isset($storeNewUser)) {
                $credentials = ['phone' => $request->phone, 'password' => '123456'];
                $storeNewUser['token'] = auth()->guard('user-api')->attempt($credentials);
                return self::returnResponseDataApi(new UserResource($storeNewUser), "تم تسجيل بيانات المستخدم بنجاح", 200);
            } else {

                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500, 500);
            }
        } catch (\Exception $exception) {

            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // register

    public function login(Request $request): JsonResponse
    {

        try {
            $rules = [
                'phone' => 'required|exists:users,phone',
            ];
            $validator = Validator::make($request->all(), $rules, [
                'phone.exists' => 409,
            ]);

            if ($validator->fails()) {

                $errors = collect($validator->errors())->flatten(1)[0];
                if (is_numeric($errors)) {

                    $errors_arr = [
                        409 => 'Failed,phone not exists',
                    ];

                    $code = collect($validator->errors())->flatten(1)[0];
                    return self::returnResponseDataApi(null, $errors_arr[$errors] ?? 500, $code);
                }
                return self::returnResponseDataApi(null, $validator->errors()->first(), 422, 422);
            }
            $credentials = ['phone' => $request->phone, 'password' => '123456'];
            $token = Auth::guard('user-api')->attempt($credentials);
            if (!$token) {
                return self::returnResponseDataApi(null, "يانات الدخول غير صحيحه برجاء المحاوله مره اخري", 403, 403);
            }
            $user = Auth::guard('user-api')->user();
            $user['token'] = $token;
            return self::returnResponseDataApi(new UserResource($user), "تم تسجيل الدخول بنجاح", 200);
        } catch (\Exception $exception) {

            return self::returnResponseDataApi(null, $exception->getMessage(), 500);
        }
    } // login

    public function logout(): JsonResponse
    {

        try {
            Auth::guard('user-api')->logout();
            return self::returnResponseDataApi(null, "تم تسجيل الخروج بنجاح", 200);
        } catch (\Exception $exception) {

            return self::returnResponseDataApi(null, $exception->getMessage(), 500, 500);
        }
    } // logout

    public function deleteAccount(): JsonResponse
    {

        try {

            $user = Auth::guard('user-api')->user();
            if ($user->type == 'driver') {

                return self::returnResponseDataApi(null, "حساب السائق غير مصرح له بالحذف", 403, 403);
            } else {
                $user->delete();
                Auth::guard('user-api')->logout();
                return self::returnResponseDataApi(null, "تم حذف الحساب بنجاح وتم تسجيل الخروج من التطبيق", 200);
            }
        } catch (\Exception $exception) {

            return self::returnResponseDataApi(null, $exception->getMessage(), 500, 500);
        }
    } // deleteAccount

    public function setting(): JsonResponse
    {

        try {

            $setting = Setting::query()->first();

            if (!$setting) {

                return self::returnResponseDataApi(null, "لا يوجد اي اعدادات بالموقع الي الان", 404, 404);
            } else {

                return self::returnResponseDataApi(new SettingResource($setting), "تم الحصول علي بيانات الشروط والاحكام بنجاح", 200);
            }
        } catch (\Exception $exception) {

            return self::returnResponseDataApi(null, $exception->getMessage(), 500, 500);
        }
    } // setting

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

    public function userHome(): JsonResponse
    {
        $home['sliders'] = Slider::query()
            ->select('image', 'link')
            ->where('status', '=', true)
            ->get();
        $home['new_trips'] = Trip::query()
            ->where('type', '=', 'new')
            ->where('user_id', '=', Auth::user()->id)
            ->whereDate('created_at', '=', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->get();
        $home['user'] = new UserResource(User::find(Auth::user()->id));
        return self::returnResponseDataApi($home, "تم الحصول علي بيانات الرئيسية بنجاح", 200);
    } // user home

    public function editProfile(Request $request): JsonResponse
    {
        $user = User::find(Auth::user()->id);
        try {
            $rules = [
                'name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'required|numeric|unique:users,phone,' . $user->id,
                'img' => 'image',
                'birth' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules, [
                'email.unique' => 406,
                'phone.numeric' => 407,
                'phone.unique' => 408,
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();

                if (is_numeric($firstError)) {
                    $errorsArr = [
                        406 => 'Failed, Email already exists',
                        407 => 'Failed, Phone number must be a number',
                        408 => 'Failed, Phone already exists',
                    ];
                    return self::returnResponseDataApi(null, $errorsArr[$firstError] ?? 'Error occurred', $firstError);
                }

                return self::returnResponseDataApi(null, $firstError, 422);
            }

            if ($request->hasFile('img')) {
                $image = $this->saveImage($request->img, 'uploads/users', 'photo');
                if (file_exists($user->img)) {
                    unlink($user->img);
                }
            } else {
                $image = $user->img;
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->img = $image;
            $user->birth = $request->birth;
            $user->save();

            if ($user->save()) {
                return self::returnResponseDataApi(new UserResource($user), "تم تحديث بيانات المستخدم بنجاح", 200);
            } else {
                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500, 500);
            }
        } catch (\Exception $exception) {

            return self::returnResponseDataApi(null, $exception->getMessage(), 500, 500);
        }
    } // edit profile

    public function createTrip(Request $request): JsonResponse
    {
        try {
            $rules = [
                'from_address' => 'required',
                'from_long' => 'required',
                'trip_type' => 'required',
                'from_lat' => 'required',
                'to_address' => 'required',
                'to_long' => 'nullable',
                'to_lat' => 'nullable',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }
            $checkQuickTrip = Trip::query()
                ->where('user_id', '=', Auth::user()->id)
                ->where('trip_type', '!=', 'scheduled')
                ->where('ended', '=', 0)->latest()->first();
            if ($checkQuickTrip) {
                return self::returnResponseDataApi(null, 'هناك رحلة حالية لم تنتهي بعد لنفس العميل', 200, 200);
            }

            $createQuickTrip = Trip::query()
                ->create([
                    'from_address' => $request->from_address,
                    'from_long' => $request->from_long,
                    'from_lat' => $request->from_lat,
                    'to_address' => $request->to_address,
                    'to_long' => $request->to_long,
                    'to_lat' => $request->to_lat,
                    'user_id' => Auth::user()->id,
                    'type' => 'new',
                    'trip_type' => $request->trip_type,
                ]);

            if (isset($createQuickTrip)) {
                return self::returnResponseDataApi(new TripResource($createQuickTrip), "تم انشاء طلب الرحلة بنجاح", 201, 200);
            } else {
                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", false, 500);
            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // start trip

    public function cancelTrip(Request $request): JsonResponse
    {
        try {
            $trip = Trip::query()
                ->where('user_id', '=', Auth::user()->id)
                ->where('type', '=', 'new')
                ->where('ended', '=', 0)
                ->first();
            if ($trip) {
                $trip->delete();
                return self::returnResponseDataApi(null, 'تم الغاء الرحلة بنجاح', 200);
            } else {
                return self::returnResponseDataApi(null, "لا يوجد لديك اي رحلة جديدة", 500, 500);
            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // cancel trip

    public function createScheduleTrip(Request $request): JsonResponse
    {
        try {
            $rules = [
                'from_address' => 'required',
                'from_long' => 'required',
                'from_lat' => 'required',
                'to_address' => 'required',
                'to_long' => 'required',
                'to_lat' => 'required',
                'date' => 'required',
                'time' => 'required',
            ];

            $dateTimeString = $request->date . ' ' . $request->time;
            $scheduleDate = Carbon::createFromFormat('Y-m-d H:i:s', $dateTimeString);

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            $checkTrip = Trip::query()
                ->create([
                    'from_address' => $request->from_address,
                    'from_long' => $request->from_long,
                    'from_lat' => $request->from_lat,
                    'to_address' => $request->to_address,
                    'to_long' => $request->to_long,
                    'to_lat' => $request->to_lat,
                    'user_id' => Auth::user()->id,
                    'type' => 'new',
                    'trip_type' => 'scheduled',
                    'created_at' => $scheduleDate,
                ]);

            if (isset($checkTrip)) {
                return self::returnResponseDataApi(new TripResource($checkTrip), "تم انشاء طلب الرحلة مجدولة في وقت لاحق بنجاح", 201, 200);
            } else {
                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", false, 500);
            }


        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    } // start create Schedule Trip

    public function userAllTrip(Request $request): JsonResponse
    {
        try {
            if ($request->has('type')) {
                if ($request->type == 'reject') {
                    $trips = Trip::query()
                        ->where('user_id', '=', Auth::user()->id)
                        ->where('type', '=', 'reject')
                        ->where('ended', '=', 0)
                        ->orderBy('created_at', 'DESC')
                        ->latest()->get();
                    $data = $trips;
                } elseif ($request->type == 'complete') {
                    $trips = Trip::query()
                        ->where('user_id', '=', Auth::user()->id)
                        ->where('type', '=', 'complete')
                        ->where('ended', '=', 1)
                        ->orderBy('created_at', 'DESC')
                        ->latest()->get();
                    $data = $trips;

                } else {
                    $trips = Trip::query()
                        ->where('type', '=', 'new')
                        ->where('user_id', '=', Auth::user()->id)
                        ->where('ended', '=', 0)
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
    } // user all trip

    public function favouriteLocations(): JsonResponse
    {
        try {
            $data = AddressFavorite::query()
                ->where('user_id', '=', Auth::user()->id)
                ->latest()->get();
            if (count($data) > 0) {
                return self::returnResponseDataApi($data, 'تم الحصول علي جميع بيانات الموقع المفضلة بنجاح', 200, 200);
            } else {
                return self::returnResponseDataApi($data, 'لا يوجد مواقع مفضلة حالية', 200, 200);

            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    }// favouriteLocations

    public function createFavouriteLocations(Request $request): JsonResponse
    {
        try {
            $rules = [
                'address' => 'required',
                'lat' => 'required',
                'long' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            $address = AddressFavorite::query()
                ->create([
                    'user_id' => Auth::user()->id,
                    'address' => $request->address,
                    'lat' => $request->lat,
                    'long' => $request->long,
                ]);

            if (isset($address)) {
                return self::returnResponseDataApi($address, "تم اضافة الموقع في المفضلة بنجاح", 201, 200);
            } else {
                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", false, 500);
            }


        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    }// favouriteLocations

    public function removeFavouriteLocations(Request $request): JsonResponse
    {
        try {
            $rules = [
                'address_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            $address = AddressFavorite::query()
                ->find($request->address_id);

            if ($address) {
                $address->delete();
                return self::returnResponseDataApi(null, "تم حذف الموقع من المفضلة بنجاح", 200, 200);
            } else {
                return self::returnResponseDataApi(null, "لا يوجد موقع في المفضلة بهذا المعرف", 404, 404);
            }


        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    }// favouriteLocations

    public function getAllSettings(): JsonResponse
    {
        $settings = Setting::first();
        return self::returnResponseDataApi($settings, "تم الحصول علي بيانات جميع الاعدادت بنجاح", 200);
    }

    public function createTripRate(Request $request): JsonResponse
    {
        try {
            $rules = [
                'trip_id' => 'required',
                'to' => 'required',
                'rate' => 'required',
                'description' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return self::returnResponseDataApi(null, $firstError, 422);
            }

            $existingTripRate = TripRates::where('trip_id', $request->trip_id)
                ->where('from', Auth::user()->id)
                ->first();

            if ($existingTripRate) {
                return self::returnResponseDataApi(null, "يوجد بالفعل تقييم لنفس الرحلة.", 422);
            }
            $createTripRate = TripRates::query()
                ->create([
                    'trip_id' => $request->trip_id,
                    'from' => Auth::user()->id,
                    'to' => $request->to,
                    'rate' => $request->rate,
                    'description' => $request->description,
                ]);

            if (isset($createTripRate)) {
                return self::returnResponseDataApi(new TripRateResource($createTripRate), "تم انشاء التقييم بنجاح", 201, 200);
            } else {
                return self::returnResponseDataApi(null, "يوجد خطاء ما أثناء دخول البيانات", false, 500);
            }
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    }
}
