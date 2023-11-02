<?php

namespace App\Repository;

use Yajra\DataTables\DataTables;
use App\Interfaces\TripInterface;
use App\Models\Trip;
use Symfony\Component\HttpFoundation\JsonResponse;

class TripRepository implements TripInterface
{
    public function complete($request)
    {
        if ($request->ajax()) {
            $trip_completed = Trip::query()->where('type', '=', 'complete')->latest()->get();
            return DataTables::of($trip_completed)
                ->addColumn('action', function ($trip_completed) {
                    return '
                            <button type="button" data-id="' . $trip_completed->id . '" class="btn btn-pill btn-success editBtn"><i class="fa fa-eye"></i></button>
                            <button class="btn btn-pill btn-danger-light" data-toggle="modal" data-target="#delete_modal"
                                    data-id="' . $trip_completed->id . '" data-title="' . $trip_completed->name . '">
                                    <i class="fas fa-trash"></i>
                            </button>
                       ';
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('admin.trips.complete');
        }
    }

    public function showCompleteTrip($trip)
    {
        $trip = Trip::findOrFail($trip);
        return view('admin.trips.parts.showCompleteTrip', compact('trip'));
    }

    public function new($request)
    {
        if ($request->ajax()) {
            $trip_completed = Trip::query()->where('type', '=', 'new')->latest()->get();
            return DataTables::of($trip_completed)
                ->addColumn('action', function ($trip_completed) {
                    return '
                    <button type="button" data-id="' . $trip_completed->id . '" class="btn btn-pill btn-success editBtn"><i class="fa fa-eye"></i></button>
                            <button class="btn btn-pill btn-danger-light" data-toggle="modal" data-target="#delete_modal"
                                    data-id="' . $trip_completed->id . '" data-title="' . $trip_completed->name . '">
                                    <i class="fas fa-trash"></i>
                            </button>
                       ';
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('admin.trips.new');
        }
    }

    public function showNewTrip($trip)
    {
        $trip = Trip::findOrFail($trip);
        return view('admin.trips.parts.showNewTrip', compact('trip'));
    }

    public function reject($request)
    {
        if ($request->ajax()) {
            $trip_completed = Trip::query()->where('type', '=', 'reject')->latest()->get();
            return DataTables::of($trip_completed)
                ->addColumn('action', function ($trip_completed) {
                    return '
                            <button type="button" data-id="' . $trip_completed->id . '" class="btn btn-pill btn-info-light editBtn"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-pill btn-danger-light" data-toggle="modal" data-target="#delete_modal"
                                    data-id="' . $trip_completed->id . '" data-title="' . $trip_completed->name . '">
                                    <i class="fas fa-trash"></i>
                            </button>
                       ';
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('admin.trips.reject');
        }
    }

    public function delete($request)
    {
        $trip = Trip::query()->where('id', $request->id)->first();
        $trip->delete();
        return response(['message' => 'تم الحذف بنجاح', 'status' => 200], 200);
    }
}