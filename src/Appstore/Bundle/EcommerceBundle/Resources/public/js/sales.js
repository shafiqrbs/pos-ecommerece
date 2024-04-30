
/**
 * Created by rbs on 2/9/16.
 */

var InventorySales = function(sales) {

    $('input[name=barcode]').focus();
    $(document).on('change', '#barcode', function() {
        var barcode = $('#barcode').val();
        if(barcode == ''){
            $('#wrongBarcode').html('Using wrong barcode, please try again correct barcode.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_item_search'),
            type: 'POST',
            data:'barcode='+barcode+'&sales='+ sales,
            success: function(response) {
                $('#barcode').focus().val('');
                obj = JSON.parse(response);
                $('#purchaseItem').html(obj['purchaseItem']);
                $('#salesItem').html(obj['salesItem']);
                $('.salesTotal').html(obj['salesTotal']);
                $('.salesTotal').val(obj['salesTotal']);
                $('#paymentTotal').val(obj['salesTotal']);
                $('#paymentSubTotal').val(obj['salesTotal']);
                FormComponents.init();
            },

        })
    })

    $(document).on('change', '#barcodeNo', function() {

        var barcode = $('#barcodeNo').val();
        if(barcode == ''){
            $('#wrongBarcode').html('Using wrong barcode, please try again correct barcode.');
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_item_search'),
            type: 'POST',
            data:'barcode='+barcode+'&sales='+ sales,
            success: function(response) {
                $('#barcode').focus().val('');
                obj = JSON.parse(response);
                $('#purchaseItem').html(obj['purchaseItem']);
                $('#salesItem').html(obj['salesItem']);
                $('.salesTotal').html(obj['salesTotal']);
                $('.salesTotal').val(obj['salesTotal']);
                $('#paymentTotal').val(obj['salesTotal']);
                $('#paymentSubTotal').val(obj['salesTotal']);
                FormComponents.init();
            },
        })
    })

    $(document).on('change', '#item', function() {

        var item = $('#item').val();
        if(item == ''){
            $('#stockItemDetails').hide();
            return false;
        }
        $.ajax({
            url: Routing.generate('inventory_sales_item_purchase'),
            type: 'POST',
            data:'item='+ item +'&sales=' + sales,
            success: function(response) {
                $('#stockItemDetails').show();
                $('#itemDetails').html(response);
                $(".editable").editable();
            },
        })
    })



    $(document).on("click", ".customPrice", function() {

        var rel = $(this).attr('rel');
        var estimatePrice = parseInt($('#estimatePrice-'+rel).val());
        var quantity = parseInt($('#quantity-'+rel).val());
        if($(this).is(':checked'))
        {
            $("#salesPrice-"+rel).attr("readonly", false);
            $("#salesPrice-"+rel).focus().val(estimatePrice);


        }else{

            var customPrice = 0;
            $("#salesPrice-"+rel).attr("readonly", true);
            $("#salesPrice-"+rel).focus().val(estimatePrice);
            var subTotal  = (quantity * estimatePrice);
            $("#subTotalShow-"+rel).html(subTotal);
            $.ajax({
                url: Routing.generate('inventory_sales_item_update'),
                type: 'POST',
                data:'salesItemId='+rel+'&quantity='+quantity+'&salesPrice='+estimatePrice+'&customPrice='+customPrice,
                success: function(response) {
                    obj = JSON.parse(response);
                    $('.salesTotal').html(obj['salesTotal']);
                    $('.salesTotal').val(obj['salesTotal']);
                    $('#paymentTotal').val(obj['salesTotal']);
                    $('#paymentSubTotal').val(obj['salesTotal']);
                },

            })
        }
    });



    $(document).on('change', '.quantity , .salesPrice', function() {

        var rel = $(this).attr('rel');

        var quantity = parseInt($('#quantity-'+rel).val());
        var price = parseInt($('#salesPrice-'+rel).val());
        if($('#customPrice-'+rel).is(':checked')){
            var customPrice = 1;
        }else{
            var customPrice = 0;
        }
        var subTotal  = (quantity * price);
        $("#subTotalShow-"+rel).html(subTotal);

        $.ajax({
            url: Routing.generate('inventory_sales_item_update'),
            type: 'POST',
            data:'salesItemId='+rel+'&quantity='+quantity+'&salesPrice='+price+'&customPrice='+customPrice,
            success: function(response) {
                obj = JSON.parse(response);
                $('.salesTotal').html(obj['salesTotal']);
                $('.salesTotal').val(obj['salesTotal']);
                $('#paymentTotal').val(obj['salesTotal']);
                $('#paymentSubTotal').val(obj['salesTotal']);
            },

        })
    })

    $('#salesItem').on("click", ".delete", function() {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                if ('success' == obj['success']) {
                    $('#remove-' + id).hide();
                    $('.salesTotal').html(obj['salesTotal']);
                    $('.salesTotal').val(obj['salesTotal']);
                    $('#paymentTotal').val(obj['salesTotal']);
                    $('#paymentSubTotal').val(obj['salesTotal']);
                }


            },
        })
    })

    $(document).on('change', '#paymentAmount , #vat , #discount ', function() {

        var payment     = parseInt($('#paymentAmount').val()  != '' ? $('#paymentAmount').val() : 0 );
        var subTotal       = parseInt($('#paymentSubTotal').val() != '' ? $('#paymentSubTotal').val() : 0);
        var vat         = parseInt($('#vat').val() != '' ? $('#vat').val() : 0 );
        var discount    = parseInt($('#discount').val() != '' ? $('#discount').val() : 0);
        var netAmount   = ((subTotal+vat)-discount);

        $('.salesTotal').html(netAmount);
        $('#paymentTotal').val(netAmount);

        var total =  parseInt($('#paymentTotal').val());
        if( payment >= total ){

            var returnAmount = ( payment - total );

            $('#returnAmount').val(returnAmount);
            $('#returnAmount').addClass('payment-yellow');
            $('#dueAmount').val('');
            $('#dueAmount').removeClass('payment-red');

        }else{

            var dueAmount = (total-payment);
            if(dueAmount > 0){

                $('#returnAmount').val('');
                $('#returnAmount').removeClass('payment-yellow');
                $('#dueAmount').val(dueAmount);
                $('#dueAmount').addClass('payment-red');
            }
        }
        if(payment > 0 && total > 0  ){
            $("#paymentBtn").attr("disabled", false);
        }else{
            $("#paymentBtn").attr("disabled", true);
        }

    })


    $(document).on('change', '#sales_paymentMethod', function() {

        var paymentMethod = $(this).val();
        if( paymentMethod == 'Payment Card'){
            $('#cartMethod').show();
        }else{
            $('#cartMethod').hide();
        }

    })

    $('#sales').on("click", ".delete", function() {

        var url = $(this).attr("rel");
        var id = $(this).attr("id");
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                obj = JSON.parse(response);
                if ('success' == obj['success']) {
                    $('#salesRemove-' + id).hide();
                }
            }
        })
    })

    $(".select2Item").select2({
        placeholder: "Search item, color, size & vendor name",
        allowClear: true,
        ajax: {
            url: Routing.generate('item_search'),
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
        formatResult: function(item){ return item.masterItemName +' - '+ item.colorName +' - '+ item.sizeName +' - '+ item.vendorName +' -> '+ ((item.purchaseQuantity + item.salesQuantityReturn + item.purchaseQuantityReplace ) - (item.purchaseQuantityReturn + item.salesQuantity ) ) }, // omitted for brevity, see the source of this page
        formatSelection: function(item){return item.sku + ' - ' + item.text}, // omitted for brevity, see the source of this page
        initSelection: function(element, callback) {
            var id = $(element).val();
        },
        allowClear: true,
        minimumInputLength:1
    });

    $("#barcodeNo").select2({
        placeholder: "Enter specific barcode",
        allowClear: true,
        ajax: {

            url: Routing.generate('inventory_purchaseitem_search'),
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
        formatResult: function(item){ return item.text +'(' +item.item_name+')'}, // omitted for brevity, see the source of this page
        formatSelection: function(item){return item.text +'(' +item.item_name+')' }, // omitted for brevity, see the source of this page
        initSelection: function(element, callback) {
            var id = $(element).val();
        },
        allowClear: true,
        minimumInputLength:1
    });


}

