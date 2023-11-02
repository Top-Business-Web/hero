<?php

namespace App\Repository;

use App\Models\DriverDocuments;
use Yajra\DataTables\DataTables;
use App\Interfaces\DriverDocumentInterface;

class DriverDocumentRepository implements DriverDocumentInterface
{
    public function index($request)
    {
        if ($request->ajax()) {
            $driver_document = DriverDocuments::query()->latest()->get();
            return DataTables::of($driver_document)
                ->addColumn('action', function ($driver_document) {
                    return '
                            <button type="button" data-id="' . $driver_document->id . '" class="btn btn-pill btn-success editBtn"><i class="fa fa-eye"></i></button>
                       ';
                })
                ->editColumn('driver_id', function ($driver_document) {
                    return $driver_document->drivers->name;
                })
                ->editColumn('status', function ($driver_document) {
                    if ($driver_document->status == 1)
                        return '<button class="btn btn-sm btn-success statusBtn" data-id="' . $driver_document->id . '">مقبول</button>';
                    else
                        return '<button class="btn btn-sm btn-danger statusBtn" data-id="' . $driver_document->id . '">غير مقبول</button>';
                })
                ->editColumn('agency_number', function ($driver_document) {
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="' . asset($driver_document->agency_number) . '">
                    ';
                })
                ->editColumn('bike_license', function ($driver_document) {
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="' . asset($driver_document->bike_license) . '">
                    ';
                })
                ->editColumn('id_card', function ($driver_document) {
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="' . asset($driver_document->id_card) . '">
                    ';
                })
                ->editColumn('house_card', function ($driver_document) {
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="' . asset($driver_document->id_card) . '">
                    ';
                })
                ->editColumn('bike_image', function ($driver_document) {
                    return '
                    <img alt="image" onclick="window.open(this.src)" class="avatar avatar-md rounded-circle" src="' . asset($driver_document->bike_image) . '">
                    ';
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('admin.driver_document.index');
        }
    }

    public function edit($driver_document)
    {
        return view('admin.driver_document.parts.edit', compact('driver_document'));
    }

    public function changeStatus($request)
    {
        $driver_document = DriverDocuments::findOrFail($request->id);

        ($driver_document->status == 1) ? $driver_document->status = 0 : $driver_document->status = 1;

        $driver_document->save();

        if ($driver_document->status == 1) {
            return response()->json('200');
        } else {
            return response()->json('201');
        }
    }
}
