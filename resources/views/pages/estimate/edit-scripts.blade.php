<script>
    $(document).ready(function () {
        function empty_field_check(value) {
            return (value == null || value.trim() === "") ? 0 : parseInt(value);
        }
        function calculate_sub_total(main_qty, sub_qty, unit_price, related_by, has_sub_unit) {
            let sub_unit_price = has_sub_unit ? parseFloat(unit_price / related_by) : 0;
            return parseFloat((main_qty * unit_price) + (sub_qty * sub_unit_price)).toFixed(2);
        }
        function updateRowTotals(row) {
            const unit_price = parseFloat(row.find(".rate").val()) || 0;
            const related_by = parseInt(row.find(".returned").data("related")) || 1;
            const has_sub_unit = row.find(".sub_qty").length > 0;

            const main_qty = empty_field_check(row.find(".main_qty").val());
            const sub_qty = empty_field_check(row.find(".sub_qty").val());
            const returned_main = empty_field_check(row.find(".returned").val());
            const returned_sub = empty_field_check(row.find(".returned_sub_unit").val());
            const damage_sub = empty_field_check(row.find(".damage").val());

            const ordered_qty = (main_qty * related_by) + sub_qty;
            const returned_qty = (returned_main * related_by) + returned_sub;
            const final_qty = ordered_qty - returned_qty - damage_sub;

            if (final_qty < 0) {
                toastr.warning("Returned & Damaged Qty Cannot Exceed Ordered Qty.");
                row.find(".returned").val(0);
                row.find(".returned_sub_unit").val(0);
                row.find(".damage").val(0);
                return updateRowTotals(row);
            }

            const sub_total = calculate_sub_total(final_qty, 0, unit_price, related_by, has_sub_unit);
            const damage_price = parseFloat(row.find(".rate").val()) || 0;
            const damaged_value = (damage_sub * damage_price).toFixed(2);

            row.find(".sub_total").val(sub_total);
            row.find(".damage_value").val(damaged_value);
        }

        function updateTotals() {
            let total_amount = 0;

            $("#tbody tr").each(function () {
                const row = $(this);
                updateRowTotals(row);

                const sub_total = parseFloat(row.find(".sub_total").val()) || 0;
                total_amount += sub_total;
            });

            $("#totalAmount").text(total_amount.toFixed(2));
        }

        $(document).on("keyup change", ".returned, .damage", function () {
            const row = $(this).closest("tr");
            let ordered_qty = empty_field_check(row.find(".main_qty").val()) || 0;
            let returned_qty = empty_field_check(row.find(".returned").val()) || 0;
            let damage_qty = empty_field_check(row.find(".damage").val()) || 0;

            if ((returned_qty + damage_qty) > ordered_qty) {
                toastr.warning("Returned & Damaged Quantity Cannot Exceed Ordered Quantity.");
                row.find(".returned").val(0);
                row.find(".damage").val(0);
                updateRowTotals(row);
            }
        });

        updateTotals();
    });
</script>