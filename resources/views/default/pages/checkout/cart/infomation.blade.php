@php
$maleChecked = (isset($shoppingCartInfo) && $shoppingCartInfo['gender']==0 || !$shoppingCartInfo || $shoppingCartInfo =='') ? true : false;
$feMaleChecked = isset($shoppingCartInfo) && $shoppingCartInfo['gender']==1 ? true : false;
$name = ($shoppingCartInfo) ? $shoppingCartInfo['name'] : '';
$phone = ($shoppingCartInfo) ? $shoppingCartInfo['phone'] : '';
$email = ($shoppingCartInfo) ? $shoppingCartInfo['email'] : '';
$note = ($shoppingCartInfo) ? $shoppingCartInfo['note'] : '';
$address = ($shoppingCartInfo) ? $shoppingCartInfo['address'] : '';


$radioMale = html()->radio('gender',$maleChecked, 0)->attributes(['class'=>'form-check-input']);
$radioFeMale = html()->radio('gender',$feMaleChecked, 1)->attributes(['class'=>'form-check-input']);
$inputName = html()->text('name', $name)->attributes( ['class' => 'form-control','placeholder'=>'Họ & Tên']);
$inputPhone = html()->text('phone', $phone)->attributes( ['class' => 'form-control','placeholder'=>'Số điện thoại']);
$inputEmail = html()->text('email', $email)->attributes( ['class' => 'form-control','placeholder'=>'Địa chỉ email']);
$inputAdress = html()->text('address', $address)->attributes( ['class' => 'form-control','placeholder'=>'Số nhà, tên đường','id'=>'address']);
$inputNote = html()->textarea('note', $note)->attributes( ['class' => 'form-control','placeholder'=>'Ghi chú thêm (Không bắt buộc)','rows' => 3, 'cols' => 30]);
@endphp
<div class="block-infomation p-3 bg-white rounded my-4">
    <div class="title my-3">
        Thông tin khách hàng
    </div>
    <div class="mb-3 mt-4">
        <div class="form-check form-check-inline">
            {!!$radioMale!!}
            <label class="form-check-label">Anh</label>
        </div>
        <div class="form-check form-check-inline">
           {!!$radioFeMale!!}
            <label class="form-check-label" >Chị</label>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md">
            <div class="form-input mb-3">
               {!!$inputName!!}
            </div>
        </div>
        <div class="col-12 col-md">
            <div class="form-input mb-3">
                {!!$inputPhone!!}
            </div>
        </div>
        <div class="col-12 col-md">
            <div class="form-input mb-3">
                {!!$inputEmail!!}
            </div>
        </div>
    </div>
    <div class="form-input">
        {!!$inputNote!!}
    </div>
    <div class="title my-4">
        Thông tin nhận hàng
    </div>
    @if($shoppingCartInfo)
        @php
        $dataProvince = [
            "city_id" => (int)$shoppingCartInfo['city_id'],
            'district_id' => $shoppingCartInfo['district_id'],
            'ward_id' => $shoppingCartInfo['ward_id']
        ];
        @endphp
    <script type="text/javascript">
        var dataProvince = {!!json_encode($dataProvince)!!};
    </script>
    @endif

    <div class="row">
        <div class="col-12 col-md-6 col-xl">
            <div class="form-input mb-3">
                <select name="city_id" id="city" class="form-control">
                    <option>Tỉnh/Thành phố</option>
                </select>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl">
            <div class="form-input mb-3">
                <select name="district_id" disabled id="district" class="form-control">
                    <option>Quận/Huyện</option>
                </select>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl">
            <div class="form-input mb-3">
                <select name="ward_id" disabled id="ward" class="form-control">
                    <option>Phường/Xã</option>
                </select>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-12">
            <div class="form-input mb-3">
                {!!$inputAdress!!}
            </div>
        </div>
    </div>
</div>