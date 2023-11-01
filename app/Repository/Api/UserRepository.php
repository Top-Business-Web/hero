<?php

namespace App\Repository\Api;

use App\Http\Resources\AreaResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\DriverResource;
use App\Http\Resources\SettingResource;
use App\Http\Resources\UserResource;
use App\Interfaces\Api\UserRepositoryInterface;
use App\Models\Area;
use App\Models\City;
use App\Models\DriverDetails;
use App\Models\Setting;
use App\Models\User;
use App\Repository\ResponseApi;
use App\Traits\PhotoTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserRepository extends ResponseApi implements UserRepositoryInterface
{
    use PhotoTrait;
    public function getAllCities(): JsonResponse
    {
        $cities = City::with('area')->get();
        return self::returnResponseDataApi($cities, "تم الحصول علي بيانات جميع المدن بنجاح", 200);
    }
    public function getAllAreas(): JsonResponse
    {
        $area = Area::with('city')->get();
        return self::returnResponseDataApi(AreaResource::collection($area), "تم الحصول علي بيانات جميع المدن بنجاح", 200);
    }

    public function register(Request $request): JsonResponse
    {
        try {

            $rules = [
                'name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|numeric|unique:users,email',
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

            if($request->hasFile('img')){
                $image = $this->saveImage($request->img,'uploads/users','photo');
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
                $credentials = ['phone' => $request->phone,'password' => '123456'];
                $storeNewUser['token'] = auth()->guard('user-api')->attempt($credentials);
                return self::returnResponseDataApi(new UserResource($storeNewUser), "تم تسجيل بيانات المستخدم بنجاح", 200);

            } else {

                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500, 500);

            }
        } catch (\Exception $exception) {

            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }

    }

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
                $errors = collect($validator->errors())->flatten(1)[0];
                if (is_numeric($errors)) {
                    $code = collect($validator->errors())->flatten(1)[0];
                    return self::returnResponseDataApi(null, 500, $code);
                }
                return self::returnResponseDataApi(null, $validator->errors()->first(), 422);
            }
            $user_id = Auth::user()->id;
            $storeNewDriverDetails = DriverDetails::updateOrCreate([
                'driver_id' => $user_id
            ],[
                'bike_type' => $request->bike_type,
                'bike_model' => $request->bike_model,
                'bike_color' => $request->bike_color,
                'area_id' => $request->area_id,
                'driver_id' => $user_id
            ]);

            if (isset($storeNewDriverDetails)) {
                $storeNewDriverDetails = new DriverResource($storeNewDriverDetails);
                return self::returnResponseDataApi($storeNewDriverDetails, "تم تسجيل بيانات السائق بنجاح", 200);
            } else {

                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء دخول البيانات", 500, 500);

            }
        } catch (\Exception $exception) {

            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }

    }


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
            $credentials = ['phone' => $request->phone,'password' => '123456'];
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

    }


    public function getProfile(Request $request): JsonResponse
    {
        try {

            $userAuth = Auth::guard('user-api')->user();
            $userAuth->token = $request->bearerToken();

            return self::returnResponseDataApi(new UserResource($userAuth), 'تم الحصول علي بيانات المستخدم بنجاح', 200);

        } catch (\Exception $exception) {

            return self::returnResponseDataApi(null, $exception->getMessage(), 500);
        }
    }

    public function changePassword(Request $request): JsonResponse
    {

        try {

            $rules = [
                'current_password' => 'required|min:6',
                'new_password' => 'required|min:6|confirmed',
            ];

            $validator = Validator::make($request->all(), $rules, [
                'new_password.confirmed' => 406,
            ]);

            if ($validator->fails()) {
                $errors = collect($validator->errors())->flatten(1)[0];

                if (is_numeric($errors)) {
                    $errors_arr = [
                        406 => 'Failed,The new password not confirmed',
                    ];

                    $code = collect($validator->errors())->flatten(1)[0];
                    return self::returnResponseDataApi($errors_arr[$errors] ?? 500, $code, 200);
                }
                return self::returnResponseDataApi(null, $validator->errors()->first(), 422);
            }

            $user = Auth::guard('user-api')->user();

            if (Hash::check($request->current_password, $user->password)) {

                $user->update(['password' => Hash::make($request->new_password)]);

                if (isset($user)) {

                    $user->token = $request->bearerToken();
                    return self::returnResponseDataApi(new UserResource($user), "تم تغيير كلمه المرور بنجاح", 200);

                } else {

                    return self::returnResponseDataApi(null, "يوجد مشكله اثناء تغيير كلمه المرور", 500, 500);
                }
            } else {

                return self::returnResponseDataApi(null, "كلمه المرور القديمه غير صحيحه برجاء كتابه كلمه السر صحيحه لمتابعه التغيير", 403, 403);

            }


        } catch (\Exception $exception) {

            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }

    }


    public function updateProfile(Request $request): JsonResponse
    {
        try {


            $rules = [
                'name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email,' . Auth::guard('user-api')->id(),
                'phone' => 'required|numeric',
                'image' => 'nullable|mimes:jpg,png,jpeg',
                'city_id' => 'required|exists:cities,id',

            ];

            $validator = Validator::make($request->all(), $rules, [
                'email.unique' => 406,
                'phone.numeric' => 407,
            ]);

            if ($validator->fails()) {
                $errors = collect($validator->errors())->flatten(1)[0];

                if (is_numeric($errors)) {
                    $errors_arr = [
                        406 => 'Failed,Email already exists',
                        407 => 'Failed,Phone number must be an number',
                    ];

                    $code = collect($validator->errors())->flatten(1)[0];
                    return self::returnResponseDataApi(null, $errors_arr[$errors] ?? 500, $code);

                }
                return self::returnResponseDataApi(null, $validator->errors()->first(), 422);
            }

            $user = Auth::guard('user-api')->user();

            if ($image = $request->file('image')) {

                $destinationPath = 'uploads/users/';
                $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $profileImage);
                $request['image'] = $profileImage;
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'image' => $request->file('image') != null ? 'uploads/users/' . $profileImage : $user->image,
                'city_id' => $request->city_id,
            ]);

            if (isset($user)) {

                $user->token = $request->bearerToken();
                return self::returnResponseDataApi(new UserResource($user), "تم تسجيل بيانات المستخدم بنجاح", 200);

            } else {

                return self::returnResponseDataApi(null, "يوجد خطاء ما اثناء  البيانات", 500, 500);

            }
        } catch (\Exception $exception) {

            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    }

    public function logout(): JsonResponse
    {

        try {
            Auth::guard('user-api')->logout();
            return self::returnResponseDataApi(null, "تم تسجيل الخروج بنجاح", 200);

        } catch (\Exception $exception) {

            return self::returnResponseDataApi(null, $exception->getMessage(), 500, 500);
        }
    }


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
    }


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
    }
}
