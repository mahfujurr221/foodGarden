@extends('layouts.master')
@section('title', 'Daily Report')
@section('page-header')
<div class="header-info mb-1">
    <h1 class="header-title">
        <strong>Profit Loss Report</strong>
    </h1>
</div>
@endsection

@section('content')
{{-- Summary Report --}}
{{-- @dd($sells) --}}
<div class="card col-12 print_area">
    <div class=" card-body">
        <div class="row mb-2">
            <div class="col-md-2">
                <h3>Profit Loss Report</h3>
            </div>
            <div class="col-md-10 print_hidden">
                <form action="#" method="GET">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <input type="text" name="start_date" class="form-control datepicker"
                                placeholder="Enter End Date" autocomplete="off"
                                value="{{ $start_date!=null?date('Y-m',strtotime($start_date)):'' }}">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" name="end_date" class="form-control datepicker"
                                placeholder="Enter End Date" autocomplete="off"
                                value="{{ $end_date!=null?date('Y-m',strtotime($end_date)):'' }}">
                        </div>
                        <div class="form-group col-md-2">
                            <select name="brand_id" class="form-control select2">
                                @foreach($brands as $brand)
                                <option value="{{$brand->id}}" @if(request()->brand_id==$brand->id) selected @endif>
                                    {{$brand->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <button class="btn btn-primary" type="submit">Filter</button>
                            <a href="{{ request()->url(0) }}" class="btn btn-danger">Reset</a>
                        </div>
                        <div class="form-group col-md-3 d-flex justify-content-end">
                            <a href="" class="btn btn-primary content-end" onclick="window.print()">Print</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @if($start_date && $end_date)
        
        <table class="table table-striped table-bordered">
            <thead class="bg-primary">
                <tr>
                    <th>Month</th>
                    <th>Purchased</th>
                    <th>Sales</th>
                    <th>Return Value</th>
                    <th>Damage Value</th>
                    <th>Profit</th>
                    {{-- <th>Expenses</th> --}}
                    <th>Profit(%)</th>
                </tr>
            </thead>
            
            @php
            // dd(date('y',strtotime($start_date)));
            $year = date('Y',strtotime($start_date));
            $month = (int)date('m',strtotime($start_date));
            $end_year = date('Y',strtotime($end_date));
            $end_month = date('m',strtotime($end_date));
            $total_sold=0;
            $total_purchased=0;
            $total_profit=0;
            $total_damage=0;
            $total_return=0;
            @endphp

            <tbody>
                @while($year < $end_year || ($year==$end_year && $month <=$end_month)) <tr>
                    <th class="bg-primary">{{ date('M',strtotime("2022-$month-01")) }} {{ $year }}</th>
                    @php
                    if(request()->brand_id==null){
                    $brandId=1;
                    }else{
                    $brandId=request()->brand_id;
                    }

                    $month_start_date="$year-$month-01";
                    $month_end_date=date('Y-m-t',strtotime("$year-$month-01"));


                    $summary_service=new App\Services\SummaryService();
                    $sell_cost_profit=$summary_service::sell_profit($month_start_date,$month_end_date,$brandId);

                    $return=$summary_service::brandReturn($brandId,$month_start_date,$month_end_date);
                    
                    $damage=$summary_service::brandDamage($brandId,$month_start_date,$month_end_date);
                    $sold=$sell_cost_profit['sell_value']+ $damage;
                    
                    $cost=$sell_cost_profit['purchase_cost'];

                    $profit=$sell_cost_profit['profit'];

                    $total_sold+=$sold;
                    $total_purchased+=$cost;
                    $total_profit+=$profit;
                    $total_return+=$return;
                    $total_damage+=$damage;

                    @endphp
                    <td>{{ number_format($cost) }}/-</td>
                    <td>{{ number_format($sold) }}/-</td>
                    <td>{{ number_format($return) }}/-</td>
                    <td>{{ number_format($damage) }}/-</td>
                    <td>{{ number_format($profit) }}/-</td>
                    @php
                    
                    $profit_percentage = ($sold !== 0) ? ($profit / $sold) * 100 : 0;
                    @endphp
                    <td>
                        {{ number_format($profit_percentage, 2) }}%
                    </td>
                    </tr>
                    @php
                    $month++;
                    if ($month == 13)
                    {
                    $year++;
                    $month = 1;
                    }
                    @endphp
                    @endwhile
            </tbody>
            <tfoot class="bg-dark">
                <tr>
                    <th>Total</th>
                    <th>{{ number_format($total_sold) }}/-</th>
                    <th>{{ number_format($total_purchased) }}/-</th>
                    <th>{{ number_format($total_return) }}/-</th>
                    <th>{{ number_format($total_damage) }}/-</th>
                    <th>{{ number_format($total_profit) }}/-</th>
                    <th>{{number_format(($total_sold !== 0) ? ($total_profit / $total_sold) * 100 : 0,2)}}%</th>
                </tr>
        </table>
        @else
        <div class="alert alert-danger">
            Please Select Start and End Month
        </div>
        @endif

    </div>
</div>


</div>
@endsection

@section('styles')
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
    integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    @media print {

        table,
        table th,
        table td {
            color: black !important;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
    integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $('.datepicker').datepicker({
            'format':'yyyy-mm',
            viewMode: "years",
            minViewMode: "months",
            autoclose:true
        });
</script>
@endsection