function financial(val) {
    return Number.parseFloat(val).toFixed(2);
}
// Install input filters.
$(".input-number").inputFilter(function(value) {
    return /^-?\d*$/.test(value); });

$(document).on('click', '#uniform-ecommerce_order_specialDiscount', function() {
    alert('true');
    if($("#ecommerce_order_specialDiscount").is(':checked')){
        alert('true');
        $('#ecommerce_order_discountCalculation').prop("disabled", false);
    }else{
        alert('false');
        $('#ecommerce_order_discountCalculation').prop("disabled", true);
    }
});

$(document).on('change', '.discount', function() {

    var discount = $("#discountCalculation").val();
    var url = $(this).attr('data-action');
    $.get(url, {discount:discount} , function(response){
        let obj = JSON.parse(response);
        $('#discount').html(obj['discount']);
        $('#vat').val(obj['vat']);
        $('#shippingCharge').val(obj['shippingCharge']);
        $('#total').html(obj['total']);
        $('#orderTotal').val(obj['total']);
        $('#receive').html(obj['receive']);
        $('#due').html(obj['due']);
    });

});

$(document).on('keyup', '#ecommerce_payment_amount', function() {
    var payment  = parseInt($('#ecommerce_payment_amount').val()  !== '' ? $('#ecommerce_payment_amount').val() : 0 );
    var due  = parseInt($('#due').val()  !== '' ? $('#due').val() : 0 );
    var dueAmount = (due - payment);
    if(dueAmount > 0){
        $('#balance').html('Due '+financial(dueAmount));
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return '+financial(balance));
    }
});


$(document).on('click', '.method-process', function() {
    var method = $(this).val();
    if(method === 'Bank' ){
        $('.bankHide').show(500);
        $('.mobileBankHide').hide(500);
    }else if(method === 'Mobile'){
        $('.bankHide').hide(500);
        $('.mobileBankHide').show(500);
    }else{
        $('.bankHide').hide(500);
        $('.mobileBankHide').hide(500);
    }
});

$(".select2StockMedicine").select2({

    placeholder: "Search stock item name",
    ajax: {
        url: Routing.generate('order_stock_item_search'),
        dataType: 'json',
        delay: 250,
        data: function (params, page) {
            return {
                q: params,
                page_limit: 100
            };
        },
        results: function (data, page) {
            return {
                results: data
            };
        },
        cache: true
    },
    escapeMarkup: function (m) {
        return m;
    },
    formatResult: function (item) { return item.text}, // omitted for brevity, see the source of this page
    formatSelection: function (item) { return item.text }, // omitted for brevity, see the source of this page
    initSelection: function (element, callback) {
        var id = $(element).val();
    },
    allowClear: true,
    minimumInputLength: 1

});


$(document).on('change', '#orderItem_itemName', function() {

    var medicine = $(this).val();
    if(medicine === ""){ return false }
    $.ajax({
        url: Routing.generate('order_item_stock_details',{'id': medicine}),
        type: 'GET',
        success: function (response) {
            obj = JSON.parse(response);
            $('#orderItem_price').val(obj['price']);
            $('#orderItem_quantity').focus();
            if(obj['unit'] !== ""){
                $('#unit').html(obj['unit']);
            }
        }
    })

});
$('form#orderItem').on('keypress', '.input', function (e) {

    if (e.which === 13) {
        var inputs = $(this).parents("form#orderItem").eq(0).find("input,select");
        var idx = inputs.index(this);

        if (idx === inputs.length - 1) {
            inputs[0].select()
        } else {
            inputs[idx + 1].focus(); //  handles submit buttons
        }
        switch (this.id) {
            case 'orderItem_itemName':
                $('#orderItem_quantity').focus();
                break;

            case 'orderItem_quantity':
                $('#addOrderItem').click();
                $('#orderItem_itemName').select2('open');
                break;

        }
        return false;
    }
});

$(document).on('click', '#addOrderItem', function() {

    if($("#orderItem").valid()){
        $.ajax({
            url         : $('form#orderItem').attr( 'action' ),
            type        : $('form#orderItem').attr( 'method' ),
            data        : new FormData($('form#orderItem')[0]),
            processData : false,
            contentType : false,
            success: function(response){
                let obj = JSON.parse(response);
                $('#orderTotal').val(obj['total']);
                $('#discount').val(obj['discount']);
                $("#orderItems").html(obj['items']);
                $('form#orderItem')[0].reset();
            }
        });
    }
});

