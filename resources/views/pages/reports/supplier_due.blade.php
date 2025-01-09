@extends('layouts.master')
@section('title', 'Supplier Due Report')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Supplier Due Report</strong>
        </h1>
    </div>
</header>
@endsection

@section('content')

<div class="col-12">


    <div class="card card-body">
        <div class="row">
            <div class="col-12">
                {{-- <a href="" class="btn btn-primary pull-right" onclick="window.print()">Print</a> --}}
                <form action="">
                    <div class="form-row">

                        <div class="form-group col-md-3">
                            <select name="supplier_id" id="" class="form-control" data-provide="selectpicker"
                                    data-live-search="true" data-size="10">
                                <option value="">Select Supplier</option>
                                @foreach ($filter_suppliers as $item)
                                    <option value="{{ $item->id }}" {{ request('supplier_id')==$item->id?'SELECTED':'' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-9">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-sliders"></i>
                                Filter
                            </button>
                            <a href="{{ request()->url() }}" class="btn btn-info">Reset</a>
                            <a href="" class="btn btn-primary pull-right" onclick="window.print()">Print</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


	     <div class="card">
        <div class="print_area"  style="width:100%;">
          {{-- <a href="" class="btn btn-primary float-right print_hidden" onclick="window.print()" style="margin-top:10px;">Print</a> --}}



          <h3 class="card-title" style=" text-align: left;"><strong>Supplier Due Report</strong></h3>
          <div class="card-body">
               <table class="table table-striped table-bordered">
                <thead>
                    <tr>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Address</th>
						<th>Invoice Due</th>
						<th>Direct Due</th>
						<th>Total Due</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $key => $supplier)
                        <tr>
                            {{-- <th scope="row">{{ (isset($_GET['page']))? ($_GET['page']-1)*20+$key+1 : $key+1 }}</th> --}}
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>{!! $supplier->address !!}</td>

							{{--<td class="font-weight-bold">
                                {{ number_format($customer->receivable())  }} /-
                            </td>
                            <td class="font-weight-bold">
                                {{ number_format($customer->paid()) }} /-
								</td>--}}

                            <td class="font-weight-bold">
                                {{ number_format($supplier_due=$supplier->due() ) }} /-
                            </td>
							<td class="font-weight-bold">
								@php
									$wallet_due=0;
								@endphp
								@if($supplier->wallet_balance())
								{{$wallet_due=abs($supplier->wallet_balance())}}
								@else
									0
								@endif
								/-
							</td>
							<td class="font-weight-bold">{{ $wallet_due+$supplier_due }} /-</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="alert alert-danger" role="alert">
                                    <strong>You have no Supplier with Due</strong>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                </tbody>
               </table>
			   {{$suppliers->links()}}
          </div>

        </div>
     </div>





</div>

@endsection

@section('styles')
<style>
    @media print{
        table,table th,table td{
            color:black !important;
        }
    }
</style>
@endsection

@section('scripts')
@include('includes.delete-alert')
<script>

</script>
@endsection
