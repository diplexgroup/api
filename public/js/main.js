jQuery(function ($) {
    if (window.location.pathname.startsWith('/project_roads/edit')) {
        var el = $('#fee_strategy_textarea');

        var changeJSON = function () {
            var data = {};
            //make data
            data.type = JSON.parse(el.val())['type'];
            var amount = parseInt($('#amount').val());
            var percent = parseFloat($('#percent').val().replace(",", "."));
            if (percent) {
                data.percent = percent;
            }
            if (amount) {
                data.amount = amount;
            }
            // console.log(data)

            el.html(JSON.stringify(data));
        };


        var fee_strategy_tr = $('#fee-strategy-tr');
        fee_strategy_tr.hide();

        $('<tr><td>Фиксированная сумма комиссии: </td><td><input type="number" value="" id="amount"></td></tr>').insertAfter(fee_strategy_tr);
        $('<tr><td>Процент комиссии</td><td><input type="number" min="0" max="1" step="0.0001" id="percent"></td></tr>').insertAfter(fee_strategy_tr);
        $('#percent').val(JSON.parse(el.val())['percent']);
        $('#amount').val(JSON.parse(el.val())['amount']);


        $('#amount, #percent').on('change keypress input', changeJSON);
    }

});


jQuery(function ($) {
    if (window.location.pathname.startsWith('/project_roads/view')) {

        var el = $('tr:contains("Стратегия категорий")');
        el.hide();
        var content = JSON.parse(el.contents()[3].innerHTML);
        if (content["percent"]) {
            $(`<tr><td>Процент комиссии</td><td>${content["percent"]}</td></tr>`).insertAfter(el);
        }
        if (content["amount"]) {
            $(`<tr><td>Фиксированная сумма комиссии</td><td>${content["amount"]}</td></tr>`).insertAfter(el);
        }
    }


});
