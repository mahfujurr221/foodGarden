<?php

namespace App;

use App\Brand;
use App\Image;
use App\PosItem;
use App\Category;
use App\PurchaseItem;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    //join damage return table
    // public function damage()
    // {
    //     return $this->hasMany(DamageReturn::class);
    // }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function sale()
    {
        return $this->hasMany(PosItem::class);
    }

    public function purchase()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function main_unit()
    {
        return $this->belongsTo(Unit::class, 'main_unit_id');
    }

    public function sub_unit()
    {
        return $this->belongsTo(Unit::class, 'sub_unit_id');
    }

    // CUSTOM

    public function average_cost()
    {
        $items = PurchaseItem::where('product_id', $this->id);
        $number = $items->count();
        // dd($number);
        $total_cost = $items->sum('rate');

        if ($this->opening_stock != 0 && $this->opening_stock != null) {
            $total_cost += $this->cost;
            ++$number;
        }
        $average = 0;
        if ($number != 0) {
            $average = $total_cost / $number;
        }

        if ($average == 0) {
            return $this->cost;
        }
        return $total_cost / $number;
    }

    public function sell_count($start_date = null, $end_date = null)
    {
        $sells = PosItem::query();
        if ($start_date) {
            $sells = $sells->whereHas('pos', function ($pos) use ($start_date) {
                $pos->where('sale_date', '>=', $start_date);
            });
        }
        if ($end_date) {
            $sells = $sells->whereHas('pos', function ($pos) use ($end_date) {
                $pos->where('sale_date', '<=', $end_date);
            });
        }
        return $sells->where('product_id', $this->id)->sum('qty');
    }

    //discount sale count 
    public function discount_sell_count($start_date = null, $end_date = null)
    {
        $sells = PosItem::query();
        if ($start_date) {
            $sells = $sells->whereHas('pos', function ($pos) use ($start_date) {
                $pos->where('sale_date', '>=', $start_date);
            });
        }
        if ($end_date) {
            $sells = $sells->whereHas('pos', function ($pos) use ($end_date) {
                $pos->where('sale_date', '<=', $end_date);
            });
        }
        $actual_sold = $sells->where('product_id', $this->id)->sum('discount_qty');
        $returned = $sells->where('product_id', $this->id)->sum('discount_return');
        return $actual_sold - $returned;
    }

    public function purchase_count()
    {
        return PurchaseItem::where('product_id', $this->id)->sum('qty');
    }
    //discount purchase count
    public function discount_purchase_count()
    {
        return PurchaseItem::where('product_id', $this->id)->sum('discount_qty');
    }
    public function damage_count()
    {
        return Damage::where('product_id', $this->id)->sum('qty');
    }

    public function return_count()
    {
        return ReturnItem::where('product_id', $this->id)->sum('qty');
    }

    public function stock()
    {
        // $stock = 0;\
        $stock = $this->purchase_count() - $this->sell_count();
        // Damage Count
        $stock -= $this->damage_count();
        // return
        $stock += $this->return_count();
        return $stock > 0 ? $stock : 0;
    }

    //discount stock 
    public function discount_stock()
    {
        // $stock = 0;
        $stock = $this->discount_purchase_count() - $this->discount_sell_count();
        return $stock > 0 ? $stock : 0;
    }
    public function update_total_sold()
    {
        $total_sold = $this->sell_count();
        $this->update([
            'total_sold' => $total_sold
        ]);
    }

    public function update_stock()
    {
        $stock = $this->stock();
        $main_sub_stock = $this->separate_main_sub_qty($stock);
        $this->update([
            'stock' => $stock,
            'main_unit_stock' => $main_sub_stock['main_qty'],
            'sub_unit_stock' => $main_sub_stock['sub_qty'],
        ]);
    }

    public function update_calculated_data()
    {
        $this->update_stock();
        $this->update_total_sold();
    }

    public function separate_main_sub_qty($quantity)
    {
        $main_unit = $this->main_unit;
        $main_qty = 0;
        $main_qty_as_sub = 0;
        $sub_qty = 0;
        if ($this->sub_unit_id && $quantity != 0 && $main_unit->related_by != null) {
            $main_qty = (int) ($quantity / $main_unit->related_by);
            $main_qty_as_sub = $main_qty * $main_unit->related_by;
            $sub_qty = $quantity - $main_qty_as_sub;
        } else {
            $main_qty = $quantity;
            $sub_qty = 0;
        }
        return [
            'main_qty' => $main_qty,
            'sub_qty' => $sub_qty
        ];
    }
    public function readable_qty($quantity)
    {
        $separated = $this->separate_main_sub_qty($quantity);
        $readable_stock = "";
        $readable_stock .= $separated['main_qty'] . " " . $this->main_unit->name;
        if ($this->sub_unit) {
            $readable_stock .= " " . $separated['sub_qty'] . " " . $this->sub_unit->name;
        }
        return $readable_stock;
    }

    // Convert all quantity to sub_unit quantity
    public function to_sub_quantity($main_quantity, $sub_quantity)
    {
        $main_unit = $this->main_unit;
        $related_by = 1;
        if ($this->sub_unit_id && $main_unit->related_by != null) {
            $related_by = $main_unit->related_by;
        }
        return ($main_quantity * $related_by) + $sub_quantity;
    }
    public function calculate_worth($main_qty, $sub_qty, $unit_price)
    {
        $main_unit = $this->main_unit;
        $sub_unit_price = 0;
        if ($main_unit->related_by) {
            $sub_unit_price = $unit_price / $main_unit->related_by;
        }
        $main_price = $main_qty * $unit_price;
        $sub_price = $sub_qty * $sub_unit_price;
        return $main_price + $sub_price;
    }

    public function quantity_worth($qty, $unit_price)
    {
        $separated = $this->separate_main_sub_qty($qty);
        return $this->calculate_worth($separated['main_qty'], $separated['sub_qty'], $unit_price);
    }
    // Don't delete if any relation is existing
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($rel) {
            $relationMethods = ['sale', 'purchase'];
            foreach ($relationMethods as $relationMethod) {
                if ($rel->$relationMethod()->count() > 0) {
                    return false;
                }
            }
        });
    }
}