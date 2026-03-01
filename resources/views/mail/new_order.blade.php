@php
    use App\Models\Storage;
    use Illuminate\Support\Facades\Storage as StorageDisk;
    use App\Helpers\Format as Format;

    use App\Helpers\Product\Price as ProductPriceHelper;
    use App\Helpers\Product\Sale as SalePriceHelper;
    use App\Helpers\Product\Quantity as ProductQuantityHelper;

    $configs = config('configs.mail');
    $paymentConfig = config('configs.payment_method');
    if (StorageDisk::disk('main')->exists('settings.json')) {
        $dataStorage = StorageDisk::disk('main')->get('settings.json');
        $settings = json_decode($dataStorage, true);
    } else {
        $dataConfig = (new Storage())->getStorage('settings');
        if ($dataConfig) {
            $settings = json_decode($dataConfig->data, true);
            StorageDisk::disk('main')->put('settings.json', json_encode($settings));
        }
    }
    $infomation = $data['customer'];
    $male = $infomation['gender'] == 0 ? 'Anh' : 'Chị';
    $name = $infomation['name'];
    $email = $infomation['email'];
    $phone = Format::formatPhone($infomation['phone']);
    $note = $infomation['note'];
    $address = $infomation['address'];
    $city = $infomation['provice']['name'];
    $district = $infomation['provice']['district_one']['name'];
    $ward = $infomation['provice']['district_one']['ward']['name'];
    /*Payment*/
    $paymentMothod = $paymentConfig[$data['payment_method']]['title'];
    /*Order*/
    $invoice_id = $data['invoice_id'];
    $order_date = $data['order_date'];
    $order_message = $data['order_message'];
    /* Cart */
    $shoppingCart = $data['shoppingCart'];
@endphp
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <!-- NAME: 1 COLUMN - BANDED -->
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>NEW ORDER</title>
</head>
@php
    $hotline = $settings['hotline'];
    $email = $settings['email'];
    $domain = $settings['domain'];
    $copyright = $settings['title'];
@endphp

