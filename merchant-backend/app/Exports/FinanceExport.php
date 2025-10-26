<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\FinanceRecord;

class FinanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
        $query = FinanceRecord::forMerchant($this->merchantId);
        
        // 应用筛选条件
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }
        
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }
        
        if (!empty($this->filters['type'])) {
            $query->byType($this->filters['type']);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            '交易ID',
            '交易类型',
            '金额',
            '余额',
            '描述',
            '订单号',
            '提现单号',
            '状态',
            '备注',
            '创建时间',
        ];
    }

    public function map($record): array
    {
        return [
            $record->transaction_id,
            $record->type_text,
            'RM' . number_format($record->amount, 2),
            'RM' . number_format($record->balance_after, 2),
            $record->description,
            $record->order_id ? '#' . $record->order_id : '',
            $record->withdrawal_id ? '#' . $record->withdrawal_id : '',
            $record->status_text,
            $record->notes ?? '',
            $record->created_at->format('Y-m-d H:i:s'),
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
            'A' => 20, // 交易ID
            'B' => 15, // 交易类型
            'C' => 12, // 金额
            'D' => 12, // 余额
            'E' => 30, // 描述
            'F' => 15, // 订单号
            'G' => 15, // 提现单号
            'H' => 12, // 状态
            'I' => 30, // 备注
            'J' => 20, // 创建时间
        ];
    }
}
