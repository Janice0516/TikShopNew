<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalLog extends Model
{
    protected $fillable = [
        'withdrawal_id',
        'action',
        'old_status',
        'new_status',
        'description',
        'operator_id',
        'operator_type',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    /**
     * 获取提现记录
     */
    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(Withdrawal::class);
    }

    /**
     * 获取操作人
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'operator_id');
    }

    /**
     * 作用域：按操作类型筛选
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * 作用域：按操作人类型筛选
     */
    public function scopeOperatorType($query, $operatorType)
    {
        return $query->where('operator_type', $operatorType);
    }

    /**
     * 作用域：按日期范围筛选
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