<body style="padding: 0;margin: 0;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #fff;">
    <div style="margin:auto;text-align:center;width:720px;border:2px solid #333">
        <table style="margin:0 auto;padding:0;background:#eee;font-size:14px;text-align:left;" cellpadding="0"
            cellspacing="0" border="0" width="720">
            <tr style="background:#c54903">
                <td width="40"></td>
                <td width="320"
                    style="font-family:'Myriad Pro', Helvetica, Arial, sans-serif;padding:8px 0 4px 0;font-size:15px;color:#eee">
                    Hotline <a style="color:#eee;text-decoration: none;"
                        href="tel://{{ $hotline }}">{{ $hotline }}</a>
                </td>
                <td width="20"></td>
                <td width="320">
                    <table cellpadding="0" border="0" cellspacing="0" width="325">
                        <tr style="padding:6px 0;text-align:right;">
                            <td
                                style="font-family:'Myriad Pro', Helvetica, Arial, sans-serif;padding:8px 0 4px 0;font-size:15px;color:#eee">
                                Email: <a style="color:#eee;text-decoration: none;"
                                    href="mailto:{{ $email }}">{{ $email }}</a></td>
                        </tr>
                    </table>
                </td>
                <td width="40"></td>
            </tr>
            <tr style="background:#f75d06">
                <td width="40"></td>
                <td width="320" style="padding:15px 0;" colspan="3" align="center">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset($configs['logo']) }}" alt="{{ $copyright }}" title=""
                            class="img-responsive" style="max-height:55px;">
                    </a>
                </td>
                <td width="40"></td>
            </tr>
            <tr>
                <td width="40"></td>
                <td width="640" style="padding:15px 0;" colspan="3" align="center">
                    <p
                        style="margin:10px 0 10px 0;color:#f16523;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:22px;">
                        <strong>XÁC NHẬN ĐƠN HÀNG</strong>
                    </p>
                    <p
                        style="margin:0 0 10px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:18px;">
                        <i>Mã đơn hàng: #{{ $invoice_id }}</i>
                    </p>
                    <p style="border-bottom:2px solid #ddd; margin: 0 0 20px 0">&nbsp;</p>
                    <p
                        style="margin:0 0 10px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:16px;text-align: left">
                        Xin chào {{ $male . ' ' . $name }},
                    </p>
                    <p
                        style="margin:0 0 10px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                        Chân thành cảm ơn quý khách hàng đã tin tưởng và sử dụng sản phẩm tại {{ ucfirst($domain) }}
                    </p>
                    <p
                        style="margin:0 0 10px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                        Đơn hàng của khách hàng sẽ được vận chuyển đến địa chỉ nhận hàng trong thời gian sớm nhất.
                    </p>
                    <p
                        style="margin:0 0 10px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                        {{ ucfirst($domain) }} hy vọng khách hàng hài lòng với trải nghiệm mua sắm và sản phẩm đã chọn.
                    </p>
                </td>
                <td width="40"></td>
            </tr>

            <tr>
                <td width="40"></td>
                <td width="640" colspan="3" style="padding: 15px;vertical-align:top;background:#fff">
                    <p
                        style="margin:0 0 10px 0;padding-bottom:3px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:18px;color:#333;">
                        <strong>Thông tin đơn hàng</strong>
                    </p>
                    <table style="margin:0 auto;padding:0;background:#fff;font-size:15px;text-align:left;"
                        cellpadding="0" cellspacing="0" border="0" width="640">
                        <tr>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                Mã đơn hàng</td>
                            <td width="10"></td>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                #{{ $invoice_id }}</td>
                        </tr>
                        <tr>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                Ngày đặt hàng</td>
                            <td width="10"></td>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                {{ $order_date }}</td>
                        </tr>
                        <tr>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                Trạng thái đơn hàng</td>
                            <td width="10"></td>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                {{ $order_message }}</td>
                        </tr>
                        <tr>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                Phương thức thanh toán</td>
                            <td width="10"></td>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                {{ $paymentMothod }}</td>
                        </tr>
                    </table>
                </td>
                <td width="40"></td>
            </tr>
            <tr>
                <td width="40"></td>
                <td width="640" colspan="3" style="padding: 15px;vertical-align:top;background:#fff">
                    <p
                        style="margin:0 0 10px 0;padding-bottom:3px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:18px;color:#333;">
                        <strong>Thông tin người nhận hàng</strong>
                    </p>
                    <table style="margin:0 auto;padding:0;background:#fff;font-size:15px;text-align:left;"
                        cellpadding="0" cellspacing="0" border="0" width="640">
                        <tr>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                Tên người nhận</td>
                            <td width="10"></td>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                {{ $name }}</td>
                        </tr>
                        <tr>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                Số điện thoại</td>
                            <td width="10"></td>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                {{ $phone }}</td>
                        </tr>
                        <tr>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                Địa chỉ nhận hàng</td>
                            <td width="10"></td>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                {{ $address }}, {{ $ward }}, {{ $district }},{{ $city }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="40"></td>
            </tr>
            <tr>
                <td colspan="5" style="padding: 8px 0">&nbsp;</td>
            </tr>
            <tr>
                <td width="40"></td>
                <td width="640" colspan="3" style="padding: 15px;vertical-align:top;background:#fff">
                    <p
                        style="margin:0 0 30px 0;padding-bottom:3px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:18px;color:#333;">
                        <strong>Chi tiết đơn hàng</strong>
                    </p>
                    <table style="margin:0 auto;padding:0;background:#fff;font-size:15px;text-align:left;"
                        cellpadding="0" cellspacing="0" border="0" width="640">
                        <thead>
                            <tr>
                                <th
                                    style="border-bottom: 1px solid #000;padding:8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: left">
                                    SẢN PHẨM</th>
                                <th
                                    style="border-bottom: 1px solid #333;padding:8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: center">
                                    GIÁ</th>
                                <th
                                    style="border-bottom: 1px solid #333;padding:8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: center">
                                    SỐ LƯỢNG</th>
                                <th
                                    style="border-bottom: 1px solid #333;padding:8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: right">
                                    TỔNG TIỀN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $discount = 0;
                                $index = 0;
                            @endphp
                            @foreach ($shoppingCart as $key => $cart)
                                @php
                                    $name = $cart['name'];
                                    $sku = $cart['sku'];
                                    $qty = $cart['qty'];
                                    $price = ProductPriceHelper::getPriceCheckout($cart);
                                    $total = $price * $qty;
                                    $style = ++$index < count($shoppingCart) ? 'border-bottom: 1px solid #d1d1d1;' : '';
                                @endphp
                                <tr>
                                    <td
                                        style="{{ $style }} padding:10px 8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: left">
                                        <p style="margin: 0 0 10px 0"><strong>{{ $name }}</strong></p>
                                        @if ($sku != '')
                                            <p style="margin: 0 0 10px 0">SKU: {{ $sku }}</p>
                                        @endif
                                        @if (isset($cart['options']) && count($cart['options']) > 0)
                                            @foreach ($cart['options'] as $option)
                                                @php
                                                    $title = $option['title'];
                                                    $titleValue = $option['attributes'][0]['title'];
                                                @endphp
                                                <p style="margin: 0 0 10px 0">{{ $title }}:
                                                    {{ $titleValue }}
                                                </p>
                                            @endforeach
                                        @endif
                                        {!! SalePriceHelper::saleGiftMail($cart) !!}
                                    </td>
                                    <td
                                        style="{{ $style }} padding:10px 8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: center">
                                        {!! ProductPriceHelper::format_price($price) !!}</td>
                                    <td
                                        style="{{ $style }} padding:10px 8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: center">
                                        {{ $qty }}</td>
                                    <td
                                        style="{{ $style }} padding:10px 8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: right">
                                        {!! ProductPriceHelper::format_price($total) !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @php
                            $subTotal = $data['subTotal'];
                            $totalQuantity = $data['totalQuantity'];
                            $shippingPrice = $data['shippingPrice'];
                            $discountCode = $data['discountCode'];
                        @endphp
                        <tfoot>
                            <tr>
                                <th colspan="3"
                                    style="background-color: #eee;padding:10px 8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: right; font-weight: 400">
                                    Tổng tiền hàng</th>
                                <th
                                    style="background-color: #eee;padding:10px 8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: right ;font-weight: 400">
                                    {!! ProductPriceHelper::format_price($subTotal) !!}</th>
                            </tr>
                            <tr>
                                <th colspan="3"
                                    style="background-color: #eee;padding:10px 8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: right; font-weight: 400">
                                    Phí vận chuyển</th>
                                <th
                                    style="background-color: #eee;padding:10px 8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: right ;font-weight: 400">
                                    {!! ProductPriceHelper::format_price($shippingPrice) !!}</th>
                            </tr>
                            @if ($discountCode)
                                @php
                                    $discount = $discountCode['discount'];
                                @endphp
                                <tr>
                                    <th colspan="3"
                                        style="background-color: #eee;padding:10px 8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: right; font-weight: 400">
                                        Giảm giá
                                        ({{ $discountCode['coupon_info']['coupon_code'] }})</th>
                                    <th
                                        style="background-color: #eee;padding:10px 8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: right ;font-weight: 400">
                                        - {!! ProductPriceHelper::format_price($discountCode['discount']) !!}</th>
                                </tr>
                            @endif
                            <tr>
                                <th colspan="3"
                                    style="background-color: #eee;padding:10px 8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: right;">
                                    Tổng tiền</th>
                                <th
                                    style="background-color: #eee;padding:10px 8px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;text-align: right ;">
                                    {!! ProductPriceHelper::format_price($subTotal + $shippingPrice - $discount) !!}</th>
                            </tr>
                        </tfoot>
                    </table>
                </td>
                <td width="40"></td>
            </tr>
            <tr>
                <td colspan="5" style="padding: 8px 0">&nbsp;</td>
            </tr>
            <tr>
                <td width="40"></td>
                <td width="640" style="padding:15px 0;" colspan="3" align="left">
                    <p
                        style="margin:0 0 10px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;">
                        - Quý khách nhận được email này vì đã mua hàng tại {{ ucfirst($domain) }}
                    </p>
                    <p
                        style="margin:0 0 10px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;line-height: 23px">
                        - Trường hợp quý khách có những băn khoăn về đơn hàng, quý khách có thể gọi cho chúng tôi theo
                        số <a style="color: blue;text-decoration: none;"
                            href="tel://{{ $hotline }}">{{ $hotline }}</a> tất cả các ngày trong tuần từ
                        8:00-22:00h
                    </p>
                    <p
                        style="margin:0 0 10px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:14px;">
                        Cảm ơn quý khách!
                    </p>
                </td>
                <td width="40"></td>
            </tr>
            <tr>
                <td colspan="5" align="center"
                    style="color:#ddd;background:#48443f;padding:15px;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:13px;line-height:23px;">
                    Copyright © {{ $copyright }}
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