$('#orderItems').on("click", ".item-remove", function() {

    var url = $(this).attr("data-url");
    var id = $(this).attr("id");
    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            let obj = JSON.parse(response);
            $('#orderTotal').val(obj['total']);
            $('#discount').val(obj['discount']);
            $("#orderItems").html(obj['items']);
            $('form#orderItem')[0].reset();
        }
    })
})

var formTemporary = $("#orderItem").validate({

    rules: {

        "orderItem[itemName]": {required: true},
        "orderItem[price]": {required: true},
        "orderItem[quantity]": {required: true},
    },

    messages: {

        "orderItem[itemName]":"Enter medicine name",
        "orderItem[price]":"Enter sales price",
        "orderItem[quantity]":"Enter medicine quantity",
    },
    tooltip_options: {
        "orderItem[itemName]": {placement:'top',html:true},
        "orderItem[price]": {placement:'top',html:true},
        "orderItem[quantity]": {placement:'top',html:true},
    }
});

$(document).on("change", ".transactionProcess", function() {

    var url = $('form#transactionUpdate').attr('action'); // Create an arbitrary FormData instance
    var shippingCharge = $('#shippingCharge').val();
    var discount = $('#discount').val();
    $.ajax({
        url:url,
        type: 'POST',
        data: 'shippingCharge='+shippingCharge+'&discount='+discount,
        success: function(response){
            let obj = JSON.parse(response);
            $('#discount').val(obj['discount']);
            $('#vat').val(obj['vat']);
            $('#shippingCharge').val(obj['shippingCharge']);
            $('#total').html(obj['total']);
            $('#receive').html(obj['receive']);
            $('#due').html(obj['due']);
        }
    });

});

$(document).on('click', '#process-order', function(event) {

    var process = $(this).val();
    var method = $('input[name=method]:checked').val();
    var outstanding  = parseInt($('#outstandingHidden').val()  !== '' ? $('#outstandingHidden').val() : 0 );
    var total  = parseInt($('#orderTotal').val()  !== '' ? $('#orderTotal').val() : 0 );
    var payment  = parseInt($('#ecommerce_order_receive').val()  !== '' ? $('#ecommerce_order_receive').val() : 0 );
    var credit = (total - outstanding);
    if( method === "Credit" && total > outstanding){
        alert("You are makeing the over the outstanding "+credit);
        return false;
    }
    myArray = new Array("Cash", "Mobile","Bank");
    var amount = ((total-payment) - outstanding);
    if($.inArray(method, myArray) !== -1 && (total-payment) > outstanding){
        alert("You are makeing the over the outstanding "+amount);
        return false;
    }
    $('#confirm-content').confirmModal({
        topOffset: 0,
        top: '25%',
        onOkBut: function(event) {
            $('#orderProcess').submit();
        }
    });
});

$(document).on('keyup', '.payment', function() {

    var payment  = parseInt($('#pos_payment').val()  !== '' ? $('#pos_payment').val() : 0 );
    var due  = parseInt($('#due').val()  !== '' ? $('#due').val() : 0 );
    var dueAmount = (due - payment);
    if(dueAmount > 0){
        $('#balance').html('Due '+financial(dueAmount));
    }else{
        var balance =  payment - due ;
        $('#balance').html('Return '+financial(balance));
    }

});

$(document).on("click", "#cashOnDelivery", function() {
    if($(this).prop("checked") === false){
        $("#cashOn").show();
        $("#adminSubmitPayment").removeClass("submitOrder").addClass("submitPayment");
    }else{
        $("#cashOn").hide();
        $("#adminSubmitPayment").removeClass("submitPayment").addClass("submitOrder");
    }
});

$(document).on("change", ".input-update", function() {

    var formData = new FormData($('form#orderUpdate')[0]); // Create an arbitrary FormData instance
    var url = $('form#orderUpdate').attr('action'); // Create an arbitrary FormData instance
    $.ajax({
        url:url ,
        type: 'POST',
        processData: false,
        contentType: false,
        data:formData,
        success: function(response){}
    });

});


