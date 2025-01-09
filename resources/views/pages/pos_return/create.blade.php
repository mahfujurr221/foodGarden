@extends('layouts.master')
@section('title', 'Return POS')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Return POS</strong>
        </h1>
    </div>
</header>
@endsection

@section('content')

<div class="col-md-4">
    <div class="card">
        <div class="card-header">
            <h3>Basic Details</h3>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <tbody>
                    <tr>
                        <th>Order Id:</th>
                        <td>{{ $pos->id }}</td>
                    </tr>

                    <tr>
                        <th>Customer Name:</th>
                        <td>{{ $pos->customer->name??'Walk-In Customer' }}</td>
                    </tr>

                    <tr>
                        <th>Product Price:</th>
                        <td class="order_product_price">{{ $actual_total=$pos->items()->sum('sub_total') }}</td>
                    </tr>

                    @php
                    $discount=0;
                    @endphp

                    {{-- @if($pos->discount!=null) --}}
                    {{-- <input type="text" name="pos_discount_field" class="pos_discount_field" value="{{ $pos->discount }}" hidden> --}}

                    {{-- @dd($pos->receivable) --}}

                    <tr>
                        <th>Discount</th>
                        <td class="discount">{{ $discount=$actual_total-$pos->receivable }}</td>
                    </tr>
                    {{-- @endif --}}

                    <tr>
                        <th>Total Receivable:</th>
                        <td class="pos_receivable">{{ $total_receivable=$pos->receivable }}</td>
                    </tr>

                    <tr>
                        <th>Total Paid:</th>
                        <td>
                            {{ $total_paid=$pos->paid }}
                            <input type="text" class="total_paid" value="{{ $total_paid }}" hidden>
                        </td>
                    </tr>

                    <tr>
                      <th>Due:</th>
                      <td>
                        <span class="due">
                          {{ $total_due=$pos->due }}
                        </span>
                      </td>
                    </tr>

                    <tr>
                        <th>Returned Product Value</th>
                        <td>
                            {{ $previous_returned_product_value=$pos->previous_returned_product_value() }}
                            <input type="text" class="previous_returned_product_value" value="{{ $previous_returned_product_value }}" hidden>
                        </td>
                    </tr>

                    {{-- <tr>
                        <th>Previous Returnable:</th>
                        <td>
                            {{ $previous_returnable=$pos->previous_returnable() }}
                            <input type="text" class="previous_returnable" value="{{ $previous_returnable }}" hidden>
                        </td>
                    </tr>

                    <tr>
                        <th>After Return Receivable</th>
                        <td>
                            {{ $total_receivable-$previous_returned_product_value }}
                        </td>
                    </tr> --}}






                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="card">
        <div class="card-header">
            <h3>Return Poduct</h3>
        </div>
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pos.return',$pos->id) }}" method="POST" class="return_form" onsubmit="return confirm('Are you Sure?')">
                @csrf

                <input type="text" name="pos_id" value="{{ $pos->id }}" hidden>

                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="max-width:10%;">No.</th>
                            <th>Name</th>
                            <th>Unit Price</th>
                            <th>Ordered Quantity</th>
                            <th>Return Quantity</th>
                            <th>Price</th>
                            {{-- <th>Return </th> --}}
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pos->items as $key=>$item)
                            @if($item->remaining_quantity()>0)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        {{ $item->product_name }}

                                        <input type="text" name="item_id[{{ $item->id }}]" value="{{ $item->id }}" hidden>
                                        <input type="text" name="product_id[{{ $item->id }}}]" value="{{ $item->product->id }}" hidden>
                                    </td>
                                    <th>
                                        <span class="unit_price">{{ $item->rate }}</span>
                                        <input type="text" name="unit_price[{{ $item->id }}]" value="{{ $item->rate }}" hidden>
                                    </th>
                                    <td style="max-width:10%;">{{ $item->easy_qty() }}</td>
                                    <td>
                                        <div class="form-row">
                                        @if(!$item->product->sub_unit)
                                                        {{-- ONLY MAIN UNIT --}}
                                            <input type="text" class="has_sub_unit" hidden value="false">
                                            <label class="ml-2 mr-2">{{ $item->product->main_unit->name }}:</label>
                                            <input type="number" value="{{ $item->remaining_main_sub()['main_qty'] }}" class="form-control col main_qty" name="main_qty[{{ $item->id }}]" data-old="{{ $item->main_unit_qty }}"  data-related="{{ $item->product->main_unit->related_by }}" onkeydown="return event.keyCode !== 190" min="1" max="{{ $item->remaining_main_sub()['main_qty'] }}">
                                        @else
                                            {{-- HAS SUB UNIT --}}
                                            <input type="text" class="has_sub_unit" hidden value="true">
                                            <input type="text" class="conversion" hidden value="{{ $item->product->main_unit->related_by }}">
                                            <label class="mr-1 ml-1">{{ $item->product->main_unit->name }}:</label>
                                            <input type="number" value="{{ $item->remaining_main_sub()['main_qty'] }}" class="form-control col main_qty mr-1" name="main_qty[{{ $item->id }}]" data-old="{{ $item->main_unit_qty }}"  data-related="{{ $item->product->main_unit->related_by }}" onkeydown="return event.keyCode !== 190" min="0" max="{{ $item->remaining_main_sub()['main_qty'] }}">
                                            <label class="mr-1">{{ $item->product->sub_unit->name }}:</label>
                                            <input type="number" value="{{ $item->remaining_main_sub()['sub_qty'] }}" class="form-control col sub_qty mr-1" name="sub_qty[{{ $item->id }}]" data-old="{{ $item->sub_unit_qty }}"  onkeydown="return event.keyCode !== 190" min="0" max="{{ $item->remaining_main_sub()['sub_qty'] }}">
                                        @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="p_price">{{ $item->remaining_product_vale() }}</span>
                                        <input type="text" name="price[{{ $item->id }}]" class="form-control t_price" value="{{ $item->remaining_product_vale() }}" hidden>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm no-return">
                                                <i class="fa fa-times"></i> No Return
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            {{-- <td colspan="2"></td> --}}
                            <td style="text-align:right;" colspan="6">Total:</td>
                            <td>
                              <span class="return_total_price">{{ $actual_total }}</span>
                            </td>
                        </tr>

                        <tr>
                          {{-- <td colspan="2"></td> --}}
                          <td style="text-align:right;" colspan="6">Discount:</td>
                          <td>
                            <span class="discount_amount">0</span>
                            <input type="text" name="calculated_discount" class="calculated_discount" value="0" hidden>
                          </td>
                      </tr>

                      <tr>
                        {{-- <td colspan="2"></td> --}}
                        <td style="text-align:right;" colspan="6">After Discount:</td>
                        <td>
                          {{-- <span class="discount_amount">{{ $actual_total }}</span> --}}
                          {{-- RETURN TOTAL --}}
                          <input type="text" name="return_product_value" class="return_amount" value="{{ $total_receivable }}" readonly>
                        </td>
                    </tr>
                    </tfoot>
                </table>


                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="" hidden>Considering The Paid Amount & Discount Amount -- You Should Return:</label>
                            @php
                            // // GEt the total
                            //     $remaining_product_value=$total_receivable-$total_receivable;

                            //     if($total_paid==0){
                            //         $return_amount=0;
                            //     }

                            //     // if fully paid - return full return product value
                            //     // in other words if due=0
                            //     else if($total_due==0){
                            //         $return_amount=$total_receivable;
                            //     }
                            //     // PARTIALLY PAID
                            //     // Remaining_product_value<paid_amount ? return extra money


                            //     else if($remaining_product_value<$total_paid){
                            //         // alert("Remaining Product Value"+remaining_product_value);
                            //         $return_amount=$total_paid-$previous_returned_product_value-$remaining_product_value;
                            //     }

                            // $return_amount=$total_paid!=0?$total_paid:0;

                            @endphp
                            <h4 class="cus_should_payable" hidden>0</h4>

                            <input type="number" name="should_pay" value="0" class="form-control cus_should_pay" hidden>
                            {{-- @if($errors->has('amount'))
                            <div class="alert alert-danger">{{ $errors->first('amount') }}</div>
                            @endif --}}


                        </div>


                    </div>
                    <div class="col-md-8" hidden>
                        <label for="">Decided to Pay</label>
                        <input type="number" name="payable_to_customer" value="0" class="form-control decided_to_pay" min="0" max="{{ 0 }}">
                        <label style="margin-top:20px; font-size:1.3em">
                            <input type="checkbox" class="form-control" style="height:15px; width:15px; display:inline;" name="pay_customer">
                            Make Payment
                        </label>
                    </div>
                    {{-- <div class="form-group col-md-6">
                        <label for="">Account</label>
                        <select name="t_account_id" class="form-control">
                        @foreach (\App\Account::all() as $item)
                            <option value="{{ $item->id }}" {{ old("t_account_id")==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
                        @endforeach
                        </select>
                        @if($errors->has('account_id'))
                        <div class="alert alert-danger">{{ $errors->first('account_id') }}</div>
                        @endif
                    </div> --}}
                </div>



                {{-- <input type="submit" value="Return Product" class="btn btn-primary" onclick="return confirm('Are you Sure?')" style="margin-top:20px;"> --}}
                <input type="submit" value="Return Product" class="btn btn-primary submit" style="margin-top:20px;">

            </form>
        </div>
    </div>
</div>



@endsection

@section('styles')
<style>
    .basic_details {
        width: 60%;
    }

    .basic_details th {
        width: 40%;
        text-align: right;
        margin-right: 30px;
    }

</style>
@endsection

@section('scripts')
<script>

// handle min-max
function handle_min_max(obj){
    var min = parseInt(obj.attr('min'));
    var max = parseInt(obj.attr('max'));
    var value = parseInt(obj.val());


    if(value<min){
        toastr.warning('Can not be less than '+min);
        obj.val(max);
    }

    if(value > max){
        toastr.warning('Can not be more than '+max);
        obj.val(max);
    }
}

$('.main_qty').change(function(){
     handle_min_max($(this));
});

$('.sub_qty').change(function(){
    handle_min_max($(this));
});

$( document ).ready(function(){
    calculate();
});
    // product search
    // on quantity change calculate total return amount
    //
    $(".no-return").on("click", function () {
        let tr = $(this).parent().parent();



    //  let returnAmount = parseFloat($("#return-amount").text().replace(",", ""));
    //  let removeAmount = parseFloat($(this).parent().parent().find(".sub-total").text().replace(",", ""));
    //  returnAmount = returnAmount - removeAmount;

    //  $("#return-amount").text(numberWithCommas(returnAmount));
    //  $("#return_input").val(returnAmount)

     tr.remove();
    //  calculate_total_return_product_price();
    calculate();
});

    function empty_field_check(placeholder) {
        if (placeholder == null) {
            placeholder = 0;
        } else if (placeholder.trim() == "") {
            placeholder = 0;
        }

        return parseFloat(placeholder);
    }

    function to_sub_unit(main_val, sub_val, related_by) {
        return (main_val * related_by) + sub_val;
    }

    function calculate_sub_total(main_qty, sub_qty, unit_price, related_by,has_sub_unit) {
        var sub_unit_price = 0;

        if (has_sub_unit=="true"&&related_by != 0) {
            sub_unit_price = parseFloat(unit_price / related_by);
        }

        var main_price = main_qty * unit_price;
        var sub_price = sub_qty * sub_unit_price;

        return parseFloat(main_price + sub_price).toFixed(2);
    }


    function update_row(obj) {
        // var qty = empty_field_check(obj.val());

        // var unit_price = empty_field_check(obj.parents('tr').find('.unit_price').html());

        // var total = qty * unit_price;
        // alert(total);

        let main_val = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').val()));
        var sub_val = parseInt(empty_field_check(obj.parents('tr').find('.sub_qty').val()));
        let related_by = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').attr('data-related')));
        let converted_sub = to_sub_unit(main_val, sub_val, related_by);
        var has_sub_unit = obj.parents('tr').find('.has_sub_unit').val();
        // alert(has_sub_unit);
        // let stock = obj.parents('tr').find('.main_qty').attr('data-value');
        let price=empty_field_check(obj.parents('tr').find('.unit_price').html());
        price = parseFloat(price);

        let subTotal = calculate_sub_total(main_val, sub_val, price, related_by,has_sub_unit);


        obj.parents('tr').find('.p_price').html(subTotal);
        obj.parents('tr').find('.t_price').val(subTotal);

    }

    function calculate_total_return_product_price(){
      total=0;

      $('.p_price').each(function(){
        total += parseFloat($(this).text());  // Or this.innerHTML, this.innerText
      });
      // alert(total);
      $('.return_total_price').text(total);
    }


    function calculate_return_product_price_with_discount(){
        // alert("Calculate Discount");
      var order_total_product_price=empty_field_check($('.order_product_price').text());
    //   alert(order_total_product_price);
      var total=empty_field_check($('.total_paid').val());

      var total_due=empty_field_check($('.due').val());

      var return_total=empty_field_check($('.return_total_price').text());


      var discount=empty_field_check($('.discount').text());

        // alert(discount);

      if(return_total!=0&&discount!=0){
        // alert("HELLO");
        // calculate based on discount
        var order_discont_field=$('.pos_discount_field').val();

        discountAmount=0;

        var percentage=0;
        // Calculate percentage
        if((typeof order_discont_field === 'string' || order_discont_field instanceof String)&&order_discont_field.includes("%")){
        //  alert("PERCENT!!");
          let removed_percent_discount=order_discont_field.replace('%','');
          percentage=parseFloat(removed_percent_discount);
          discountAmount = Math.round(return_total* (percentage / 100));
        }else{
          // calculate Percentage
          percentage=((discount/order_total_product_price)*100);
          // alert(percentage);
          discountAmount=Math.round(return_total* (percentage / 100));
        }

        // alert(discountAmount);

        $('.discount_amount').text(discountAmount);
        $('.calculated_discount').val(discountAmount);
        // alert(discountAmount);
        return_total-=discountAmount;

        //
      }else{
        $('.discount_amount').text(0);
        $('.calculated_discount').val(0);
      }
      // return payable;
      // else return 0
      // $('.cus_payable').val(return_total);
      $('.return_amount').val(return_total);
    }


    function calculate_customer_payable_amount(){
      // var order_total_product_price=empty_field_check($('.order_product_price').text());
      // alert(order_total_product_price);
      var total_product_value = empty_field_check($('.pos_receivable').text());;
      var pos_receivable=empty_field_check($('.pos_receivable').text());
      var total_paid=empty_field_check($('.total_paid').val());
      var previous_returned_product_value=empty_field_check($('.previous_returned_product_value').val());
    //   alert(previous_returned_product_value);
      var total_due=empty_field_check($('.due').text());

      var return_total_after_discount=empty_field_check($('.return_amount').val());

    //   alert(return_total_after_discount);

    //Remaining value of the -- Exisiting POS items
      var remaining_product_value=pos_receivable-return_total_after_discount -previous_returned_product_value;

        // alert()
    //   console.log(remaining_product_value);
    //   alert(remaining_product_value);
      // if total paid=0 => return 0
      payable=0;

      if(total_paid==0){
        payable=0;
      }else if(remaining_product_value==0&&return_total_after_discount==0){
        //   alert(remaining_product_value);
          payable=0;
      }
    //   else if(remaining_product_value==0){
    //     payable=return_total_after_discount;
    //   }

      // if fully paid - return full return product value
      // in other words if due=0
      else if(total_due==0){
        // alert("HERE");
        payable=return_total_after_discount;
      }
      // PARTIALLY PAID
      // Remaining_product_value<paid_amount ? return extra money


      else if(remaining_product_value<total_paid){
        // alert("HERE");
        // alert("Remaining Product Value"+remaining_product_value);
        payable=total_paid-remaining_product_value;
      }else{
        //   alert("ZERO");
          payable = 0;
        //   alert("HAHAHA");
        // receivable-previous_returned-current_value

        // if return amoutn is more than - paid amount
        // check - current invoice value - considering return
            // if current invoice value is less than paid amoutn
                    // paid_amount-current_value
            // else
                    // return 0

      }
    //   alert(payable);

      $('.cus_should_payable').text(payable);
      $('.cus_should_pay').val(payable);
      $('.decided_to_pay').val(payable);

      $(".decided_to_pay").attr({ "max" : payable  });

    //   set max

    }

    function calculate(){
        calculate_total_return_product_price();
        calculate_return_product_price_with_discount();
        calculate_customer_payable_amount();
    }

    // $('.qty').change(function(){
    //     update_row($(this));
    //     calculate();
    // });

    $(document).on('change', '.main_qty', function(e) {
        update_row($(this));
        calculate();
    });

    //sub_qty change
    $(document).on('change', '.sub_qty', function(e) {
        update_row($(this));
        calculate();
    });

    $('.submit').click(function(e){
        e.preventDefault();
        $(".return_form").submit();
    });

</script>
@endsection
