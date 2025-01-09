@extends('layouts.master')
@section('title', 'Payments History')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Payments </strong>
    </h1>
  </div>

  <div class="header-action">
    <nav class="nav">
      <a class="nav-link active" href="{{ route('payment.index') }}">
        Payments
      </a>

      <a class="nav-link" href="{{ route('payment.create') }}">
        <i class="fa fa-plus"></i>
        Create Payment
      </a>
    </nav>
  </div>

</header>
@endsection

@section('content')
<div class="col-lg-12">
  <div class="card">
    <h5 class="card-title"><strong>All Payments History </strong></h5>

    <div class="card-body">

      <p>All Kinds of Payment History</p>
      <!-- Nav tabs -->
      <ul class="nav nav-tabs nav-justified">
        <li class="nav-item">
          <a class="nav-link active" data-toggle="tab" href="#pos-payments">Pos Payments</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#purchase-payments">Purchases Payments</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#customer-payments">Customer Payments</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#supplier-payments">Supplier Payments</a>
        </li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content">
        <div class="tab-pane fade active show" id="pos-payments">
          Globally syndicate resource sucking ideas through interactive networks. Proactively underwhelm technically
          sound growth strategies after high-payoff customer service. Professionally provide.
        </div>
        <div class="tab-pane fade" id="purchase-payments">
          Professionally embrace proactive value whereas customized solutions. Monotonectally formulate high standards
          in e-business with cost effective ideas. Objectively cultivate maintainable.
        </div>
        <div class="tab-pane fade" id="customer-payments">
          Globally optimize market positioning experiences with an expanded array of users. Seamlessly underwhelm
          backward-compatible customer service after extensive web services.
        </div>
        <div class="tab-pane fade" id="supplier-payments">
          Collaboratively optimize covalent technologies through high standards in models. Objectively synthesize
          premier process improvements with granular functionalities. Phosfluorescently disseminate technically.
        </div>
      </div>

    </div>
  </div>
</div>

@endsection

@section('styles')
<style>

</style>
@endsection

@section('scripts')
<script>

</script>
@endsection