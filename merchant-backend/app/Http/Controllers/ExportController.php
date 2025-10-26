<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;
use App\Exports\ProductsExport;
use App\Exports\FinanceExport;
use App\Exports\WithdrawalsExport;

class ExportController extends Controller
{
    /**
     * 导出订单数据
     */
    public function exportOrders(Request $request)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return response()->json([
                    'success' => false,
                    'message' => '请先登录'
                ], 401);
            }

            $request->validate([
                'format' => 'required|in:xlsx,csv,pdf',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'status' => 'nullable|string',
            ]);

            $filename = 'orders_' . now()->format('Y-m-d_H-i-s') . '.' . $request->input('format');
            
            return Excel::download(new OrdersExport($merchantId, $request->all()), $filename);
        } catch (\Throwable $e) {
            \Log::error('Export orders error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'message' => '导出失败: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 导出商品数据
     */
    public function exportProducts(Request $request)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return response()->json([
                    'success' => false,
                    'message' => '请先登录'
                ], 401);
            }

            $request->validate([
                'format' => 'required|in:xlsx,csv,pdf',
                'status' => 'nullable|string',
                'category' => 'nullable|string',
            ]);

            $filename = 'products_' . now()->format('Y-m-d_H-i-s') . '.' . $request->input('format');
            
            return Excel::download(new ProductsExport($merchantId, $request->all()), $filename);
        } catch (\Throwable $e) {
            \Log::error('Export products error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'message' => '导出失败: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 导出财务数据
     */
    public function exportFinance(Request $request)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return response()->json([
                    'success' => false,
                    'message' => '请先登录'
                ], 401);
            }

            $request->validate([
                'format' => 'required|in:xlsx,csv,pdf',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'type' => 'nullable|string',
            ]);

            $filename = 'finance_' . now()->format('Y-m-d_H-i-s') . '.' . $request->input('format');
            
            return Excel::download(new FinanceExport($merchantId, $request->all()), $filename);
        } catch (\Throwable $e) {
            \Log::error('Export finance error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'message' => '导出失败: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 导出提现数据
     */
    public function exportWithdrawals(Request $request)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return response()->json([
                    'success' => false,
                    'message' => '请先登录'
                ], 401);
            }

            $request->validate([
                'format' => 'required|in:xlsx,csv,pdf',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'status' => 'nullable|string',
            ]);

            $filename = 'withdrawals_' . now()->format('Y-m-d_H-i-s') . '.' . $request->input('format');
            
            return Excel::download(new WithdrawalsExport($merchantId, $request->all()), $filename);
        } catch (\Throwable $e) {
            \Log::error('Export withdrawals error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'message' => '导出失败: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 导出综合报表
     */
    public function exportReport(Request $request)
    {
        try {
            $merchantId = session('merchant_info.id');
            if (!$merchantId) {
                return response()->json([
                    'success' => false,
                    'message' => '请先登录'
                ], 401);
            }

            $request->validate([
                'format' => 'required|in:xlsx,csv,pdf',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'include_orders' => 'nullable|boolean',
                'include_products' => 'nullable|boolean',
                'include_finance' => 'nullable|boolean',
                'include_withdrawals' => 'nullable|boolean',
            ]);

            $filename = 'merchant_report_' . now()->format('Y-m-d_H-i-s') . '.' . $request->input('format');
            
            // 这里可以实现综合报表导出
            // 暂时返回订单导出作为示例
            return Excel::download(new OrdersExport($merchantId, $request->all()), $filename);
        } catch (\Throwable $e) {
            \Log::error('Export report error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'message' => '导出失败: ' . $e->getMessage()
            ], 500);
        }
    }
}