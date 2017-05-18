$('#datameter-month, #datameter-address').change(function () {
    $.get(opts.getCustomerUrl, {
        periode: $('#datameter-month').val(),
        address: $('#datameter-address').val(),
    }, function (data) {
        // clear table
        var $tbody = $('#tbl-body');
        opts.periode = data.periode;
        $tbody.html('');
        $.each(data.rows, function (i, v) {
            var $tr = $('#row-template').clone();
            $tr.children('td.code').text(v.code);
            $tr.children('td.name').text(v.name);
            $tr.children('td.previous').text(v.meter1);
            var $inp = $tr.find('td.input > input');
            $inp.val(v.meter2);
            $inp.data('c_id', v.id);
            $inp.data('last_value', v.meter2);
            $tr.show();
            $tbody.append($tr);
        });
    });
});

$('#tbl-body').on('blur', 'td.input > input', function () {
    postMeter(this);
});

function postMeter(inp) {
    var $inp = $(inp);
    if (inp.value !== $inp.data('last_value')) {
        console.log([$inp.data('c_id'), inp.value]);
        $.post(opts.postMeterUrl,{
            periode:opts.periode,
            c_id:$inp.data('c_id'),
            value:inp.value,
        }, function (data) {
            $inp.data('last_value', data.current_meter);
        });
    }
}

$('#tbl-body').on('keypress', 'td.input > input', function (e) {
    switch (e.keyCode) {
        case 13:
        case 40: // bawah
            var $el = $(this).closest('tr').next('tr');
            if ($el.length) {
                $el.find('td.input > input').focus().select();
            } else {
                postMeter(this);
            }
            break;
        case 38:
            var $el = $(this).closest('tr').prev('tr');
            if ($el.length) {
                $el.find('td.input > input').focus().select();
            } else {
                postMeter(this);
            }
            break;
    }
});
