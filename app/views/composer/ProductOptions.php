<?php

namespace App\views\composer;

use App\Brand;
use App\Category;
use App\ProductType;
use Illuminate\View\View;

class ProductOptions
{
     public function compose(View $view)
     {
          // $categories = Category::orderBy('name')->get();
          // $brands = Supplier::orderBy('name')->get();
          // $product_types = json_decode(ProductType::type());

          // $view->withBrands($brands)
          //      ->withCategories($categories)
          // ->withTypes($product_types);
     }
}
