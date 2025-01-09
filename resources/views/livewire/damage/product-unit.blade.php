<div>
    <div class="form-group">
        <label for="">Product</label>
        <select name="product_id" class="form-control" data-provide="selectpicker" data-live-search="true" data-size="10" wire:model="selected_product">
          <option value="">Select Product</option>
          @foreach (\App\Product::all() as $item)
            <option value="{{ $item->id }}" {{ old("product_id")==$item->id?"SELECTED":"" }}>{{ $item->name." - Stock: ".$item->readable_qty($item->stock()) }}</option>
          @endforeach
        </select>
        @if($errors->has('product_id'))
          <div class="alert alert-danger">{{ $errors->first('product_id') }}</div>
        @endif
      </div>

      @if($product)
      <div class="form-group">


            {{-- @if($main_qty) --}}
                <label class="mr-1 ml-1">{{ $product->main_unit->name }}:</label>
                <input type="number" value="{{ $main_qty }}" class="form-control col main_qty mr-1" name="main_unit_qty">
            {{-- @endif --}}

            @if($sub_qty||$product->sub_unit_id)
                <label class="mr-1">{{ $product->sub_unit->name }}:</label>
                <input type="number" value="{{ $sub_qty }}" class="form-control col sub_qty mr-1" name="sub_unit_qty">
            @endif

        </div>
      @endif
</div>
