<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Infrastructure\Persistence\Models\OrderItemModel;
use App\Infrastructure\Persistence\Models\OrderModel;
use App\Infrastructure\Persistence\Models\PaymentModel;

class OrderController extends BaseController
{
    private OrderModel $orderModel;
    private OrderItemModel $orderItemModel;
    private PaymentModel $paymentModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->paymentModel   = new PaymentModel();
    }

    public function index(): string
    {
        $userId = $this->session->get('user_id');
        $orders = $this->orderModel->getByUser($userId);

        return view('web/orders/index', compact('orders'));
    }

    public function show(string $orderNumber): string
    {
        $userId = $this->session->get('user_id');
        $order  = $this->orderModel->findByOrderNumber($orderNumber);

        if (!$order || $order->user_id != $userId) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $items    = $this->orderItemModel->getByOrder($order->id);
        $payments = $this->paymentModel->getByOrder($order->id);

        return view('web/orders/show', compact('order', 'items', 'payments'));
    }
}
