<?php

namespace App\Repository\Api\User;

use App\Http\Resources\AreaResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\SettingResource;
use App\Http\Resources\UserResource;
use App\Interfaces\Api\User\UserRepositoryInterface;
use App\Models\Area;
use App\Models\City;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\Trip;
use App\Models\User;
use App\Repository\ResponseApi;
use App\Traits\PhotoTrait;
use Carbon\Carbon;
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
        if ($user->status == false){
            return self::returnResponseDataApi(['status' => $user->status], "انت الان خارج الخدمة", 200);
        }else {
            return self::returnResponseDataApi(['status' => $user->status], "انت الان في الخدمة", 200);
        }
    } // change status

    public function userHome(): JsonResponse
    {
        $home['sliders'] = Slider::query()
            ->select('image','link')
            ->where('status','=',true)
            ->get();
        $home['new_trip'] = Trip::query()
            ->where('type','=','new')
            ->whereDate('created_at','=',Carbon::now())
            ->orderBy('created_at','desc')
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
                'email' => 'required|email|unique:users,email,'.$user->id,
                'phone' => 'required|numeric|unique:users,phone,'.$user->id,
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
            }else {
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
    }
}
