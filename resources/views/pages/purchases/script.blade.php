<script>
    var count = {{ isset($purchase) ? $purchase->items()->count() : 0 }};
    var db_pid_array;
    console.log(db_pid_array)

    app.ready(function() {

        $("#product_search").autocomplete({
            source: function(req, res) {
                
                let url = "{{ route('product-search') }}";
                let brand_id = $('#supplier').val();
                if (!brand_id) {
                    toastr.warning('Please select a supplier first.');
                    return;
                }
                // $.get(url, {
                //     req: req.term
                // }, (data) => {
                //     res($.map(data, function(item) {
                //         return {
                //             id: item.id,
                //             value: item.name,
                //             price: item.price
                //         }
                //     })); // end res
                // });
                $.get(url, {
                    req: req.term,
                    brand_id: brand_id // pass supplier_id as brand_id
                }, (data) => {
                    res($.map(data, function(item) {
                        return {
                            id: item.id,
                            value: item.name,
                            price: item.price
                        }
                    }));
                });
            },
            
            select: function(event, ui) {
                //check is supplier is selected.
                if ($("#supplier").val() == '') {
                    toastr.warning('Please Select Supplier First !');
                    $(this).val('');
                    return false;
                }
                if (db_pid_array && db_pid_array.includes(ui.item.id)) {
                    //SHOW ERROR
                    toastr.warning('Please Increase the quantity.');
                    return;
                }
                count++;
                $(this).val(ui.item.value);
                $("#search_product_id").val(ui.item.id);
                let url = "{{ route('product.details', 'my_id') }}".replace('my_id', ui.item.id);
                $.get(url, (product) => {
                    // product add
                    var quantity_data = '';

                    if (product.sub_unit == null) {
                        quantity_data =
                            `<input type="text" class="has_sub_unit" hidden value="false">
                    <label class="ml-4 mr-2">${product.main_unit.name}:</label>
                    <input type="number" value="" class="form-control col main_qty" name="new_main_qty[${product.id}]"  onkeydown="return event.keyCode !== 190" min="1">`;
                    } else {
                        quantity_data =
                            `<input type="text" class="has_sub_unit" hidden value="true">
                    <input type="text" class="conversion" hidden value="${product.main_unit.related_by}">
                    <label class="mr-2 ml-4">${product.main_unit.name}:</label>
                    <input type="number" value="" class="form-control col main_qty mr-4" name="new_main_qty[${product.id}]"  onkeydown="return event.keyCode !== 190" min="1">
                    <label class="mr-2">${product.sub_unit.name}:</label>
                    <input type="number" value="" class="form-control col sub_qty" name="new_sub_qty[${product.id}]"  onkeydown="return event.keyCode !== 190" min="1">`;
                    }
                    // <input type="number" value="1" class="form-control sale_qty" name="qty[]">
                    let row = `
              <tr>
                <td>${count}</td>
                <td>
                ${product.name + " - " + product.code}
                <input type="hidden" value="${product.id}" name="new_product[${product.id}]" class="product">
                 <input type="hidden" value="${product.name}" name="product_name[${product.id}]" />
                </td>
                <td style="width:150px">
                  <input type="text" value="${product.cost}" class="form-control rate" name="new_rate[${product.id}]">
                </td>
                <td class="pr-3">
                    <div class="form-row">
                        ${quantity_data}
                    </div>
                </td>
                <td class="d-none">
                    <input hidden type="text" value="" class="form-control discount_qty" name="discount_qty[${product.id}]">
                </td>
                <td>
                  <strong><span class="sub_total">0</span> Tk</strong>
                  <input type="hidden" name="new_subtotal_input[${product.id}]" class="subtotal_input" value="0">
                </td>
                <td>
                  <a href="#" class="remove">
                    <i class="fa fa-trash"></i>
                  </a>
                </td>
              </tr>
        `;
                    // Duplicate check
                    let tableBody = document.querySelector('#table_body');
                    let products = tableBody.querySelectorAll('.product');
                    let isDuplicate = false;
                    products.forEach(function(item) {
                        if (ui.item.id == item.value) {
                            isDuplicate = true;
                        } else {
                            isDuplicate = false;
                        }
                    });

                    if (isDuplicate) {
                        toastr.warning('Please Increase the quantity.');
                    } else {
                        $("#table_body").append(row);
                    }
                    itemUpdate();
                    updateTotalQty();
                    // Update Total
                    updateTotal();
                });
                $(this).val('');

                return false;
            },
            response: function(event, ui) {
                if (ui.content.length == 1) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                }
            },
            minLength: 0
        });
    });

    function calculate_sub_total(obj) {
        var has_sub_unit = $(obj).parents('tr').find('.has_sub_unit').val();
        var rate = $(obj).parents('tr').find('.rate').val();
        var sub_total = parseFloat($(obj).parents('tr').find('.main_qty').val() * rate);
        if (has_sub_unit == "true") {
            sub_total += parseFloat(($(obj).parents('tr').find('.sub_qty').val() / $(obj).parents('tr').find(
                '.conversion').val()) * rate);
        } else {

        }
        // $(obj).val() * $(obj).parents('tr').find('.sale_qty').val();
        Math.round(sub_total);
        return sub_total;
    }

    // change rate
    $(document).on('change', '.rate', function() {
        let subTotal = calculate_sub_total($(this));
        Math.round(subTotal);
        $(this).parents('tr').children('td').find('.sub_total').text(subTotal);
        $(this).parents('tr').children('td').find('.subtotal_input').val(subTotal);
        updateTotalQty();
        updateTotal();
    });

    // change qty
    $(document).on('change', '.main_qty', function() {
        // let rate = $(this).parents('tr').find('.rate').val();
        let subTotal = calculate_sub_total($(this));
        Math.round(subTotal);
        $(this).parents('tr').children('td').find('.sub_total').text(subTotal);
        $(this).parents('tr').children('td').find('.subtotal_input').val(subTotal);
        updateTotalQty();
        updateTotal();
    });

    $(document).on('change', '.sub_qty', function() {
        // let rate = $(this).parents('tr').find('.rate').val();
        let subTotal = calculate_sub_total($(this));
        Math.round(subTotal);
        $(this).parents('tr').children('td').find('.sub_total').text(subTotal);
        $(this).parents('tr').children('td').find('.subtotal_input').val(subTotal);
        updateTotalQty();
        updateTotal();
    });

    // Remove DOM
    $(document).on('click', '.remove', function() {
        $(this).parents('tr').remove();
        updateTotal();
        updateTotalQty();
        itemUpdate();
        count--;
    });

    // update total qty function
    function updateTotalQty() {
        let totalQty = 0;
        $(".sale_qty").each((i, obj) => {
            let qty_val = parseInt(obj.value);
            totalQty += qty_val;
        });
        $("#total_qty").text(totalQty);
    }

    //  Update item function
    function itemUpdate() {
        let totalItems = 0
        $(".sale_qty").each((i, obj) => {
            totalItems += 1;
        });
        $("#total_items").text(totalItems);
    }

    // update total amount
    function updateTotal() {
        let totalAmount = 0;
        $(".sub_total").each((i, obj) => {
            let subtotal = obj.innerHTML;
            totalAmount += parseFloat(subtotal);
        });
        Math.round(totalAmount);
        $("#total").text(totalAmount)
    }

    // payment
    $("#payment_btn").click(function() {
        let divLength = $(".product").length;
        // if purches list is empty
        if (divLength == 0) {
            toastr.warning('Please Select Product. Thanks !');
            return;
        }

        let total = $("#total").text();
        // total.toFixed(3);
        let totalItems = $("#total_items").text();
        $("#items").text(totalItems);

        $("#payable").text(total);
        $("#payable_input").val(total);
        // init due all amount
        $("#due").text(total);
        $("#due_input").val(total);
        $("#payment-modal").modal('show');

    });

    // Due Calcualation
    $("#pay_amount").change(function() {
        let payAmount = 0
        if ($(this).val() != '') {
            payAmount = parseFloat($(this).val());
        }

        let payAbleAmount = parseFloat($("#total").text());
        let due = payAbleAmount - payAmount;
        $("#due").text(due);
        $("#due_input").val(due);
    });

    // click paid button
    $("#paid_btn").click(function() {
        let payAbleAmount = parseFloat($("#total").text());

        $("#pay_amount").val(payAbleAmount);

        $("#due").text(0);
        $("#due_input").val(0);
    });
</script>
