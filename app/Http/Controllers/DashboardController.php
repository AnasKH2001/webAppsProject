<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ComplaintService;

class DashboardController extends Controller
{
    public function __construct(protected ComplaintService $adminService) {}

    public function index(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'admin') {
        return response()->json(['message' => 'Unauthorized'], 403);
        }

        $stats = $this->adminService->getGlobalStatistics();

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }


    public function export(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $csvData = $this->adminService->getStatsCsv();
        $fileName = 'system_report_' . now()->format('Y-m-d') . '.csv';

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$fileName\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
