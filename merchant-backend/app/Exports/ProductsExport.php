<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\MerchantProduct;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
        $query = MerchantProduct::where('merchant_id', $this->merchantId)->with('platformProduct');
        
        // 应用筛选条件
        if (!empty($this->filters['status'])) {
            $query->byStatus($this->filters['status']);
        }
        
        if (!empty($this->filters['category'])) {
            $query->byCategory($this->filters['category']);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            '商品名称',
            'SKU',
            '分类',
            '品牌',
            '成本价',
            '售价',
            '利润率',
            '库存数量',
            '状态',
            '销量',
            '评分',
            '创建时间',
            '更新时间',
        ];
    }

    public function map($product): array
    {
        $platformProduct = $product->platformProduct;
        $profitMargin = $product->sale_price > 0 ? (($product->sale_price - $product->cost_price) / $product->sale_price * 100) : 0;
        
        return [
            $product->name,
            $product->sku,
            $platformProduct->category ?? '',
            $platformProduct->brand ?? '',
            'RM' . number_format($product->cost_price, 2),
            'RM' . number_format($product->sale_price, 2),
            number_format($profitMargin, 1) . '%',
            $product->stock_quantity,
            $product->is_active ? '在售' : '下架',
            $platformProduct->sales ?? 0,
            $platformProduct->rating ?? 0,
            $product->created_at->format('Y-m-d H:i:s'),
            $product->updated_at->format('Y-m-d H:i:s'),
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
            'A' => 30, // 商品名称
            'B' => 20, // SKU
            'C' => 15, // 分类
            'D' => 15, // 品牌
            'E' => 12, // 成本价
            'F' => 12, // 售价
            'G' => 10, // 利润率
            'H' => 10, // 库存数量
            'I' => 10, // 状态
            'J' => 10, // 销量
            'K' => 10, // 评分
            'L' => 20, // 创建时间
            'M' => 20, // 更新时间
        ];
    }
}
