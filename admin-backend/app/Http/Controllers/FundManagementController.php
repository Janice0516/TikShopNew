<?php

namespace App\Http\Controllers;

use App\Models\FundAccount;
use App\Models\FundTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FundManagementController extends Controller
{
    /**
     * 显示资金账户列表
     */
    public function index(Request $request)
    {
        $query = FundAccount::with(['owner']);

        // 搜索
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // 按账户类型筛选
        if ($request->filled('account_type')) {
            $query->accountType($request->account_type);
        }

        // 按状态筛选
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // 按所有者筛选
        if ($request->filled('owner_id')) {
            $query->owner($request->owner_id);
        }

        // 排序
        $sortBy = $request->get('sort_by', 'balance');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $fundAccounts = $query->paginate(20)->withQueryString();

        // 获取筛选选项
        $users = User::whereIn('type', ['merchant', 'customer'])->get();

        // 获取统计信息
        $stats = [
            'total_accounts' => FundAccount::count(),
            'platform_accounts' => FundAccount::accountType('platform')->count(),
            'merchant_accounts' => FundAccount::accountType('merchant')->count(),
            'customer_accounts' => FundAccount::accountType('customer')->count(),
            'total_balance' => FundAccount::sum('balance'),
            'total_frozen_amount' => FundAccount::sum('frozen_amount'),
            'total_available_balance' => FundAccount::sum('available_balance'),
            'active_accounts' => FundAccount::status('active')->count(),
        ];

        return view('admin.fund-management.index', compact('fundAccounts', 'users', 'stats'));
    }

    /**
     * 显示资金账户详情
     */
    public function show(FundAccount $fundAccount)
    {
        $fundAccount->load(['owner']);
        
        // 获取最近交易
        $recentTransactions = $fundAccount->transactions()
            ->with(['fromAccount', 'toAccount', 'operator'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // 获取交易统计
        $transactionStats = [
            'total_transactions' => $fundAccount->transactions->count(),
            'completed_transactions' => $fundAccount->transactions->where('status', 'completed')->count(),
            'pending_transactions' => $fundAccount->transactions->where('status', 'pending')->count(),
            'total_incoming' => $fundAccount->incomingTransactions->where('status', 'completed')->sum('amount'),
            'total_outgoing' => $fundAccount->outgoingTransactions->where('status', 'completed')->sum('amount'),
        ];

        return view('admin.fund-management.show', compact('fundAccount', 'recentTransactions', 'transactionStats'));
    }

    /**
     * 创建资金账户
     */
    public function create()
    {
        $users = User::whereIn('type', ['merchant', 'customer'])->get();
        return view('admin.fund-management.create', compact('users'));
    }

    /**
     * 存储资金账户
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|string|in:platform,merchant,customer,system',
            'owner_id' => 'nullable|exists:users,id',
            'owner_type' => 'nullable|string|in:merchant,customer,admin',
            'currency' => 'required|string|in:RM,USD,EUR',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['account_number'] = FundAccount::generateAccountNumber($request->account_type);
        $data['balance'] = 0;
        $data['frozen_amount'] = 0;
        $data['available_balance'] = 0;
        $data['status'] = 'active';

        FundAccount::create($data);

        return redirect()->route('admin.fund-management.index')
            ->with('success', '资金账户创建成功');
    }

    /**
     * 更新资金账户
     */
    public function update(Request $request, FundAccount $fundAccount)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'status' => 'required|string|in:active,frozen,closed',
            'description' => 'nullable|string',
        ]);

        $fundAccount->update($request->all());

        return redirect()->route('admin.fund-management.index')
            ->with('success', '资金账户已更新');
    }

    /**
     * 调整账户余额
     */
    public function adjustBalance(Request $request, FundAccount $fundAccount)
    {
        $request->validate([
            'adjustment_type' => 'required|string|in:add,deduct,freeze,unfreeze',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
        ]);

        $adminId = Auth::guard('admin')->id();
        $amount = $request->amount;

        try {
            DB::beginTransaction();

            switch ($request->adjustment_type) {
                case 'add':
                    $fundAccount->addBalance($amount);
                    $transactionType = 'adjustment_add';
                    break;
                case 'deduct':
                    if (!$fundAccount->deductBalance($amount)) {
                        throw new \Exception('账户余额不足');
                    }
                    $transactionType = 'adjustment_deduct';
                    break;
                case 'freeze':
                    if (!$fundAccount->freezeAmount($amount)) {
                        throw new \Exception('可用余额不足');
                    }
                    $transactionType = 'adjustment_freeze';
                    break;
                case 'unfreeze':
                    if (!$fundAccount->unfreezeAmount($amount)) {
                        throw new \Exception('冻结金额不足');
                    }
                    $transactionType = 'adjustment_unfreeze';
                    break;
            }

            // 创建交易记录
            FundTransaction::create([
                'transaction_number' => FundTransaction::generateTransactionNumber(),
                'to_account_id' => $request->adjustment_type === 'add' ? $fundAccount->id : null,
                'from_account_id' => $request->adjustment_type === 'deduct' ? $fundAccount->id : null,
                'amount' => $amount,
                'transaction_type' => $transactionType,
                'status' => 'completed',
                'currency' => $fundAccount->currency,
                'description' => $request->description,
                'operator_id' => $adminId,
                'operator_type' => 'admin',
                'processed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.fund-management.show', $fundAccount)
                ->with('success', '账户余额调整成功');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * 转账
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:fund_accounts,id',
            'to_account_id' => 'required|exists:fund_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
        ]);

        if ($request->from_account_id === $request->to_account_id) {
            return redirect()->back()
                ->with('error', '不能向自己转账');
        }

        $adminId = Auth::guard('admin')->id();

        try {
            DB::beginTransaction();

            // 创建交易记录
            $transaction = FundTransaction::create([
                'transaction_number' => FundTransaction::generateTransactionNumber(),
                'from_account_id' => $request->from_account_id,
                'to_account_id' => $request->to_account_id,
                'amount' => $request->amount,
                'transaction_type' => 'transfer',
                'status' => 'pending',
                'currency' => 'RM',
                'description' => $request->description,
                'operator_id' => $adminId,
                'operator_type' => 'admin',
            ]);

            // 执行交易
            if ($transaction->execute()) {
                DB::commit();
                return redirect()->route('admin.fund-management.index')
                    ->with('success', '转账成功');
            } else {
                throw new \Exception('转账失败');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * 获取资金统计
     */
    public function statistics(Request $request)
    {
        $query = FundAccount::query();

        $stats = [
            'total_accounts' => $query->count(),
            'platform_accounts' => $query->clone()->accountType('platform')->count(),
            'merchant_accounts' => $query->clone()->accountType('merchant')->count(),
            'customer_accounts' => $query->clone()->accountType('customer')->count(),
            'system_accounts' => $query->clone()->accountType('system')->count(),
            'active_accounts' => $query->clone()->status('active')->count(),
            'frozen_accounts' => $query->clone()->status('frozen')->count(),
            'closed_accounts' => $query->clone()->status('closed')->count(),
            'total_balance' => $query->clone()->sum('balance'),
            'total_frozen_amount' => $query->clone()->sum('frozen_amount'),
            'total_available_balance' => $query->clone()->sum('available_balance'),
            'avg_balance' => $query->clone()->avg('balance'),
            'max_balance' => $query->clone()->max('balance'),
            'min_balance' => $query->clone()->min('balance'),
        ];

        return response()->json($stats);
    }

    /**
     * 导出资金数据
     */
    public function export(Request $request)
    {
        $query = FundAccount::with(['owner']);

        // 应用筛选条件
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('account_type')) {
            $query->accountType($request->account_type);
        }
        if ($request->filled('status')) {
            $query->status($request->status);
        }
        if ($request->filled('owner_id')) {
            $query->owner($request->owner_id);
        }

        $fundAccounts = $query->orderBy('balance', 'desc')->get();

        // 这里可以集成Laravel Excel来导出数据
        // 暂时返回JSON格式
        return response()->json([
            'data' => $fundAccounts,
            'count' => $fundAccounts->count(),
        ]);
    }
}
