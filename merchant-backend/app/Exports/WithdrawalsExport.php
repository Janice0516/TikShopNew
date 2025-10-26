<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Withdrawal;

class WithdrawalsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $merchantId;
    protected $filters;

    public function __construct($merchantId, $filters = [])
    {
        $this->merchantId = $merchantId;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Withdrawal::forMerchant($this->merchantId);
        
        // 应用筛选条件
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }
        
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }
        
        if (!empty($this->filters['status'])) {
            $query->byStatus($this->filters['status']);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            '提现单号',
            '提现金额',
            '手续费',
            '实际到账',
            '提现方式',
            '收款人',
            '账号信息',
            '状态',
            '处理时间',
            '完成时间',
            '备注',
            '申请时间',
        ];
    }

    public function map($withdrawal): array
    {
        $paymentInfo = $withdrawal->payment_info;
        
        return [
            $withdrawal->withdrawal_number,
            'RM' . number_format($withdrawal->amount, 2),
            'RM' . number_format($withdrawal->fee, 2),
            'RM' . number_format($withdrawal->actual_amount, 2),
            $withdrawal->method_text,
            $paymentInfo['account_name'] ?? '',
            $paymentInfo['account_number'] ?? '',
            $withdrawal->status_text,
            $withdrawal->processed_at ? $withdrawal->processed_at->format('Y-m-d H:i:s') : '',
            $withdrawal->completed_at ? $withdrawal->completed_at->format('Y-m-d H:i:s') : '',
            $withdrawal->notes ?? '',
            $withdrawal->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // 提现单号
            'B' => 12, // 提现金额
            'C' => 10, // 手续费
            'D' => 12, // 实际到账
            'E' => 15, // 提现方式
            'F' => 15, // 收款人
            'G' => 20, // 账号信息
            'H' => 12, // 状态
            'I' => 20, // 处理时间
            'J' => 20, // 完成时间
            'K' => 30, // 备注
            'L' => 20, // 申请时间
        ];
    }
}
