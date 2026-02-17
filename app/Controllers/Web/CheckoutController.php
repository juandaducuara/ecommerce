<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Infrastructure\Persistence\Models\AddressModel;
use App\Infrastructure\Persistence\Models\CartItemModel;
use App\Infrastructure\Persistence\Models\CartModel;
use App\Infrastructure\Persistence\Models\CouponModel;
use App\Infrastructure\Persistence\Models\OrderItemModel;
use App\Infrastructure\Persistence\Models\OrderModel;
use App\Infrastructure\Persistence\Models\PaymentModel;
use App\Infrastructure\Persistence\Models\StockModel;

class CheckoutController extends BaseController
{
    private CartModel $cartModel;
    private CartItemModel $cartItemModel;
    private OrderModel $orderModel;
    private OrderItemModel $orderItemModel;
    private AddressModel $addressModel;
    private PaymentModel $paymentModel;
    private StockModel $stockModel;
    private CouponModel $couponModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->cartModel      = new CartModel();
        $this->cartItemModel  = new CartItemModel();
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->addressModel   = new AddressModel();
        $this->paymentModel   = new PaymentModel();
        $this->stockModel     = new StockModel();
        $this->couponModel    = new CouponModel();
    }

    public function index()
    {
        $userId = $this->session->get('user_id');
        $cart   = $this->cartModel->getOrCreateForUser($userId);
        $items  = $this->cartItemModel->getItemsWithProduct($cart->id);

        if (empty($items)) {
            return redirect()->to('/cart')->with('error', 'Tu carrito está vacío');
        }

        $savedAddresses = $this->addressModel->getByUserAndType($userId, 'shipping');
        $defaultAddress = $this->addressModel->getDefault($userId, 'shipping');

        return view('web/checkout/index', compact('cart', 'items', 'savedAddresses', 'defaultAddress'));
    }

    public function process()
    {
        $userId = $this->session->get('user_id');
        $cart   = $this->cartModel->getOrCreateForUser($userId);
        $items  = $this->cartItemModel->getItemsWithProduct($cart->id);

        if (empty($items)) {
            return redirect()->to('/cart')->with('error', 'Tu carrito está vacío');
        }

        $rules = [
            'first_name'      => 'required|min_length[2]',
            'last_name'       => 'required|min_length[2]',
            'phone'           => 'required|min_length[7]',
            'address_line_1'  => 'required|min_length[5]',
            'city'            => 'required',
            'state'           => 'required',
            'shipping_method' => 'required|in_list[standard,express,pickup]',
            'payment_method'  => 'required|in_list[card,cod,pse]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data     = $this->request->getPost();
        $subtotal = (float) ($cart->subtotal ?? 0);
        $discount = (float) ($cart->discount ?? 0);
        $tax      = (float) ($cart->tax ?? 0);

        // Shipping cost (free if subtotal >= 150,000)
        $shippingCost = 0;
        if ($data['shipping_method'] !== 'pickup' && $subtotal < 150000) {
            $shippingCost = $data['shipping_method'] === 'express' ? 15900 : 8900;
        }

        $total = $subtotal - $discount + $shippingCost;

        // Get coupon code before transaction (read-only)
        $couponCode = null;
        if ($cart->coupon_id) {
            $coupon     = $this->couponModel->find($cart->coupon_id);
            $couponCode = $coupon->code ?? null;
        }

        $orderNumber = $this->orderModel->generateOrderNumber();

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Save shipping address
            $addressId = $this->saveAddress($userId, $data);

            // Build shipping/billing data JSON
            $addressJson = json_encode([
                'first_name'     => $data['first_name'],
                'last_name'      => $data['last_name'],
                'phone'          => $data['phone'],
                'address_line_1' => $data['address_line_1'],
                'address_line_2' => $data['address_line_2'] ?? '',
                'city'           => $data['city'],
                'state'          => $data['state'],
                'postal_code'    => $data['postal_code'] ?? '',
                'country'        => 'CO',
            ]);

            // Create order
            $orderId = $this->orderModel->skipValidation(true)->insert([
                'order_number'        => $orderNumber,
                'user_id'             => $userId,
                'shipping_address_id' => $addressId,
                'billing_address_id'  => $addressId,
                'coupon_id'           => $cart->coupon_id ?: null,
                'coupon_code'         => $couponCode,
                'status'              => 'pending',
                'payment_status'      => 'pending',
                'subtotal'            => $subtotal,
                'discount'            => $discount,
                'tax'                 => $tax,
                'shipping_cost'       => $shippingCost,
                'total'               => $total,
                'currency'            => 'COP',
                'items_count'         => (int) ($cart->items_count ?? 0),
                'customer_email'      => $this->session->get('user_email'),
                'customer_phone'      => $data['phone'],
                'customer_notes'      => $data['notes'] ?? null,
                'shipping_data'       => $addressJson,
                'billing_data'        => $addressJson,
                'ip_address'          => $this->request->getIPAddress(),
            ]);

            if (!$orderId) {
                throw new \RuntimeException('No se pudo crear la orden');
            }

            // Create order items
            if (!$this->orderItemModel->createFromCart($orderId, $items)) {
                throw new \RuntimeException('No se pudieron guardar los items de la orden');
            }

            // Reserve stock
            foreach ($items as $item) {
                $this->stockModel->reserve($item->product_id, $item->quantity);
            }

            // Process payment
            if ($data['payment_method'] !== 'cod') {
                $paymentId = $this->paymentModel->createPending(
                    $orderId,
                    $total,
                    'payu',
                    $this->session->get('user_email')
                );
                $this->paymentModel->update($paymentId, ['method' => $data['payment_method']]);

                // Simulate approval (sandbox)
                $transactionId = 'SIM-' . strtoupper(bin2hex(random_bytes(6)));
                $this->paymentModel->approve($paymentId, $transactionId, [
                    'simulated' => true,
                    'method'    => $data['payment_method'],
                    'amount'    => $total,
                ]);

                // Mark order as confirmed + paid
                $this->orderModel->updatePaymentStatus($orderId, 'paid');

                // Confirm stock reservation (discount from physical inventory)
                foreach ($items as $item) {
                    $this->stockModel->confirmReservation($item->product_id, $item->quantity);
                }
            } else {
                // COD: processing status, payment still pending
                $this->orderModel->update($orderId, ['status' => 'processing']);
            }

            // Increment coupon usage
            if ($cart->coupon_id) {
                $this->couponModel->incrementUsage($cart->coupon_id);
            }

            // Clear cart
            $this->cartModel->clear($cart->id);

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[Checkout] Transacción fallida: ' . $e->getMessage());

            return redirect()->back()->withInput()
                ->with('error', 'Ocurrió un error al procesar tu orden. Por favor intenta de nuevo.');
        }

        return redirect()->to('/checkout/success/' . $orderNumber);
    }

    public function success(string $orderNumber): string
    {
        $userId = $this->session->get('user_id');
        $order  = $this->orderModel->findByOrderNumber($orderNumber);

        if (!$order || $order->user_id != $userId) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $items    = $this->orderItemModel->getByOrder($order->id);
        $payments = $this->paymentModel->getByOrder($order->id);

        return view('web/checkout/success', compact('order', 'items', 'payments'));
    }

    private function saveAddress(int $userId, array $data): ?int
    {
        // Mark existing shipping addresses as non-default
        $existing = $this->addressModel->getByUserAndType($userId, 'shipping');
        foreach ($existing as $addr) {
            $this->addressModel->update($addr->id, ['is_default' => 0]);
        }

        $id = $this->addressModel->skipValidation(true)->insert([
            'user_id'        => $userId,
            'type'           => 'shipping',
            'first_name'     => $data['first_name'],
            'last_name'      => $data['last_name'],
            'phone'          => $data['phone'],
            'address_line_1' => $data['address_line_1'],
            'address_line_2' => $data['address_line_2'] ?? null,
            'city'           => $data['city'],
            'state'          => $data['state'],
            'postal_code'    => $data['postal_code'] ?? null,
            'country'        => 'CO',
            'is_default'     => 1,
        ]);

        return $id ?: null;
    }
}
