<?php

namespace App\Repository\Api\Driver;

use App\Http\Resources\DriverDocumentResource;
use App\Http\Resources\DriverResource;
use App\Interfaces\Api\Driver\DriverRepositoryInterface;
use App\Models\DriverDetails;
use App\Models\DriverDocuments;
use App\Models\User;
use App\Repository\ResponseApi;
use App\Traits\PhotoTrait;
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

    public function instantTrip(Request $request): JsonResponse
    {
        try {
            $rules = [

            ];
            return self::returnResponseDataApi(null, 777, false, 500);
        } catch (\Exception $exception) {
            return self::returnResponseDataApi($exception->getMessage(), 500, false, 500);
        }
    }
}
