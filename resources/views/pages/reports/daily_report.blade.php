@extends('layouts.master')
@section('title', 'Daily Report')

@section('page-header')
<header class="header bg-ui-general">
	<div class="header-info">
		<h1 class="header-title">
			<strong>Daily Report</strong>
		</h1>
	</div>
</header>
@endsection

@section('content')
<div class="card col-12 print_area">
	<div class=" card-body">
		<div class="row mb-2">
		    
			<div class="col-md-2">
				<h3>Daily Report</h3>
			</div>
			
			<div class="col-md-10 print_hidden">
				<form action="#" method="GET">
					<div class="card-body">
						<div class="form-row">
							<div class="form-group col-md-2">
								<input type="text" name="start_date" data-provide="datepicker"
									data-date-today-highlight="true" data-orientation="bottom"
									data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control"
									placeholder="Enter Start Date" autocomplete="off">
							</div>
							<div class="form-group col-md-2">
								<input type="text" name="end_date" data-provide="datepicker"
									data-date-today-highlight="true" data-orientation="bottom"
									data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control"
									placeholder="Enter End Date" autocomplete="off">
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
					</div>
				</form>
			</div>
		</div>

		<table class="table table-striped table-bordered">
			<thead class="bg-primary">
				<tr>
					<th>Date</th>
					<th>Purchase Amount</th>
					<th>Sell Amount</th>
					<th>Returned</th>
					<th>Damage</th>
					<th>Profit</th>
					<th>Profit(%)</th>
				</tr>
			</thead>
			
			<tbody>
				@php
				$summaryService=new \App\Services\SummaryService();
				$begin = new DateTime( $start_date );
				$end = new DateTime( $end_date );
				$end = $end->modify( '+1 day' );
				$interval = new DateInterval('P1D');
				$daterange = new DatePeriod($begin, $interval ,$end);

				$brandId = (request()->brand_id == null) ? 1 : request()->brand_id;

				$total_sold = 0;
				$total_purchased = 0;
				$total_damage = 0;
				$total_returned = 0;
				$total_profit = 0;
				$total_profit_percentage = 0;
				@endphp

				@foreach ($daterange as $loopDate)
				@php
				$currentDate = $loopDate->format('Y-m-d');
				$sell_cost_profit = $summaryService::sell_profit($currentDate, $currentDate, $brandId);
				@endphp

				<tr>
					<td class="bg-primary">{{ $currentDate }}</td>
					<td>{{ number_format($purchase_cost=$sell_cost_profit['purchase_cost']),
						$total_purchased+=$purchase_cost }}/-</td>
						
					<td>{{ number_format(($sold=$sell_cost_profit['sell_value']+$summaryService::brandDamage($brandId, $currentDate, $currentDate))),
						$total_sold+=$sold }}/-</td>
						
					<td>{{ number_format($returned=$summaryService::brandReturn($brandId, $currentDate, $currentDate)),
						$total_returned+=$returned }}/-</td>
				
					<td>{{ number_format($damage=$summaryService::brandDamage($brandId, $currentDate, $currentDate)),
						$total_damage+=$damage }}/-</td>
					
					<td>{{ number_format($profitOfDay=$sell_cost_profit['profit']), $total_profit+=$profitOfDay }}/-</td>
					
					<td>{{ number_format($profit_percentage=($sold !== 0) ? ($profitOfDay / $sold) * 100 : 0, 2),
						$total_profit_percentage+=$profit_percentage }}%</td>
						
				</tr>

				@endforeach
			</tbody>

			<tfoot class="bg-dark text-white">
				<tr class="text-bold">
					<td>Total:</td>
					<td>{{ number_format($total_purchased) }}/-</td>
					<td>{{ number_format($total_sold) }}/-</td>
					<td>{{ number_format($total_returned) }}/-</td>
					<td>{{ number_format($total_damage) }}/-</td>
					<td>{{ number_format($total_profit) }}/-</td>
					<td>{{number_format(($total_sold !== 0) ? ($total_profit / $total_sold) * 100 : 0, 2)}} %</td>
				</tr>
			</tfoot>

		</table>

	</div>
</div>


</div>
@endsection

@section('styles')
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

@endsection