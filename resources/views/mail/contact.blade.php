@php
    use \Illuminate\Support\Carbon;
    use App\Models\Storage;
    use Illuminate\Support\Facades\Storage as StorageDisk;
    use App\Helpers\Format as Format;

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
    // data
    $name = $data['name'];
    $phone = Format::formatPhone($data['phone']);
    $email = $data['email'];
    $title = $data['title'];
    $message = $data['message'];
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
                        <strong>THÔNG TIN LIÊN HỆ</strong>
                    </p>
                    <p
                    style="margin:0 0 10px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:18px;">
                    <i>Ngày: {{ Carbon::now()->format("d/m/Y") }}</i>
                </p>
                </td>
                <td width="40"></td>
            </tr>
            <tr>
                <td width="40"></td>
                <td width="640" colspan="3" style="padding: 15px;vertical-align:top;background:#fff">
                    <p
                        style="margin:0 0 10px 0;padding-bottom:3px;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:18px;color:#333;">
                        <strong>Nội dung</strong>
                    </p>
                    <table style="margin:0 auto;padding:0;background:#fff;font-size:15px;text-align:left;"
                        cellpadding="0" cellspacing="0" border="0" width="640">
                        <tr>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                Tiêu đề</td>
                            <td width="10"></td>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                {{ $title }}</td>
                        </tr>
                        <tr>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                Nội dung</td>
                            <td width="10"></td>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                {{ $message }}</td>
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
                        <strong>Thông tin người gửi</strong>
                    </p>
                    <table style="margin:0 auto;padding:0;background:#fff;font-size:15px;text-align:left;"
                        cellpadding="0" cellspacing="0" border="0" width="640">
                        <tr>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                Họ và Tên</td>
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
                                Email</td>
                            <td width="10"></td>
                            <td width="320"
                                style="padding:3px 0;color:#333;font-family:'Myriad Pro', Helvetica, Arial, sans-serif;font-size:15px;text-align: left">
                                {{ $email }}</td>
                        </tr>

                    </table>
                </td>
                <td width="40"></td>
            </tr>

            <tr>
                <td colspan="5" style="padding: 8px 0">&nbsp;</td>
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
