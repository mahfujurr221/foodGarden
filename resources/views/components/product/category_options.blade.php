<div class="form-group">
     <label for="">Category<span class="field_required"></span></label>
     <select name="category_id" id="" class="form-control {{ $errors->has('price') ? 'is-invalid': '' }}">
     <option value="">Select Category</option>
     @foreach (\App\Category::all() as $category)
          <option
          @isset($category_id)
              {{ $category_id == $category->id ? 'selected' : '' }}
          @endisset
          value="{{ $category->id }}">{{ $category->name }}</option>
     @endforeach
     </select>
     @if($errors->has('category_id'))
          <span class="invalid-feedback">{{ $errors->first('category_id') }}</span>
     @endif
</div>
