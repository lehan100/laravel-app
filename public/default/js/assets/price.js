const Price = {
    formatNumber: function (number) {
        number = parseInt(number);
        return new Intl.NumberFormat('vi-VN', {style: 'currency', currency: 'VND'}).format(number);
    },
    getPercent: function (param) {
        if (param.price > 0 && param.special_price > 0) {
            var pecent = ((param.price - param.special_price) / param.price) * 100;
            return "-" + Math.round(pecent) + '<i class="bi bi-percent"></i>';
        }
    }
}