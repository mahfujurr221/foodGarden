@extends('layouts.master')
@section('title', 'Promotional SMS ')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Promotional SMS</strong>
          </h1>
     </div>
</header>
@endsection

@section('content')
<div class="col-12">
     <div class="card">
          <h4 class="card-title">Send Promotional SMS </h4>

          <form action="{{ route('send.promotion.sms') }}" method="POST">
               @csrf
               <div class="card-body">
                    <div class="form-row">
                         <div class="col-md-6">
                              <div class="form-group">
                                   <label for="">Select Customers </label>
                                   <select multiple name="customers[]" id="" class="form-control"
                                        data-provide="selectpicker" data-live-search="true" multiple
                                        data-actions-box="true" data-size="14" required>
                                        @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                   </select>
                                   @if($errors->has('customers'))
                                   <span class="invalid-feedback">{{ $errors->first('customers') }}</span>
                                   @endif
                              </div>
                              <div class="form-group">
                                   <label for="sms">SMS Body </label>
                                   <textarea name="sms"
                                        class="form-control {{ $errors->has('sms') ? 'is-invalid': '' }}"
                                        placeholder="Write your promotional message" required>{{ old('sms') }}</textarea>
                                   @if($errors->has('sms'))
                                   <span class="invalid-feedback">{{ $errors->first('sms') }}</span>
                                   @endif
                              </div>
                         </div>
                    </div>
               </div>
               <div class="card-footer text-left">
                    <button type="submit" class="btn btn-primary">
                         <i class="fa fa-send"></i>
                         Send
                    </button>
               </div>
          </form>
     </div>
</div>
@endsection

@section('styles')

@endsection

@section('scripts')

@endsection