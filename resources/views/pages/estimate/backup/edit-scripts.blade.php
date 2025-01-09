<script>
    // global variable
    var cartList = [];
    var pos_product = {};

    var empty = '';

    var productListDOM = $("#tbody");
    var appendedDom = '';


    var localData = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];

    function empty_field_check(placeholder) {
        if (placeholder == null) {
            placeholder = 0;
        } else if (placeholder.trim() == "") {
            placeholder = 0;
        }
        return placeholder;
    }

    // poroduct is exists in localdata
    function pExist(pid) {
        let ldata = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];
        return ldata.some(function(el) {
            return el.pId === pid
        });
    }

    function addProductToCard(product) {

        sotoredata(product);
        var x = 0;
        domPrepend(product, x++);
        totalCalculate();
    }

    $('#id_code').blur();
    //
    // $(document).on('submit', '#sale-manage-form', function (e) {
    //     e.preventDefault();
    //     // localStorage.clear();
    //
    //     $('#sale-manage-form').submit();
    // });

    $('#submit-btn').click(function(e) {
        e.preventDefault();
        // alert("Hello");
        localStorage.clear();
        $('#sale-manage-form').submit();
    });

    $(document).on('submit', '#scan_code', function(e) {
        e.preventDefault();

        let url = $(this).attr('action');
        $.ajax({
            url: url,
            method: $(this).attr('method'),
            data: $(this).serialize(),
            success: function(data) {
                $("#scan_code")[0].reset();
                if (product) {

                    // check stock
                    if (product.checkSaleOverStock == 0) {
                        if (product.stock <= 0) {
                            toastr.warning(
                                'This product is Stock out. Please Purchases the Product.');
                            return false;
                        }
                    }

                    addProductToCard(product);
                }
            }
        }); // Load Data to cart
    });

    function sotoredata(data) {
        if (localStorage.getItem('pos-items') != null) {
            cartList = JSON.parse(localStorage.getItem('pos-items'))
            cartList.push(data);
        } else {
            cartList.push(data);
        }
        localStorage.setItem('pos-items', JSON.stringify(cartList));
    }

    $(document).on('click', '.remove-btn', function() {
        let itemIndex = $(this).attr('data-value');
        localData.splice(itemIndex, 1);
        localStorage.removeItem('pos-items');
        localStorage.setItem('pos-items', JSON.stringify(localData))
        $(this).parents('tr').remove();
        totalCalculate();
    });

    $("#clearList").on('click', function() {
        localStorage.removeItem('pos-items');
        $("#tbody").html(empty);
        totalCalculate();
    });

    function showList() {
        localData.forEach((item, index) => {
            domPrepend(item, index);
        });
    }

    function domPrepend(product = null, index = null) {
        var name = product.name;
        var quantity_data = '';

        if (product.sub_unit == null) {
            quantity_data =
                `<input type="text" class="has_sub_unit" hidden value="false">
                    <label class="ml-2 mr-2">${product.main_unit.name}:</label>
                    <input type="number" value="" class="form-control col main_qty" name="main_qty[${product.id}]" data-value="" data-related="${product.main_unit.related_by}" onkeydown="return event.keyCode !== 190" min="1">`;
        } else {
            quantity_data =
                `<input type="text" class="has_sub_unit" hidden value="true">
                    <input type="text" class="conversion" hidden value="${product.main_unit.related_by}">
                    <label class="mr-1 ml-1">${product.main_unit.name}:</label>
                    <input type="number" value="" class="form-control col main_qty mr-1" name="main_qty[${product.id}]" data-value="" data-related="${product.main_unit.related_by}" onkeydown="return event.keyCode !== 190" min="1">
                    <label class="mr-1">${product.sub_unit.name}:</label>
                    <input type="number" value="" class="form-control col sub_qty mr-1" name="sub_qty[${product.id}]"  onkeydown="return event.keyCode !== 190" min="1" max="${product.main_unit.related_by-1}">`;
        }
        let dom = `
              <tr>
                <td>
                  ${product.name + " - " + product.code}
                  <input type="hidden" class="name" value="${name.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '')}" name="name[]" />
                  <input type="hidden" value="${product.id}" name="product_id[]" />
                </td>
                <td style="width:100px">
                    <div class="form-row">
                        ${quantity_data}
                    </div>
                </td>
                <td style="width:100px">
                  <input type="text" value="${product.price}" class="form-control rate" name="rate[]" readonly/>
                </td>          
                <td style="width:150px">
                  <input type="text" name="sub_total[]" class="form-control sub_total" value="${product.price}"/>
                </td>
                <td>
                  <a href="#" class="remove-btn item-index" data-value="${index}"><i class="fa fa-trash"></i></a>
                </td>
              </tr>
         `;
        $("#tbody").prepend(dom);
    }

    function to_sub_unit(main_val, sub_val, related_by) {
        return (main_val * related_by) + sub_val;
    }

    function convert_to_main_and_sub(quantity, related_by) {
        var main_qty = 0;
        var main_qty_as_sub = 0;
        var sub_qty = 0;

        if (quantity != 0) {
            main_qty = parseInt(quantity / related_by);
            main_qty_as_sub = main_qty * related_by;
            sub_qty = quantity - main_qty_as_sub;
        }
        return {
            'main_qty': main_qty,
            'sub_qty': sub_qty
        };
    }

    function calculate_sub_total(main_qty, sub_qty, unit_price, related_by, has_sub_unit) {
        var sub_unit_price = 0;
        if (has_sub_unit == "true" && related_by != 0) {
            sub_unit_price = parseFloat(unit_price / related_by);
        }
        var main_price = main_qty * unit_price;
        var sub_price = sub_qty * sub_unit_price;
        return parseFloat(main_price + sub_price).toFixed(2);
    }

    function old_quantity(obj, has_sub_unit, related_by) {
        let old_main = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').attr('data-old')));
        let returned = parseInt(empty_field_check(obj.parents('tr').find('.returned').attr('data-returned')));
        let damage = parseInt(empty_field_check(obj.parents('tr').find('.damage').attr('data-damage')));
        old_main = old_main - returned - damage;
        let old_sub = 0;
        if (has_sub_unit == "true") {
            old_sub = parseInt(empty_field_check(obj.parents('tr').find('.sub_qty').attr('data-old')))
        }
        return to_sub_unit(old_main, old_sub, related_by);
    }

    function handle_change(obj) {
        var old_main = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').val()));
        var old_sub = parseInt(empty_field_check(obj.parents('tr').find('.sub_qty').val()));
        var main_val = parseInt(empty_field_check(obj.parents('tr').find('.returned').val()));
        var sub_val = parseInt(empty_field_check(obj.parents('tr').find('.returned_sub_unit').val()));
        var damage_value = parseInt(empty_field_check(obj.parents('tr').find('#damageValue').val()));
        var damage_qty = parseInt(empty_field_check(obj.parents('tr').find('.damage').val()));
        let related_by = parseInt(empty_field_check(obj.parents('tr').find('.returned').attr('data-related')));
        var has_sub_unit = obj.parents('tr').find('.has_sub_unit').val();
        var oldSubTotal = parseFloat(obj.parents('tr').find('.subtotal_holder').val());
        var return_value = 0;
        let converted_sub = to_sub_unit(main_val, sub_val, related_by, has_sub_unit);
        let stock = obj.parents('tr').find('.main_qty').attr('data-value');
        
        if (main_val > (old_main-damage_qty)) {
            toastr.warning('Returned Quantity is greater than sold quantity.');
            obj.parents('tr').find('.returned').val(0);
            main_val = 0;
            return false;
        }

        // if(sub_val>(related_by+old_sub)){
        //     toastr.warning('Returned Quantity is greater than sold quantity.');
        //     obj.parents('tr').find('.returned_sub_unit').val(0);
        //     sub_val = 0;
        // }
        // if((main_val*related_by+sub_val)>(related_by*old_main+old_sub)){
        //     toastr.warning('Returned Quantity is greater than sold quantity.');
        //     obj.parents('tr').find('.returned_sub_unit').val(0);
        //     obj.parents('tr').find('.returned').val(0);
        //     main_val = 0;
        //     sub_val = 0;
        // }

        if (stock < converted_sub) {
            var converted;
            if (has_sub_unit == "true") {
                converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
                obj.parents('tr').find('.main_qty').val(converted.main_qty);
                obj.parents('tr').find('.sub_qty').val(converted.sub_qty);
            } else {
                converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
                obj.parents('tr').find('.main_qty').val(converted.main_qty);
            }
            let price = obj.parents('tr').find('.rate').val();
            price = parseFloat(price);
            let subTotal = calculate_sub_total(converted.main_qty, converted.sub_qty, price, related_by, has_sub_unit);
            let updatedSubTotal = oldSubTotal - subTotal;
            obj.parents('tr').find('.sub_total').val(updatedSubTotal);
            totalCalculate();
            toastr.warning('Not Enough Stock.');
        } else {
            let price = obj.parents('tr').find('.rate').val();
            price = parseFloat(price);
            let subTotal = calculate_sub_total(main_val, sub_val, price, related_by, has_sub_unit);
            let return_value = ((price * main_val) + ((price / related_by) * sub_val));
            let updatedSubTotal = oldSubTotal - subTotal - damage_value;
            obj.parents('tr').find('.sub_total').val(updatedSubTotal);
            obj.parents('tr').find('.returnValue').val(return_value);
            totalCalculate();
        }
    }

    // main_qty
    $(document).on('keyup', '.main_qty', function(e) {
        var main_val = parseInt(empty_field_check($(this).val()));
        var sub_val = parseInt(empty_field_check($(this).parents('tr').find('.sub_qty').val()));
        var returned_qty = parseInt(empty_field_check($(this).parents('tr').find('.returned').val()));
        var returned_value = parseInt(empty_field_check($(this).parents('tr').find('.returnValue').val()));
        var damage_qty = parseInt(empty_field_check($(this).parents('tr').find('.damage').val()));
        var damage_value = parseInt(empty_field_check($(this).parents('tr').find('#damageValue').val()));
        let related_by = parseInt(empty_field_check($(this).attr('data-related')));
        var has_sub_unit = $(this).parents('tr').find('.has_sub_unit').val();
        let converted_sub = to_sub_unit(main_val, sub_val, related_by ,has_sub_unit);

        let stock = $(this).attr('data-value');
        if (main_val < returned_qty+damage_qty) {
            toastr.warning('Returned Quantity is greater than ordered quantity.');
            $(this).parents('tr').find('.returned').val(0);
            main_val = 0;
        }

        if (stock < converted_sub) {
            var converted;
            if (has_sub_unit == "true") {
                converted = convert_to_main_and_sub(stock, related_by);
                $(this).val(converted.main_qty);
                $(this).parents('tr').find('.sub_qty').val(converted.sub_qty);
            } else {
                converted = convert_to_main_and_sub(stock, related_by);
                $(this).val(converted.main_qty);
            }

            let price = $(this).parents('tr').find('.rate').val();
            price = parseFloat(price);
            let subTotal = calculate_sub_total(converted.main_qty, converted.sub_qty, price, related_by, has_sub_unit);
            subTotal = parseFloat(subTotal)-returned_value-damage_value;
            $(this).parents('tr').find('.sub_total').val(subTotal);
            $(this).parents('tr').find('.sub_total_holder').val(subTotal);
            totalCalculate();

        } else {
            let price = $(this).parents('tr').find('.rate').val();
            price = parseFloat(price);
            let subTotal = calculate_sub_total(main_val, sub_val, price, related_by, has_sub_unit);
            subTotal = parseFloat(subTotal) - returned_value - damage_value;
            $(this).parents('tr').find('.sub_total').val(subTotal);
            $(this).parents('tr').find('.subtotal_holder').val(subTotal);

            totalCalculate();
        }
    });

    //subunit 
    $(document).on('keyup', '.sub_qty', function(e) {
        var main_val = parseInt(empty_field_check($(this).parents('tr').find('.main_qty').val()));
        var sub_val = parseInt(empty_field_check($(this).val()));
        var returned_qty = parseInt(empty_field_check($(this).parents('tr').find('.returned').val()));
        var returned_value = parseInt(empty_field_check($(this).parents('tr').find('.returnValue').val()));
        var damage_qty = parseInt(empty_field_check($(this).parents('tr').find('.damage').val()));
        var damage_value = parseInt(empty_field_check($(this).parents('tr').find('#damageValue').val()));
        let related_by = parseInt(empty_field_check($(this).parents('tr').find('.returned').attr('data-related')));
        var has_sub_unit = $(this).parents('tr').find('.has_sub_unit').val();
        let converted_sub = to_sub_unit(main_val, sub_val, related_by, has_sub_unit);

        let stock = $(this).parents('tr').find('.main_qty').attr('data-value');
        if (main_val < returned_qty+damage_qty) {
            toastr.warning('Returned Quantity is greater than ordered quantity.');
            $(this).parents('tr').find('.returned').val(0);
            main_val = 0;
        }

        if (stock < converted_sub) {
            var converted;
            if (has_sub_unit == "true") {
                converted = convert_to_main_and_sub(stock, related_by);
                $(this).parents('tr').find('.main_qty').val(converted.main_qty);
                $(this).val(converted.sub_qty);
            } else {
                converted = convert_to_main_and_sub(stock, related_by);
                $(this).val(converted.main_qty);
            }

            let price = $(this).parents('tr').find('.rate').val();
            price = parseFloat(price);
            let subTotal = calculate_sub_total(converted.main_qty, converted.sub_qty, price, related_by, has_sub_unit);
            subTotal = parseFloat(subTotal)-returned_value-damage_value;
            $(this).parents('tr').find('.sub_total').val(subTotal);
            $(this).parents('tr').find('.subtotal_holder').val(subTotal);
            totalCalculate();

        } else {
            let price = $(this).parents('tr').find('.rate').val();
            price = parseFloat(price);
            let subTotal = calculate_sub_total(main_val, sub_val, price, related_by, has_sub_unit);
            subTotal = parseFloat(subTotal) - returned_value - damage_value;
            $(this).parents('tr').find('.sub_total').val(subTotal);
            $(this).parents('tr').find('.subtotal_holder').val(subTotal);

            totalCalculate();
        }
    });
    

    //on keyup return 
    $(document).on('keyup', '.returned', function(e) {
        handle_change($(this));
    });

    //on keyup return 
    $(document).on('keyup', '.returned', function(e) {
        handle_change($(this));
    });

    //sub_qty change
    $(document).on('keyup', '.returned_sub_unit', function(e) {
        handle_change($(this));
    });

    //on keyup #damageValue
    $(document).on('keyup', '.damage', function(e) {
        var damage_qty = $(this).val();
        var damage_rate = $(this).parents('tr').find('.damage_rate').val();
        var return_value = $(this).parents('tr').find('#returnValue').val();
        var input_subtotal = $(this).parents('tr').find('.subtotal_holder').val();
        damage_value = parseFloat(damage_qty * damage_rate);
        return_value = parseFloat(return_value);
        var sub_total = parseFloat(input_subtotal - return_value - damage_value);
        $(this).parents('tr').find('.sub_total').val(sub_total.toFixed(3));
        $(this).parents('tr').find('#damageValue').val(damage_value);
        totalCalculate();
    });

    function totalCalculate() {
        let subTotalList = document.querySelectorAll('.sub_total');
        let qtyList = document.querySelectorAll('.qty');
        // alert(subTotalList);
        let total = 0;
        let totalQty = 0;
        $.each(subTotalList, (index, value) => {
            total += parseFloat(value.value);
        });
        $("#totalAmount").text(total);
        $("#total_input_Amount").val(total);
        $("#receivable").text(total);
        $("#after_discount").text(total);
        $("#receivable_input").val(total);

        $.each(qtyList, (index, value) => {
            totalQty += parseInt(value.value);
        });
        
        $(".totalQty").text(totalQty);
        $("#items").text(totalQty);
    }

    showList();
    totalCalculate();
</script>