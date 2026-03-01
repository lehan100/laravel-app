<tr>
    <td class="border-0 form-input" colspan="2">
        <div class="row">
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-ticket-perforated"></i></span>
                    <input class="form-control" id="coupon_code" value="{{@$discountCode['coupon_info']['coupon_code']}}" placeholder="Nhập mã giảm giá" name="coupon_code"
                        type="text" />
                </div>
            </div>
            <div class="col-auto">
                <button type="button" @disabled(!$discountCode) id="btn-coupon" class="btn btn-coupon btn-primary">Áp dụng</button>
            </div>
        </div>
    </td>
</tr>
