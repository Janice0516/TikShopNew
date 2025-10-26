<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FundAccount extends Model
{
    protected $fillable = [
        'account_name',
        'account_type',
        'account_number',
        'owner_id',
        'owner_type',
        'balance',
        'frozen_amount',
        'available_balance',
        'currency',
        'status',
        'description',
        'settings',
        'last_transaction_at',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'frozen_amount' => 'decimal:2',
            'available_balance' => 'decimal:2',
            'settings' => 'array',
            'last_transaction_at' => 'datetime',
        ];
    }

    /**
     * 获取账户所有者
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * 获取转出交易
     */
    public function outgoingTransactions(): HasMany
    {
        return $this->hasMany(FundTransaction::class, 'from_account_id');
    }

    /**
     * 获取转入交易
     */
    public function incomingTransactions(): HasMany
    {
        return $this->hasMany(FundTransaction::class, 'to_account_id');
    }

    /**
     * 获取所有交易
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(FundTransaction::class, 'from_account_id')
                   ->orWhere('to_account_id', $this->id);
    }

    /**
     * 作用域：按账户类型筛选
     */
    public function scopeAccountType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    /**
     * 作用域：按状态筛选
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：按所有者筛选
     */
    public function scopeOwner($query, $ownerId, $ownerType = null)
    {
        $query->where('owner_id', $ownerId);
        if ($ownerType) {
            $query->where('owner_type', $ownerType);
        }
        return $query;
    }

    /**
     * 作用域：搜索
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('account_name', 'like', "%{$keyword}%")
              ->orWhere('account_number', 'like', "%{$keyword}%")
              ->orWhereHas('owner', function ($ownerQuery) use ($keyword) {
                  $ownerQuery->where('name', 'like', "%{$keyword}%")
                           ->orWhere('email', 'like', "%{$keyword}%");
              });
        });
    }

    /**
     * 生成账户号码
     */
    public static function generateAccountNumber($type = 'platform'): string
    {
        $prefix = match($type) {
            'platform' => 'PLT',
            'merchant' => 'MCH',
            'customer' => 'CUS',
            'system' => 'SYS',
            default => 'ACC',
        };

        do {
            $number = $prefix . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('account_number', $number)->exists());

        return $number;
    }

    /**
     * 更新可用余额
     */
    public function updateAvailableBalance()
    {
        $this->available_balance = $this->balance - $this->frozen_amount;
        $this->save();
    }

    /**
     * 冻结资金
     */
    public function freezeAmount($amount)
    {
        if ($this->available_balance < $amount) {
            return false;
        }

        $this->frozen_amount += $amount;
        $this->updateAvailableBalance();
        return true;
    }

    /**
     * 解冻资金
     */
    public function unfreezeAmount($amount)
    {
        if ($this->frozen_amount < $amount) {
            return false;
        }

        $this->frozen_amount -= $amount;
        $this->updateAvailableBalance();
        return true;
    }

    /**
     * 增加余额
     */
    public function addBalance($amount)
    {
        $this->balance += $amount;
        $this->updateAvailableBalance();
        $this->last_transaction_at = now();
        $this->save();
    }

    /**
     * 减少余额
     */
    public function deductBalance($amount)
    {
        if ($this->available_balance < $amount) {
            return false;
        }

        $this->balance -= $amount;
        $this->updateAvailableBalance();
        $this->last_transaction_at = now();
        $this->save();
        return true;
    }

    /**
     * 获取账户类型标签
     */
    public function getAccountTypeLabelAttribute(): string
    {
        return match($this->account_type) {
            'platform' => '平台账户',
            'merchant' => '商家账户',
            'customer' => '客户账户',
            'system' => '系统账户',
            default => '未知',
        };
    }

    /**
     * 获取状态标签
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => '正常',
            'frozen' => '冻结',
            'closed' => '关闭',
            default => '未知',
        };
    }

    /**
     * 获取状态颜色
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'frozen' => 'yellow',
            'closed' => 'red',
            default => 'gray',
        };
    }

    /**
     * 获取或创建商家的资金账户
     */
    public static function getOrCreateForMerchant($merchantId): self
    {
        return self::firstOrCreate(
            [
                'owner_id' => $merchantId,
                'owner_type' => 'merchant',
                'account_type' => 'merchant',
            ],
            [
                'account_name' => '商家资金账户',
                'account_number' => self::generateAccountNumber('merchant'),
                'balance' => 0,
                'frozen_amount' => 0,
                'available_balance' => 0,
                'currency' => 'RM',
                'status' => 'active',
            ]
        );
    }

    /**
     * 获取或创建客户资金账户
     */
    public static function getOrCreateForCustomer($customerId): self
    {
        return self::firstOrCreate(
            [
                'owner_id' => $customerId,
                'owner_type' => 'customer',
                'account_type' => 'customer',
            ],
            [
                'account_name' => '客户资金账户',
                'account_number' => self::generateAccountNumber('customer'),
                'balance' => 0,
                'frozen_amount' => 0,
                'available_balance' => 0,
                'currency' => 'RM',
                'status' => 'active',
            ]
        );
    }
}
