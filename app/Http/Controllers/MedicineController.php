<?php

// app/Http/Controllers/MedicineController.php
namespace App\Http\Controllers;

use App\Imports\MedicinesImport;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MedicineController extends Controller
{
    // Create new medicine
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'expiry_date' => 'required|date',
            'type' => 'required|string|max:255',
            'subunits_per_unit' => 'required|integer|min:1',
            'subunits_count' => 'required|integer|min:0',
            'scientific_form' => 'required|string|max:255'
        ]);

        try {
            $medicine = Medicine::create($validated);
            return response()->json([
                'message' => 'Medicine created successfully',
                "status_code" => 200,

                'data' => $medicine
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating medicine',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update existing medicine
    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'quantity' => 'sometimes|integer|min:0',
            'expiry_date' => 'sometimes|date',
            'type' => 'sometimes|in:box,strip,piece',
            'subunits_per_unit' => 'sometimes|integer|min:1',
            'subunits_count' => 'sometimes|integer|min:0',
            'scientific_form' => 'sometimes|string|max:255'
        ]);

        try {
            $medicine->update($validated);
            return response()->json([
                'message' => 'Medicine updated successfully',
                "status_code" => 200,
                'data' => $medicine
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating medicine',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllMedicines(Request $request)
    {
        try {
            $query = Medicine::query();

            // Search by name
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('scientific_form', 'LIKE', "%{$searchTerm}%");
            }

            // Filter by type (box, strip, piece)
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Filter by price range
            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Filter by quantity range
            if ($request->has('min_quantity')) {
                $query->where('quantity', '>=', $request->min_quantity);
            }
            if ($request->has('max_quantity')) {
                $query->where('quantity', '<=', $request->max_quantity);
            }

            // Filter by expiry date range
            if ($request->has('expiry_from')) {
                $query->where('expiry_date', '>=', $request->expiry_from);
            }
            if ($request->has('expiry_to')) {
                $query->where('expiry_date', '<=', $request->expiry_to);
            }

            // Filter by stock status
            if ($request->has('stock_status')) {
                switch ($request->stock_status) {
                    case 'in_stock':
                        $query->where('quantity', '>', 0);
                        break;
                    case 'out_of_stock':
                        $query->where('quantity', '=', 0);
                        break;
                    case 'low_stock':
                        $query->where('quantity', '<=', 10)->where('quantity', '>', 0);
                        break;
                }
            }

            // Filter by expiry status
            if ($request->has('expiry_status')) {
                $today = now();
                switch ($request->expiry_status) {
                    case 'expired':
                        $query->where('expiry_date', '<', $today);
                        break;
                    case 'expiring_soon':
                        $query->whereBetween('expiry_date', [
                            $today,
                            $today->copy()->addMonths(6)
                        ]);
                        break;
                    case 'valid':
                        $query->where('expiry_date', '>', $today);
                        break;
                }
            }

            // Sorting
            $sortField = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');

            // Validate sort field to prevent SQL injection
            $allowedSortFields = [
                'name',
                'price',
                'quantity',
                'expiry_date',
                'created_at'
            ];

            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
            }

            // Pagination
            $perPage = $request->input('per_page', 10);
            $medicines = $query->paginate($perPage);

            // Calculate statistics
            $statistics = [
                'total_medicines' => Medicine::count(),
                'total_value' => Medicine::sum(DB::raw('price * quantity')),
                'average_price' => Medicine::avg('price'),
                'out_of_stock_count' => Medicine::where('quantity', 0)->count(),
                'expired_count' => Medicine::where('expiry_date', '<', now())->count(),
                'expiring_soon_count' => Medicine::whereBetween('expiry_date', [
                    now(),
                    now()->addMonths(3)
                ])->count(),
            ];

            return response()->json([
                'message' => 'Medicines retrieved successfully',
                'status_code' => 200,
                'data' => $medicines,
                'statistics' => $statistics,
                'filters_applied' => array_filter([
                    'search' => $request->search,
                    'type' => $request->type,
                    'price_range' => $request->has('min_price') || $request->has('max_price') ? [
                        'min' => $request->min_price,
                        'max' => $request->max_price
                    ] : null,
                    'quantity_range' => $request->has('min_quantity') || $request->has('max_quantity') ? [
                        'min' => $request->min_quantity,
                        'max' => $request->max_quantity
                    ] : null,
                    'expiry_range' => $request->has('expiry_from') || $request->has('expiry_to') ? [
                        'from' => $request->expiry_from,
                        'to' => $request->expiry_to
                    ] : null,
                    'stock_status' => $request->stock_status,
                    'expiry_status' => $request->expiry_status,
                    'sort_by' => $sortField,
                    'sort_direction' => $sortDirection
                ])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving medicines',
                'status_code' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // Get all medicines
    public function getShortcomings($quantity)
    {
        try {
            $medicines = Medicine::where('quantity', '=', $quantity)->get();
            return response()->json([
                'message' => 'Medicines retrieved successfully',
                "status_code" => 200,
                'data' => $medicines
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving medicines',
                "status_code" => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get expired medicines
    public function getExpiredMedicines()
    {
        try {
            $expiredMedicines = Medicine::where('expiry_date', '<', Carbon::now()->toDateString())->get();
            return response()->json([
                'message' => 'Medicines Expired retrieved successfully',
                "status_code" => 200,
                'data' => $expiredMedicines
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving expired medicines',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get medicines expiring soon (within 3 months)
    public function getMedicinesExpiringSoon()
    {
        try {
            $threeMothsFromNow = Carbon::now()->addMonths(6)->toDateString();
            $medicinesExpiringSoon = Medicine::whereBetween('expiry_date', [
                Carbon::now()->toDateString(),
                $threeMothsFromNow
            ])->get();

            return response()->json([
                'message' => 'Medicines expiring soon retrieved successfully',
                "status_code" => 200,
                'data' => $medicinesExpiringSoon
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving medicines expiring soon',
                'error' => $e->getMessage()
            ], 500);
        }
    }    // Get expired medicines
    public function deleteMedicine(Medicine $medicine)
    {
        try {
            $medicine->delete();
            return response()->json([
                'message' => 'Medicine deleted successfully',
                "status_code" => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting medicine',
                "status_code" => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv'
            ]);

            Excel::import(new MedicinesImport, $request->file('file'));

            return response()->json([
                'message' => 'Medicines imported successfully',
                'status_code' => 200
            ], 200);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'status_code' => 422,
                'errors' => $e->failures()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error importing medicines',
                'status_code' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
