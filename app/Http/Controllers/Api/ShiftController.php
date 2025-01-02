<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SettingWeb;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


// app/Http/Controllers/ShiftController.php
class ShiftController extends Controller
{
    // Open a new shift
    public function openShift(Request $request)
    {
        try {
            // Check if there's already an open shift
            $openShift = Shift::where('status', 'open')->first();
            if ($openShift) {
                return response()->json([
                    'message' => 'There is already an open shift',
                    'status_code' => 400
                ], 400);
            }

            $validated = $request->validate([
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'initial_amount' => 'required|numeric|min:0'
            ]);

            $shift = Shift::create([
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'shift_date' => now(),
                'status' => 'open',
                'initial_amount' => $validated['initial_amount']
            ]);

            return response()->json([
                'message' => 'Shift opened successfully',
                'status_code' => 200,
                'data' => $shift
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error opening shift',
                'status_code' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Close the shift
    public function closeShift(Request $request)
    {
        try {
            $openShift = Shift::where('status', 'open')->first();
            if (!$openShift) {
                return response()->json([
                    'message' => 'No open shift found',
                    'status_code' => 400
                ], 400);
            }

            $validated = $request->validate([
                'remaining_amount' => 'required|numeric|min:0',
                'unpaid_amount' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'actual_amount' => 'required|numeric|min:0'
            ]);

            // Calculate unpaid amount from sales
            // $totalUnpaid = Sale::where('shift_id', $openShift->id)
            //     ->where('is_paid', false)
            //     ->sum('total_price');

            $openShift->update([
                'status' => 'closed',
                'remaining_amount' => $validated['remaining_amount'],
                'unpaid_amount' => $validated['unpaid_amount'],
                'total_amount' => $validated['total_amount'],
                'actual_amount' => $validated['actual_amount']
            ]);

            return response()->json([
                'message' => 'Shift closed successfully',
                'status_code' => 200,
                'data' => $openShift
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error closing shift',
                'status_code' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get all shifts with their sales and medicine details
     */
    public function getAllShifts(Request $request)
    {
        try {
            $query = Shift::with(['sales' => function ($query) {
                $query->with('medicine'); // Load medicine details for each sale
            }]);

            // Add filters if needed
            if ($request->has('date')) {
                $query->whereDate('shift_date', $request->date);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $shifts = $query->orderBy('created_at', 'desc')
                ->paginate(15)
                ->map(function ($shift) {
                    // Calculate totals for each shift
                    $totalSales = $shift->sales->sum('total_price');
                    $totalPaidSales = $shift->sales->where('is_paid', true)->sum('total_price');
                    $totalUnpaidSales = $shift->sales->where('is_paid', false)->sum('total_price');

                    return [
                        'id' => $shift->id,
                        'start_time' => $shift->start_time,
                        'end_time' => $shift->end_time,
                        'shift_date' => $shift->shift_date,
                        'status' => $shift->status,
                        'initial_amount' => $shift->initial_amount,
                        'remaining_amount' => $shift->remaining_amount,
                        'unpaid_amount' => $shift->unpaid_amount,
                        'actual_amount' => $shift->actual_amount,
                        'created_at' => $shift->created_at,
                        'total_sales' => $totalSales,
                        'total_paid_sales' => $totalPaidSales,
                        'total_unpaid_sales' => $totalUnpaidSales,
                        'total_amount' => $shift->total_amount,
                        'sales_count' => $shift->sales->count(),
                        'sales' => $shift->sales->map(function ($sale) {
                            return [
                                'id' => $sale->id,
                                'medicine' => [
                                    'id' => $sale->medicine->id,
                                    'name' => $sale->medicine->name,
                                    'scientific_form' => $sale->medicine->scientific_form,
                                    'type' => $sale->medicine->type
                                ],
                                'box_count' => $sale->box_count,
                                'strip_count' => $sale->strip_count,
                                'total_price' => $sale->total_price,
                                'is_paid' => $sale->is_paid,
                                'created_at' => $sale->created_at
                            ];
                        })
                    ];
                });

            return response()->json([
                'message' => 'Shifts retrieved successfully',
                'status_code' => 200,
                'data' => $shifts,
                'summary' => [
                    'total_shifts' => $shifts->count(),
                    'total_sales' => $shifts->sum('total_sales'),
                    'total_paid_sales' => $shifts->sum('total_paid_sales'),
                    'total_unpaid_sales' => $shifts->sum('total_unpaid_sales')
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving shifts',
                'status_code' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
