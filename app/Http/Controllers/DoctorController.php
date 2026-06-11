<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DoctorsImport;

class DoctorController extends Controller
{
    public function index(): JsonResponse
    {
        $doctors = Excel::toCollection(new DoctorsImport    , storage_path('app/doctors/Name-list-updated.xlsx'))
            ->first()
            ->skip(0) // skip if there's a header row handled by import
            ->map(fn($row) => [
                'first_name'  => $row['first_name'] ?? null,
                'middle_name' => $row['middle_name'] ?? null,
                'last_name'   => $row['last_name'] ?? null,
                'nmc_no'      => $row['nmc_no'] ? (int) $row['nmc_no'] : null,
                'memberType' => $row['membertype'] ?? null,
            ])
            ->values();

        return response()->json([
            'success' => true,
            'total'   => $doctors->count(),
            'data'    => $doctors,
        ]);
    }
}