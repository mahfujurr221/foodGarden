<script>
    $(document).ready(function () {
        var cartList = [];
        var localData = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];

        // Load Existing Order Data
        function loadOrderData() {
            localData.forEach((item, index) => {
                domPrepend(item, index);
            });
        }

        // Validate Empty Fields
        function empty_field_check(value) {
            return (value == null || value.trim() === "") ? 0 : value;
        }

        // Add Product to Cart
        function addProductToCard(product) {
            if (pExist(product.id)) {
                toastr.warning('Product is already in the cart.');
                return;
            }
            storeData(product);
            domPrepend(product);
            updateTotals();
        }

        function pExist(productId) {
            return localData.some(function (el) { return el.pId === productId; });
        }

        function storeData(data) {
            cartList.push(data);
            localStorage.setItem('pos-items', JSON.stringify(cartList));
        }

        function domPrepend(product, index) {
            var quantity_data = product.sub_unit == null ?
                `<label>${product.main_unit.name}:</label>
                 <input type="number" value="1" class="form-control main_qty" name="main_qty[${product.id}]" min="1">`
                :
                `<label>${product.main_unit.name}:</label>
                 <input type="number" value="1" class="form-control main_qty" name="main_qty[${product.id}]" min="1">
                 <label>${product.sub_unit.name}:</label>
                 <input type="number" value="0" class="form-control sub_qty" name="sub_qty[${product.id}]" min="0">`;

            let dom = `
                <tr>
                    <td>${product.name} - ${product.code}
                        <input type="hidden" value="${product.id}" name="product_id[]" />
                    </td>
                    <td style="width:100px">
                        ${quantity_data}
                    </td>
                    <td style="width:100px">
                        <input type="text" value="${product.price}" class="form-control rate" name="rate[]" readonly/>
                    </td>
                    <td style="width:150px">
                        <input type="text" name="sub_total[]" class="form-control sub_total" value="${product.price}" readonly />
                    </td>
                    <td>
                        <a href="#" class="remove-btn item-index" data-value="${index}">
                            <i class="fa fa-trash text-danger"></i>
                        </a>
                    </td>
                </tr>
            `;
            $("#tbody").prepend(dom);
        }

        // Calculate Sub Total
        function updateRowTotals(row) {
            let unit_price = parseFloat(row.find(".rate").val()) || 0;
            let main_qty = parseInt(empty_field_check(row.find(".main_qty").val())) || 0;
            let sub_qty = parseInt(empty_field_check(row.find(".sub_qty").val())) || 0;

            let sub_total = parseFloat(main_qty * unit_price).toFixed(2);
            row.find(".sub_total").val(sub_total);
        }

        // Update Total Amount
        function updateTotals() {
            let total_amount = 0;
            $("#tbody tr").each(function () {
                updateRowTotals($(this));
                total_amount += parseFloat($(this).find(".sub_total").val()) || 0;
            });
            $("#totalAmount").text(total_amount.toFixed(2));
            $("#total_input_Amount").val(total_amount.toFixed(2));
        }

        // Remove Item from Cart
        $(document).on('click', '.remove-btn', function () {
            let itemIndex = $(this).attr('data-value');
            localData.splice(itemIndex, 1);
            localStorage.setItem('pos-items', JSON.stringify(localData));
            $(this).parents('tr').remove();
            updateTotals();
        });

        // Prevent Returning More than Ordered
        $(document).on("keyup change", ".returned, .damage", function () {
            let row = $(this).closest("tr");
            let ordered_qty = parseInt(empty_field_check(row.find(".main_qty").val())) || 0;
            let returned_qty = parseInt(empty_field_check(row.find(".returned").val())) || 0;
            let damage_qty = parseInt(empty_field_check(row.find(".damage").val())) || 0;

            if ((returned_qty + damage_qty) > ordered_qty) {
                toastr.warning("Returned & Damaged Quantity Cannot Exceed Ordered Quantity.");
                row.find(".returned").val(0);
                row.find(".damage").val(0);
                updateRowTotals(row);
            }
        });

        // Submit Order via AJAX
        $(document).on('submit', '#sale-manage-form', function (e) {
            e.preventDefault();
            let formData = $(this).serialize();
            let formAction = $(this).attr('action');

            $.ajax({
                url: formAction,
                method: 'PUT',
                data: formData,
                success: function (response) {
                    toastr.success("Order Updated Successfully!");
                    localStorage.clear();
                    setTimeout(() => window.location.reload(), 1500);
                },
                error: function (error) {
                    toastr.error("Error Updating Order!");
                }
            });
        });

        // Trigger Updates on Input Changes
        $(document).on("keyup change", ".main_qty, .sub_qty", function () {
            updateRowTotals($(this).closest("tr"));
            updateTotals();
        });

        loadOrderData();
        updateTotals();
    });
</script>
