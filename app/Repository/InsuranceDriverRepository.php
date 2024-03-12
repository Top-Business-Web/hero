<?php

namespace App\Repository;

use App\Interfaces\InsuranceDriverInterface;
use App\Models\InsuranceDriver;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class InsuranceDriverRepository implements InsuranceDriverInterface
{

    public function index($request)
    {
        if ($request->ajax()) {
            $insurance_drivers = InsuranceDriver::query()->latest()->get();
            return DataTables::of($insurance_drivers)
                ->editColumn('driver_id', function ($insurance_drivers) {
                    return $insurance_drivers->driver->name;
                })
                ->editColumn('from', function ($insurance_drivers) {
                    $start = Carbon::parse($insurance_drivers->from);
                    $end = Carbon::parse($insurance_drivers->to);

                    $diffInDays = $start->diffInDays($end);

                    return $diffInDays . " " . 'أيام';
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('admin.insurance_drivers.index');
        }
    }
}
