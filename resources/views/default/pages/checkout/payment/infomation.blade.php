@php
    use App\Helpers\Format as Format;
@endphp
@if ($infomation)
    @php
        $dataTyle = config('configs.location');
        $male = $infomation['gender'] == 0 ? 'Anh' : 'Chị';
        $name = $infomation['name'];
        $email = $infomation['email'];
        $phone = Format::formatPhone($infomation['phone']);
        $note = $infomation['note'];
        $address = $infomation['address'];
        $city = $infomation['provice']['name'];
        $district = $infomation['provice']['district_one']['name'];
        $ward = $infomation['provice']['district_one']['ward']['name'];
    @endphp
    <div class="block-infomation p-3 bg-white rounded mb-4">
        <p class="mt-3">Cảm ơn {{ $male }} <strong>{{ $name }}</strong> đã cho {{ $locals['company'] }}
            cơ hội phục vụ.</p>
        <div class="title my-4">
            Thông tin giao hàng
        </div>
        <p><strong>Người nhận hàng: </strong>{{ $male }} {{ $name }}, {{ $phone }},
            {{ $email }}</p>
        <p><strong>Giao đến: </strong> {{ $address }}, {{ $ward }}, {{ $district }},
            {{ $city }}</p>
        @if ($note != '')
            <p><strong>Ghi chú: </strong> {{ $note }}</p>
        @endif
    </div>
@endif
