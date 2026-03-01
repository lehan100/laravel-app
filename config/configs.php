<?php

use League\Flysystem\UrlGeneration\PublicUrlGenerator;

return [
    'prefix' => [
        'admin' => 'admin123',
        'frontend' => '/'
    ],
    'template' => [
        'form_label' => 'col-form-label col-md-3 col-sm-3 label-align font-weight-bold',
        //        'form_input_class' => 'col-md-9 col-sm-9',
        'form_input' => 'form-control',
    ],
    'messages' => [
        'update_status_success' => '<i class="fa fa-check mr-2"></i>Cập nhật phần tử thành công!',
        'update_cancel_success' => '<i class="d-none fa fa-check mr-2"></i>Hủy đơn hàng thành công!',
        'update_status_error' => '<i class="fa fa-close mr-2"></i>Cập nhật phần tử thất bại!',
        'update_cancel_error' => '<i class="d-none fa fa-close mr-2"></i>Hủy đơn hàng thất bại!',
        'delete_success' => '<i class="fa fa-check mr-2"></i>Xóa phần tử thành công!',
        'delete_error' => '<i class="fa fa-close mr-2"></i>Xóa phần tử thất bại!',
    ],
    'user_group' => [
        '1' => '<span class="badge badge-success">Aministrators</span>',
        '2' => '<span class="badge badge-primary">Admin</span>',
        '3' => '<span class="badge badge-warning">Makerting</span>',
        '4' => '<span class="badge badge-danger">Member</span>',
    ],
    'user_group_name' => [
        '1' => 'Aministrators',
        '2' => 'Admin',
        '3' => 'Makerting',
        '4' => 'Member',
    ],
    'mode' => [
        '1' => "Không quy định",
        '2' => "Thuộc danh mục"
    ],
    'dialog_messages' => [
        'delete' => ['title' => 'Xác nhận xóa', 'message' => 'Thông tin sẽ được xóa vĩnh viễn và không thể khôi phục.', 'class' => 'alert alert-warning', 'icon' => 'fa fa-exclamation-circle'],
    ],
    'active_status' => [
        '0' => ['name' => 'Tạm ẩn', 'class' => 'text-nowrap btn btn-danger btn-xs', 'icon' => 'fa fa-ban'],
        '1' => ['name' => 'Kích hoạt', 'class' => 'text-nowrap btn btn-success btn-xs', 'icon' => 'fa fa-check'],
        '2' => ['name' => 'Tạm ẩn', 'class' => 'text-nowrap btn btn-danger btn-xs', 'icon' => 'fa fa-ban'],
    ],
    'stock_status' => [
        '0' => ['name' => 'Hết hàng', 'class' => 'text-nowrap btn btn-danger btn-xs', 'icon' => 'fa fa-ban'],
        '1' => ['name' => 'Còn hàng', 'class' => 'text-nowrap btn btn-success btn-xs', 'icon' => 'fa fa-check'],
    ],
    'contact_status' => [
        '0' => ['name' => 'Chưa xem', 'class' => 'text-nowrap btn btn-danger btn-xs', 'icon' => 'fa fa-ban'],
        '1' => ['name' => 'Đã xem', 'class' => 'text-nowrap btn btn-success btn-xs', 'icon' => 'fa fa-check'],
        '2' => ['name' => 'Chưa xem', 'class' => 'text-nowrap btn btn-danger btn-xs', 'icon' => 'fa fa-ban'],
    ],
    'order_status' => [
        'awaiting' => ['code' => 'awaiting', 'comment' => 'Đơn hàng chưa xử lý', 'name' => 'Chưa xử lý', 'class' => 'order_status text-nowrap text-warning rounded', 'color' => '#ffc107'],
        'processed' => ['code' => 'processed', 'comment' => 'Đơn hàng đang xử lý', 'name' => 'Đang xử lý', 'class' => 'order_status text-nowrap text-info rounded', 'color' => '#17a2b8'],
        'success' => ['code' => 'success', 'comment' => 'Đơn hàng đã hoàn thành', 'name' => 'Đã hoàn thành', 'class' => 'order_status text-nowrap text-success rounded', 'color' => '#28a745'],
        'cancel' => ['code' => 'cancel', 'comment' => 'Đã hủy đơn hàng', 'name' => 'Đã hủy đơn', 'class' => 'order_status text-nowrap text-danger rounded', 'color' => '#dc3545'],
    ],
    'shipping_status' => [
        'awaiting' => ['code' => 'awaiting', 'comment' => 'Chưa giao hàng', 'name' => 'Chưa giao hàng', 'class' => 'order_status text-nowrap text-warning rounded', 'color' => '#ffc107'],
        'processed' => ['code' => 'processed', 'comment' => 'Đang giao hàng', 'name' => 'Đang giao hàng', 'class' => 'order_status text-nowrap text-info rounded', 'color' => '#17a2b8'],
        'success' => ['code' => 'success', 'comment' => 'Đã giao hàng', 'name' => 'Đã giao hàng', 'class' => 'order_status text-nowrap text-success rounded', 'color' => '#28a745'],
        'cancel' => ['code' => 'cancel', 'comment' => 'Đã hủy giao hàng', 'name' => 'Đã hủy', 'class' => 'order_status text-nowrap text-danger rounded', 'color' => '#dc3545'],
    ],
    'payment_status' => [
        'awaiting' => ['code' => 'awaiting', 'comment' => 'Đang chờ thanh toán', 'name' => 'Đang chờ thanh toán', 'class' => 'order_status text-nowrap text-warning rounded ml-3', 'color' => '#ffc107'],
        'success' => ['code' => 'success', 'comment' => 'Đã thanh toán', 'name' => 'Đã thanh toán', 'class' => 'order_status text-nowrap text-success rounded ml-3', 'color' => '#28a745'],
        'cancel' => ['code' => 'cancel', 'comment' => 'Đã hủy thanh toán', 'name' => 'Đã hủy thanh toán', 'class' => 'order_status text-nowrap text-danger rounded ml-3', 'color' => '#dc3545'],
        'cancel_refund' => ['code' => 'cancel_refund', 'comment' => 'Đã hủy thanh toán và hoàn tiền', 'name' => 'Hủy thanh toán và hoàn tiền', 'class' => 'order_status text-nowrap text-danger rounded ml-3', 'color' => '#dc3545'],
    ],
    'payment_method' => [
        'cash_on_delivery' => ['picture' => 'media/payments/cod.jpg', 'name' => 'COD', 'title' => 'Thanh toán khi nhận hàng'],
        'captureMoMoWallet' => ['picture' => 'media/payments/momo.png', 'name' => 'Ví MoMo', 'title' => 'Ví MoMo'],
        'payWithMoMoATM' => ['picture' => 'media/payments/atm.svg', 'name' => 'Thẻ ATM nội địa', 'title' => 'Thẻ ATM nội địa'],
    ],
    'location' => [
        'province' => ['' => '--- Please Select ---', '0' => 'Tỉnh', '1' => 'Thành phố'],
        'district' => ['' => '--- Please Select ---', '0' => 'Huyện', '1' => 'Quận', '3' => 'Thị xã', '4' => 'Thành phố'],
        'ward' => ['' => '--- Please Select ---', '0' => 'Xã', '1' => 'Phường', '2' => 'Thị trấn'],
        'active_status' => ['' => '--- Please Select ---', '0' => 'Tạm ẩn', '1' => 'Kích hoạt'],
        'stock_status' => ['' => '--- Please Select ---', '0' => 'Hết hàng', '1' => 'Còn hàng'],
        'payment_method' => [
            '' => '--- Please Select ---',
            'cash_on_delivery' => 'COD',
            'captureMoMoWallet' => 'Ví MoMo',
            'payWithMoMoATM' => 'Thẻ ATM nội địa'
        ],
        'order_status' => [
            '' => '--- Please Select ---',
            'awaiting' => 'Chưa xử lý',
            'processed' => 'Đang xử lý',
            'success' => 'Đã hoàn thành',
            'cancel' => 'Đã hủy đơn',
        ],
        'shipping_status' => [
            '' => '--- Please Select ---',
            'awaiting' => 'Chưa giao hàng',
            'processed' => 'Đang giao hàng',
            'success' => 'Đã giao hàng',
            'cancel' => 'Đã hủy',
        ],
        'payment_status' => [
            '' => '--- Please Select ---',
            'awaiting' => 'Đang chờ thanh toán',
            'success' => 'Đã thanh toán',
            'cancel' => 'Đã hủy thanh toán',
            'cancel_refund' => 'Hủy thanh toán và hoàn tiền',
        ],
    ],
    'main_button' => [
        'add' => ['class' => 'text-nowrap btn btn-warning', 'icon' => 'fa fa-file', 'name' => 'Thêm mới', 'route' => '/form'],
        'addRole' => ['class' => 'text-nowrap btn btn-info', 'icon' => 'fa fa-plus', 'name' => 'Thêm mới', 'route' => '.create'],
        'sort' => ['class' => 'text-nowrap btn btn-info', 'icon' => 'fa fa-sort', 'name' => 'Sắp xếp', 'route' => '/sort'],
        'delete' => ['class' => 'text-nowrap btn btn-danger ', 'icon' => 'fa fa-trash-o', 'name' => 'Xóa', 'route' => '/multiple', 'type' => 'delete'],
        'cancel' => ['class' => 'text-nowrap btn btn-danger ', 'icon' => 'fa fa-close', 'name' => 'HỦY ĐƠN', 'route' => '/multiple', 'type' => 'cancel'],
        'active' => ['class' => 'text-nowrap btn btn-success ', 'icon' => 'fa fa-check', 'name' => 'Kích hoạt', 'route' => '/multiple', 'type' => 'active'],
        'inactive' => ['class' => 'text-nowrap btn btn-danger ', 'icon' => 'fa fa-close', 'name' => 'Tạm ẩn', 'route' => '/multiple', 'type' => 'inactive'],
        'saveandrollback' => ['class' => 'text-nowrap btn btn-success ', 'icon' => 'fa fa-save', 'name' => 'Lưu', 'route' => '/save'],
        'saveandrollbackRole' => ['class' => 'text-nowrap btn btn-success ', 'icon' => 'fa fa-save', 'name' => 'Lưu', 'route' => '.update'],
        'save' => ['class' => 'text-nowrap btn btn-warning ', 'icon' => 'fa fa-save', 'name' => 'Lưu và Đóng', 'route' => '/save'],
        'save_index' => ['class' => 'text-nowrap btn btn-success ', 'icon' => 'fa fa-save', 'name' => 'Lưu', 'route' => '/save'],
        'deleteatform' => ['class' => 'text-nowrap btn btn-danger ', 'icon' => 'fa fa-trash-o', 'name' => 'Xóa', 'route' => '/delete'],
        'back' => ['class' => 'text-nowrap btn btn-info ', 'icon' => 'fa fa-mail-reply', 'name' => 'Quay về', 'route' => ''],
    ],
    'button_action' => [
        'edit' => ['class' => 'text-nowrap btn btn-info btn-xs', 'icon' => 'fa fa-pencil', 'name' => 'Sửa', 'route' => '/form'],
        'delete' => ['class' => 'text-nowrap btn btn-danger btn-xs', 'icon' => 'fa fa-trash-o', 'name' => 'Xóa', 'route' => '/delete'],
    ],
    'button_configs' => [
        'button_action' => [
            'default' => ['edit', 'delete'],
            'province' => ['edit', 'delete'],
            'district' => ['edit', 'delete'],
            'user' => ['edit', 'delete'],
        ],
        'main_button' => [
            'default' => [
                'default' => ['add', 'delete', 'active', 'inactive'],
                'form' => ['saveandrollback', 'save', 'deleteatform', 'back']
            ],
            'category' => [
                'default' => ['add', 'sort', 'delete', 'active', 'inactive'],
                'form' => ['saveandrollback', 'save', 'deleteatform', 'back']
            ],
            'province' => [
                'default' => ['add', 'delete', 'active', 'inactive'],
                'form' => ['saveandrollback', 'save', 'deleteatform', 'back']
            ],
            'settings' => [
                'default' => ['save_index'],
                'form' => []
            ],
            'district' => [
                'default' => ['add', 'delete', 'active', 'inactive'],
                'form' => ['saveandrollback', 'save', 'deleteatform', 'back']
            ],
            'user' => [
                'default' => ['add', 'delete', 'active', 'inactive'],
                'form' => ['saveandrollback', 'save', 'back']
            ],
            'roles' => [
                'default' => ['addRole'],
            ],
            'order' => [
                'default' => ['cancel'],
            ]
        ]
    ],
    'mail'=>[
        'logo' => 'media/mail/logo.png',
    ]
];
