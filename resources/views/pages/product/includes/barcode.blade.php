@if($pos_setting->barcode_type=="a4")
<div class="modal fade" id="bar_code_modal" role="modal" aria-modal="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-body" id="barcode-page">
        </div>
        <div class="modal-footer">
          <button type="button" onclick="print_barcode()" class="btn btn-primary">
            <i class="fa fa-print"></i>
            Print
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>

<script>
    $(document).on('click', '.generated_barcode', function() {
        let name = $(this).attr('data-name');
        let code = $(this).attr('data-code');
        let price = $(this).attr('data-price');
        let url = "{{ route('product.barcode', 'value') }}".replace('value', code);
        $.get(url, (data) => {
            let company = "{{ $pos_setting->company }}";

            let barcode = '';
            barcode += `
                <div class="text-center p-4 print_area" id="barcode">
                    <table class="table table-bordered">`;
                        for ($i = 0; $i < 10; $i++) {
                            barcode += `<tr>`;
                            for ($j = 0; $j < 3; $j++) {
                                barcode +=
                                `<td>
                                    <p style="margin-bottom:2px; line-height:9px; margin-top:10px; font-size: 9px; color:black;"><strong>${company}</strong></p>
                                    ${data}
                                    <p style="margin-bottom:0; line-height:9px; margin-top:-7px; font-size: 9px; color:black;"><strong>${name}</strong></p>
                                    <p style="margin-bottom:0; line-height:9px; margin-top:1px; font-size: 9px; color:black;"><strong>${price} Tk</strong></p>
                                </td>`;
                            }
                            barcode += `</tr>`;
                        }
                    barcode += `
                    </table>
                </div>
                `;
            $("#barcode-page").html(barcode);
            });
                $("#bar_code_modal").modal('show');
        });
</script>
@elseif($pos_setting->barcode_type=="single")

<div class="modal fade" id="bar_code_modal" role="modal" aria-modal="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="width:2in; margin-left:auto; margin-right:auto;">
        <div class="modal-body" id="barcode-page" style="padding:0;">

        </div>
        <div class="modal-footer" style="padding:.5em;">
          <button type="button" onclick="print_barcode()" class="btn btn-primary">
            <i class="fa fa-print"></i>
            Print
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script>
      $(document).on('click', '.generated_barcode', function() {
            let code = $(this).attr('data-code');
            let name = $(this).attr('data-name');
            let price = $(this).attr('data-price');
            let url = "{{ route('product.barcode', 'value') }}".replace('value', code);
            $.get(url, (data) => {
                let company = "{{ $pos_setting->company }}";
                let barcode = '';
                barcode += `
                    <div class="text-center p-4 print_area" id="barcode" style="padding:0 !important">
                        <table class="table" style="margin-bottom:0 !important;">
                            <tr>
                                <td style="border-top:0 !important;padding:0;" class="code_p">
                                <p style="margin-bottom:2px; line-height:9px; margin-top:10px; font-size: 9px; color:black;"><strong>${company}</strong></p>
                                ${data}
                                {{--<p style="margin-bottom:0; line-height:9px; margin-top:5px; font-size: 9px;"><strong>${code}</strong></p>--}}
                                <p style="margin-bottom:7px; line-height:9px; margin-top:-5px; font-size: 9px; color:black;"><strong>${name}</strong></p>

                                <p style="margin-bottom:0; line-height:9px; margin-top:0; font-size: 12px; color:black;"><strong>${price} Tk</strong></p>
                                </td>
                            </tr>
                        </table>
                    </div>`;
                $("#barcode-page").html(barcode);
                });
                $("#bar_code_modal").modal('show');
        });
  </script>
@endif

<script>
    //  Print Barcode
    function print_barcode(id) {
        $("#bar_code_modal").modal('hide');
        $(".modal-backdrop").remove();
        $(".modal").css('display', 'none');

        let mainDocBody = $('.main-container').html();
        let printDoc = $("#barcode-page").html();
        $(".main-container").html(printDoc);
        $("body").attr('style', '');
        window.print();
        $(".main-container").html(mainDocBody);
    }
</script>
