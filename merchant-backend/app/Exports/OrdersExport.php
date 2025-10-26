<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Order;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
        $query = Order::forMerchant($this->merchantId)->with('items');
        
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
            '订单号',
            '客户姓名',
            '客户电话',
            '客户邮箱',
            '订单状态',
            '支付状态',
            '商品数量',
            '订单金额',
            '运费',
            '税费',
            '优惠金额',
            '实付金额',
            '创建时间',
            '支付时间',
            '发货时间',
            '完成时间',
            '备注',
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->customer_name,
            $order->customer_phone ?? '',
            $order->customer_email ?? '',
            $order->status_text,
            $order->payment_status_text,
            $order->items->sum('quantity'),
            'RM' . number_format($order->subtotal, 2),
            'RM' . number_format($order->shipping_fee, 2),
            'RM' . number_format($order->tax_amount, 2),
            'RM' . number_format($order->discount_amount, 2),
            'RM' . number_format($order->total_amount, 2),
            $order->created_at->format('Y-m-d H:i:s'),
            $order->paid_at ? $order->paid_at->format('Y-m-d H:i:s') : '',
            $order->shipped_at ? $order->shipped_at->format('Y-m-d H:i:s') : '',
            $order->delivered_at ? $order->delivered_at->format('Y-m-d H:i:s') : '',
            $order->notes ?? '',
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
            'A' => 20, // 订单号
            'B' => 15, // 客户姓名
            'C' => 15, // 客户电话
            'D' => 25, // 客户邮箱
            'E' => 12, // 订单状态
            'F' => 12, // 支付状态
            'G' => 10, // 商品数量
            'H' => 12, // 订单金额
            'I' => 10, // 运费
            'J' => 10, // 税费
            'K' => 12, // 优惠金额
            'L' => 12, // 实付金额
            'M' => 20, // 创建时间
            'N' => 20, // 支付时间
            'O' => 20, // 发货时间
            'P' => 20, // 完成时间
            'Q' => 30, // 备注
        ];
    }
}
