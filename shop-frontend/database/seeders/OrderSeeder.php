<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Merchant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchants = Merchant::with('user')->get();
        
        if ($merchants->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];
        
        // 创建一些客户用户
        $customers = [];
        for ($i = 1; $i <= 10; $i++) {
            $customers[] = \App\Models\User::firstOrCreate(
                ['email' => 'customer' . $i . '@example.com'],
                [
                    'name' => '客户' . $i,
                    'password' => bcrypt('password123'),
                    'type' => 'customer',
                ]
            );
        }

        // 为每个商家创建一些订单
        foreach ($merchants as $merchant) {
            $products = Product::where('merchant_id', $merchant->user_id)->get();
            
            if ($products->isEmpty()) {
                continue;
            }

            // 每个商家创建5-15个订单
            $orderCount = rand(5, 15);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $customer = $customers[array_rand($customers)];
                $status = $statuses[array_rand($statuses)];
                $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
                
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'merchant_id' => $merchant->user_id,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'customer_phone' => '012345678' . rand(0, 9),
                    'shipping_address' => '马来西亚吉隆坡市' . rand(1, 100) . '号',
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentStatus === 'paid' ? 'credit_card' : null,
                    'subtotal' => 0,
                    'shipping_fee' => rand(0, 20),
                    'tax_amount' => 0,
                    'discount_amount' => rand(0, 50),
                    'total_amount' => 0,
                    'currency' => 'MYR',
                    'notes' => rand(0, 1) ? '请尽快发货' : null,
                    'paid_at' => $paymentStatus === 'paid' ? now()->subDays(rand(1, 30)) : null,
                    'shipped_at' => in_array($status, ['shipped', 'delivered']) ? now()->subDays(rand(1, 20)) : null,
                    'delivered_at' => $status === 'delivered' ? now()->subDays(rand(1, 10)) : null,
                ]);

                // 为订单添加商品
                $itemCount = min(rand(1, 3), $products->count());
                $selectedProducts = $products->random($itemCount);
                $subtotal = 0;

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 3);
                    $unitPrice = $product->price;
                    $totalPrice = $unitPrice * $quantity;
                    $subtotal += $totalPrice;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'product_snapshot' => [
                            'name' => $product->name,
                            'sku' => $product->sku,
                            'price' => $product->price,
                            'images' => $product->images,
                        ],
                    ]);
                }

                // 更新订单总金额
                $totalAmount = $subtotal + $order->shipping_fee - $order->discount_amount;
                $order->update([
                    'subtotal' => $subtotal,
                    'total_amount' => max(0, $totalAmount),
                ]);
            }
        }
    }
}
