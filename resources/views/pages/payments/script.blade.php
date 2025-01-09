<script>
     // $("#account_type").change(function () {
     //      $("#details").hide(500);
     //      let aType = $(this).val();
     //      let selectTag = $("#account_id");
     //      let options = `
     //           <option value="">Select Account</option>
     //      `;
     //      if(aType == 'customer') {
     //           $("#account_id").attr('disabled', false);
     //           url = "{{ route('get_customers') }}";
     //           $.get(url, (customers) => {
     //                customers.forEach((value) => {
     //                    //console.log(value);
     //                     options += `<option value="${value.id}">${value.shop_name+" - "+value.name+" - "+value.address_name+"-"+value.phone}</option>`;
     //                });
     //                $("#account_id").addClass('customer');
     //                $("#account_id").removeClass('supplier');
     //                selectTag.html(options);
     //           });
     //      }

     //      if(aType == 'supplier') {
     //           $("#account_id").attr('disabled', false);
     //            url = "{{ route('get_suppliers') }}";
     //           $.get(url, (suppliers) => {
     //                suppliers.forEach((value) => {
     //                    options += `<option value="${value.id}">${value.name+" - "+value.phone}</option>`;
     //                });

     //           $("#account_id").addClass('supplier');
     //           $("#account_id").removeClass('customer');

     //                selectTag.html(options);
     //           });
     //      }

     //      if(aType == '') {
     //           $("#account_id").attr('disabled', true);
     //           $("#account_id").removeClass('customer');
     //           $("#account_id").removeClass('supplier');
     //      }
     // });

     // $("#account_id").attr('disabled', true);
     // $("#account_id").removeClass('customer');
     // $("#account_id").removeClass('supplier');


     // change customer
     $(document).on('change', '.customer', function () {
          let customer_id = $(this).val();
          let url = "{{ route('customer_due', 'id') }}".replace('id', customer_id);
          $.get(url, data => {
               $("#details").show(500);
               $("#account_name").text(data.customer_name);
               $("#due_invoice").text(data.due_invoice);
               $("#total_invoice_due").text(data.sell_due);
               // $("#amount").attr('max', data.total_due);
               $("#wallet_balance").text(data.walletBalance);
               $("#total_due").text(data.total_due);

                if(data.sell_due>0){
                    $("#id_hint").html('*** বিক্রয় বাবদ পাওনা আছে '+Math.abs(data.sell_due)+' Tk ***');
                }else{
                    $("#id_hint").html('*** বিক্রয়ের থেকে অতিরিক্ত পেমেন্ট নেয়া হয়েছে '+Math.abs(data.sell_due)+' Tk ***');
                }

               if(data.walletBalance>=0){
                    $("#wb_hint").html('**** কাস্টমারের ওয়ালেটে জমা আছেঃ '+Math.abs(data.walletBalance)+'Tk ****');
               }else{
                    $("#wb_hint").html('**** কাস্টমারের কাছে পাওনা আছেঃ '+Math.abs(data.walletBalance)+'Tk****');
               }

          });
     });

     $(document).on('change', '.supplier', function () {
          let supplier_id = $(this).val();
          let url = "{{ route('supplier_due', 'id') }}".replace('id', supplier_id);
          $.get(url, data => {
               $("#details").show(500);
               $("#account_name").text(data.supplier_name);
               $("#due_invoice").text(data.due_invoice);
               $("#total_invoice_due").text(data.purchase_due);
               $("#wallet_balance").text(data.walletBalance);
               $("#total_due").text(data.total_due);

               if(data.purchase_due>0){
                    $("#id_hint").html('*** ক্রয় বাবদ দেনা আছে '+Math.abs(data.purchase_due)+' Tk ***');
                }else{
                    $("#id_hint").html('*** ক্রয়ের থেকে অতিরিক্ত পেমেন্ট দেয়া হয়েছে '+Math.abs(data.purchase_due)+' Tk ***');
                }

               // $("#amount").attr('max', data.total_due);
               if(data.walletBalance>=0){
                    $("#wb_hint").html('**** সাপ্লাইয়ারের কাছে জমা আছেঃ '+Math.abs(data.walletBalance)+'Tk ****');
               }else{
                    $("#wb_hint").html('**** আপনার থেকে সাপ্লাইয়ার পাবেঃ '+Math.abs(data.walletBalance)+'Tk ****');
               }
          });
     });

     $("#account_id").change(function(){
          if($(this).val() == '') {
               $("#details").hide(500);
          }
     })

     $("#details").hide();
</script>
