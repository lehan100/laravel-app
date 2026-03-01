<div class="block-payment p-3 bg-white rounded mb-4">
    <div class="title mb-4 mt-2">
        Hình thức thanh toán
    </div>
    <div class="form-check item-payment mb-3">
        <label class="form-check-label" for="cod">

            <div class="row align-items-center mx-0">
                <div class="col-auto p-0">
                    <input class="form-check-input" @checked(true) type="radio" value="cash_on_delivery" name="payment_method"
                        id="cod">
                </div>
                <div class="col-auto px-1">
                    <img src="{{ asset('media/payments/cod.jpg') }}" width="45px" alt="cod" />
                </div>
                <div class="col ps-1"><strong>Thanh toán khi nhận hàng</strong></div>
            </div>
        </label>
    </div>
    <div class="form-check item-payment mb-3 d-none">
        <label class="form-check-label" for="captureMoMoWallet">
            <div class="row align-items-center mx-0">
                <div class="col-auto p-0">
                    <input class="form-check-input" type="radio" value="captureMoMoWallet" name="payment_method"
                        id="captureMoMoWallet">
                </div>
                <div class="col-auto px-1">
                    <img src="{{ asset('media/payments/momo.png') }}" width="45px" alt="Ví MoMo" />
                </div>
                <div class="col ps-1"><strong>Ví MoMo</strong></div>
            </div>
        </label>
    </div>
    <div class="form-check item-payment mb-3 d-none">
        <label class="form-check-label" for="payWithMoMoATM">
            <div class="row align-items-center mx-0">
                <div class="col-auto p-0">
                    <input class="form-check-input" type="radio" value="payWithMoMoATM" name="payment_method"
                        id="payWithMoMoATM">
                </div>
                <div class="col-auto px-1">
                    <img src="{{ asset('media/payments/atm.svg') }}" width="45px" alt="MoMo ATM" />
                </div>
                <div class="col ps-1"><strong>Thẻ ATM nội địa</strong></div>
            </div>
        </label>
    </div>
</div>
