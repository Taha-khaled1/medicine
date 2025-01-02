<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\SettingWeb;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function createSale(Request $request)
    {
        try {
            // Check for open shift
            $openShift = Shift::where('status', 'open')->first();
            if (!$openShift) {
                return response()->json([
                    'message' => 'No open shift found. Please open a shift first',
                    'status_code' => 400
                ], 400);
            }

            $validated = $request->validate([
                'medicine_id' => 'required|exists:medicines,id',
                'box_count' => 'required|integer|min:0',
                'strip_count' => 'required|integer|min:0',
                'is_paid' => 'boolean',
                'total_price' => 'required|numeric|min:0'
            ]);

            $sale = Sale::create([
                'medicine_id' => $validated['medicine_id'],
                'shift_id' => $openShift->id,
                'box_count' => $validated['box_count'],
                'strip_count' => $validated['strip_count'],
                'total_price' => $validated['total_price'],
                'is_paid' => $validated['is_paid'] ?? true
            ]);
            // Medicine::where('id', $validated['medicine_id'])->decrement('quantity', $validated['box_count']);
            // Medicine::where('id', $validated['medicine_id'])->decrement('subunits_count', $validated['strip_count']);
            return response()->json([
                'message' => 'Sale created successfully',
                'status_code' => 200,
                'data' => $sale
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating sale',
                'status_code' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get all sales with medicine and shift details
     */
    public function getAllSales(Request $request)
    {
        try {
            $query = Sale::with(['medicine', 'shift']);

            // Add filters
            if ($request->has('date')) {
                $query->whereHas('shift', function ($q) use ($request) {
                    $q->whereDate('shift_date', $request->date);
                });
            }

            if ($request->has('is_paid')) {
                $query->where('is_paid', $request->is_paid);
            }

            if ($request->has('medicine_id')) {
                $query->where('medicine_id', $request->medicine_id);
            }

            // Add date range filter
            if ($request->has('from_date') && $request->has('to_date')) {
                $query->whereHas('shift', function ($q) use ($request) {
                    $q->whereBetween('shift_date', [$request->from_date, $request->to_date]);
                });
            }

            $sales = $query->orderBy('created_at', 'desc')
                ->paginate(15)
                ->map(function ($sale) {
                    return [
                        'id' => $sale->id,
                        'medicine' => [
                            'id' => $sale->medicine->id,
                            'name' => $sale->medicine->name,
                            'scientific_form' => $sale->medicine->scientific_form,
                            'type' => $sale->medicine->type,
                            'price' => $sale->medicine->price
                        ],
                        'shift' => [
                            'id' => $sale->shift->id,
                            'shift_date' => $sale->shift->shift_date,
                            'start_time' => $sale->shift->start_time,
                            'end_time' => $sale->shift->end_time,
                            'total_amount' => $sale->shift->total_amount,
                            'status' => $sale->shift->status
                        ],
                        'box_count' => $sale->box_count,
                        'strip_count' => $sale->strip_count,
                        'total_price' => $sale->total_price,
                        'is_paid' => $sale->is_paid,
                        'created_at' => $sale->created_at
                    ];
                });

            return response()->json([
                'message' => 'Sales retrieved successfully',
                'status_code' => 200,
                'data' => $sales,
                'summary' => [
                    'total_sales_count' => $sales->count(),
                    'total_amount' => $sales->sum('total_price'),
                    'paid_amount' => $sales->where('is_paid', true)->sum('total_price'),
                    'unpaid_amount' => $sales->where('is_paid', false)->sum('total_price'),
                    'total_boxes' => $sales->sum('box_count'),
                    'total_strips' => $sales->sum('strip_count')
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving sales',
                'status_code' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
