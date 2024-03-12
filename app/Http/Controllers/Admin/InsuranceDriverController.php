<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\InsuranceDriverInterface;
use App\Models\InsuranceDriver;
use Illuminate\Http\Request;

class InsuranceDriverController extends Controller
{
    protected InsuranceDriverInterface $insuranceDriverInterface;

    public function __construct(InsuranceDriverInterface $insuranceDriverInterface)
    {
        $this->insuranceDriverInterface = $insuranceDriverInterface;
    }

    public function index(Request $request)
    {
        return $this->insuranceDriverInterface->index($request);

        
    }
}
