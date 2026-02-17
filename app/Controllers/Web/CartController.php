<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Infrastructure\Persistence\Models\CartModel;
use App\Infrastructure\Persistence\Models\CartItemModel;
use App\Infrastructure\Persistence\Models\CouponModel;
use App\Infrastructure\Persistence\Models\StockModel;

class CartController extends BaseController
{
    private CartModel $cartModel;
    private CartItemModel $cartItemModel;
    private CouponModel $couponModel;
    private StockModel $stockModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->cartModel     = new CartModel();
        $this->cartItemModel = new CartItemModel();
        $this->couponModel   = new CouponModel();
        $this->stockModel    = new StockModel();
    }

    private function getCurrentCart(): object
    {
        $userId = $this->session->get('user_id');
        return $userId
            ? $this->cartModel->getOrCreateForUser($userId)
            : $this->cartModel->getOrCreateForSession(session_id());
    }

    public function index(): string
    {
        $cart  = $this->getCurrentCart();
        $items = $this->cartItemModel->getItemsWithProduct($cart->id);
        return view('web/cart/index', compact('cart', 'items'));
    }

    public function add()
    {
        $productId = (int) $this->request->getPost('product_id');
        $quantity  = max(1, (int) ($this->request->getPost('quantity') ?? 1));

        $available = $this->stockModel->getAvailable($productId);
        if ($available < $quantity) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Solo hay {$available} unidades disponibles",
            ]);
        }

        $cart   = $this->getCurrentCart();
        $result = $this->cartItemModel->addItem($cart->id, $productId, $quantity);

        if (!$result) {
            return $this->response->setJSON(['success' => false, 'message' => 'Producto no disponible']);
        }

        $updated = $this->cartModel->find($cart->id);
        return $this->response->setJSON([
            'success'     => true,
            'message'     => '¡Producto agregado al carrito!',
            'items_count' => (int) $updated->items_count,
        ]);
    }

    public function update()
    {
        $itemId   = (int) $this->request->getPost('item_id');
        $quantity = (int) $this->request->getPost('quantity');

        $item = $this->cartItemModel->find($itemId);
        if (!$item) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item no encontrado']);
        }

        if ($quantity > 0) {
            $available = $this->stockModel->getAvailable($item->product_id);
            if ($available < $quantity) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Solo hay {$available} unidades disponibles",
                ]);
            }
        }

        $this->cartItemModel->updateQuantity($itemId, $quantity); // qty <= 0 removes item

        $cart    = $this->getCurrentCart();
        $updated = $this->cartModel->find($cart->id);

        return $this->response->setJSON([
            'success'     => true,
            'subtotal'    => (float) $updated->subtotal,
            'discount'    => (float) $updated->discount,
            'total'       => (float) $updated->total,
            'items_count' => (int) $updated->items_count,
        ]);
    }

    public function remove()
    {
        $itemId = (int) $this->request->getPost('item_id');
        $this->cartItemModel->removeItem($itemId);

        $cart    = $this->getCurrentCart();
        $updated = $this->cartModel->find($cart->id);

        return $this->response->setJSON([
            'success'     => true,
            'items_count' => (int) $updated->items_count,
        ]);
    }

    public function applyCoupon()
    {
        $code = strtoupper(trim($this->request->getPost('code') ?? ''));
        $cart = $this->getCurrentCart();

        $coupon = $this->couponModel->findByCode($code);
        if (!$coupon) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cupón no válido o inexistente']);
        }

        $isValid = $this->couponModel->isValid(
            $coupon->id,
            $this->session->get('user_id'),
            (float) ($cart->subtotal ?? 0)
        );

        if ($isValid !== true) {
            return $this->response->setJSON([
                'success' => false,
                'message' => is_string($isValid) ? $isValid : 'Cupón no válido',
            ]);
        }

        $discount = $this->couponModel->calculateDiscount($coupon->id, (float) ($cart->subtotal ?? 0));
        $this->cartModel->update($cart->id, ['coupon_id' => $coupon->id, 'discount' => $discount]);
        $updated = $this->cartModel->find($cart->id);

        return $this->response->setJSON([
            'success'  => true,
            'message'  => '¡Cupón aplicado! Descuento: $' . number_format($discount, 0, ',', '.'),
            'discount' => $discount,
            'cart'     => $updated,
        ]);
    }

    public function removeCoupon()
    {
        $cart = $this->getCurrentCart();
        $this->cartModel->update($cart->id, ['coupon_id' => null, 'discount' => 0]);
        $updated = $this->cartModel->find($cart->id);

        return $this->response->setJSON(['success' => true, 'cart' => $updated]);
    }
}
