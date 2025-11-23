<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GovernmentEntityService;

class GovernmentEntityController extends Controller
{
    protected GovernmentEntityService $service;

    public function __construct(GovernmentEntityService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json($this->service->getAllEntities());
    }
}
