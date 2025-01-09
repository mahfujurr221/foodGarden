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
    var main_qty = Math.floor(quantity / related_by);
    var sub_qty = quantity % related_by;
    return {
        main_qty: main_qty,
        sub_qty: sub_qty,
    };
}

function calculate_sub_total(main_qty, sub_qty, unit_price, related_by, has_sub_unit) {
    let sub_unit_price = 0;
    if (has_sub_unit === "true" && related_by > 0) {
        sub_unit_price = parseFloat(unit_price / related_by); // Price per sub-unit
    }

    let main_price = main_qty * unit_price; // Price for main units
    let sub_price = sub_qty * sub_unit_price; // Price for sub-units
    return parseFloat(main_price + sub_price).toFixed(2);
}

function old_quantity(row, has_sub_unit, related_by) {
    let old_main = parseInt(empty_field_check(row.find(".main_qty").attr("data-old"))) || 0;
    let returned = parseInt(empty_field_check(row.find(".returned").attr("data-returned"))) || 0;
    let damage = parseInt(empty_field_check(row.find(".damage").attr("data-damage"))) || 0;

    old_main -= (returned + damage); // Adjust for returned and damaged quantities
    let old_sub = 0;

    if (has_sub_unit === "true") {
        old_sub = parseInt(empty_field_check(row.find(".sub_qty").attr("data-old"))) || 0;
    }

    return to_sub_unit(old_main, old_sub, related_by); // Total in sub-units
}

function updateRowTotals(row) {
    const unit_price = parseFloat(row.find(".rate").val()) || 0; // Price per unit
    const related_by = parseInt(row.find(".returned").data("related")) || 1; // Conversion factor
    const has_sub_unit = row.find(".has_sub_unit").val() === "true";

    const main_qty = parseInt(empty_field_check(row.find(".main_qty").val())) || 0;
    const sub_qty = parseInt(empty_field_check(row.find(".sub_qty").val())) || 0;
    const returned_main = parseInt(empty_field_check(row.find(".returned").val())) || 0;
    const returned_sub = parseInt(empty_field_check(row.find(".returned_sub_unit").val())) || 0;
    const damage_sub = parseInt(empty_field_check(row.find(".damage").val())) || 0; // Damage in sub-units

    // Convert quantities to sub-units
    const ordered_qty = to_sub_unit(main_qty, sub_qty, related_by);
    const returned_qty = to_sub_unit(returned_main, returned_sub, related_by);
    const final_qty = ordered_qty - returned_qty - damage_sub;

    if (final_qty < 0) {
        toastr.warning("Returned and damaged quantities cannot exceed the ordered quantity.");
        row.find(".returned").val(0);
        row.find(".returned_sub_unit").val(0);
        row.find(".damage").val(0);
        return updateRowTotals(row); // Recalculate with corrected values
    }

    // Monetary calculations
    const sub_total = calculate_sub_total(final_qty, 0, unit_price, related_by, has_sub_unit);
    const returned_value = calculate_sub_total(returned_main, returned_sub, unit_price, related_by, has_sub_unit);
    const damaged_value = calculate_sub_total(0, damage_sub, unit_price, related_by, has_sub_unit);

    // Update row values
    row.find(".sub_total").val(sub_total); // Subtotal for remaining quantity
    row.find(".returnValue").val(returned_value); // Value of returned items
    row.find("#damageValue").val(damaged_value); // Value of damaged items
}

function updateTotals() {
    let total_amount = 0;
    let total_qty = 0;

    $("#tbody tr").each(function () {
        const row = $(this);
        updateRowTotals(row);

        const sub_total = parseFloat(row.find(".sub_total").val()) || 0;
        const qty = parseInt(row.find(".main_qty").val()) || 0;

        total_amount += sub_total;
        total_qty += qty;
    });

    // Update total values in the UI
    $("#totalAmount").text(total_amount.toFixed(2));
    $("#total_input_Amount").val(total_amount.toFixed(2));
    $("#items").text(total_qty);
}

$(document).ready(function () {
    // Trigger updates on input changes
    $(document).on("keyup change", ".main_qty, .sub_qty, .returned, .returned_sub_unit, .damage", function () {
        const row = $(this).closest("tr");
        updateRowTotals(row);
        updateTotals();
    });

    // Initial calculation on page load
    updateTotals();
});


    showList();
    totalCalculate();
</script>