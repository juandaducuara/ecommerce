<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Infrastructure\Persistence\Models\OrderModel;
use App\Infrastructure\Persistence\Models\ProductModel;
use App\Infrastructure\Persistence\Models\RoleModel;
use App\Infrastructure\Persistence\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $orderModel   = new OrderModel();
        $productModel = new ProductModel();
        $userModel    = new UserModel();

        // Conteos generales
        $totalOrders   = $orderModel->countAllResults();
        $totalProducts = $productModel->countAllResults();

        $customerRole = (new RoleModel())->findBySlug('customer');
        $totalUsers   = $customerRole
            ? $userModel->where('role_id', $customerRole->id)->countAllResults()
            : $userModel->countAllResults();

        // Ingresos del mes: órdenes pagadas (cubre tarjeta, PSE y contraentrega)
        $revenueRow = $orderModel
            ->selectSum('total', 'amount')
            ->where('payment_status', OrderModel::PAYMENT_PAID)
            ->where('orders.created_at >=', date('Y-m-01 00:00:00'))
            ->first();
        $monthRevenue = $revenueRow->amount ?? 0;

        // Pedidos recientes
        $recentOrders = $orderModel
            ->select('orders.*, users.first_name, users.last_name')
            ->join('users', 'users.id = orders.user_id', 'left')
            ->orderBy('orders.created_at', 'DESC')
            ->findAll(8);

        // Stats por estado
        $pendingOrders    = $orderModel->where('status', OrderModel::STATUS_PENDING)->countAllResults();
        $processingOrders = $orderModel->where('status', OrderModel::STATUS_PROCESSING)->countAllResults();
        $shippedOrders    = $orderModel->where('status', OrderModel::STATUS_SHIPPED)->countAllResults();

        return view('admin/dashboard', [
            'title'            => 'Dashboard',
            'totalOrders'      => $totalOrders,
            'totalProducts'    => $totalProducts,
            'totalUsers'       => $totalUsers,
            'monthRevenue'     => $monthRevenue,
            'recentOrders'     => $recentOrders,
            'pendingOrders'    => $pendingOrders,
            'processingOrders' => $processingOrders,
            'shippedOrders'    => $shippedOrders,
        ]);
    }
}
