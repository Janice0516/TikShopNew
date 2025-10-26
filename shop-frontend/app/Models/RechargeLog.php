<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RechargeLog extends Model
{
    protected $fillable = [
        'recharge_request_id',
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
     * 获取充值申请
     */
    public function rechargeRequest(): BelongsTo
    {
        return $this->belongsTo(RechargeRequest::class);
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
