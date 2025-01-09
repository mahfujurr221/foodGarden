<?php

namespace App\Services;

use App\OrderDamageItem;
use App\OrderReturnItem;
use App\PosItem;
use App\Product;
use App\PurchaseItem;

class StockService
{
    // public static function return_purchase_ids_and_qty_for_the_sell($product_id, $qty)
    // {
    //     // if not enough stock return error
    //     $product = Product::find($product_id);
    //     // dd($product->stock());
    //     if ($product->stock() < $qty || $qty == 0) {
    //         return $data = [];
    //     }
    //     $purchase_items = PurchaseItem::where('product_id', $product_id)
    //         ->where('remaining', '>', '0')
    //         ->get();
    //     $purchase_items = $purchase_items->filter(function ($item) {
    //         if ($item->remaining() > 0) {
    //             return $item;
    //         }
    //     })->values();
    //     $data = [];
    //     $total_price = 0;
    //     $placeholder_qty = $qty;
    //     foreach ($purchase_items as $item) {
    //         if ($placeholder_qty <= 0) {
    //             break;
    //         }
    //         if ($item->remaining() >= $placeholder_qty) {
    //             $data['purchase_items'][] = [
    //                 'purchase_item_id' => $item->id,
    //                 'purchase_id' => $item->purchase_id,
    //                 'qty' => $placeholder_qty,
    //                 'price' => $item->rate
    //             ];

    //             $product = $item->product;
    //             $total_price += $product->quantity_worth($qty, $item->rate);
    //             $placeholder_qty = 0;

    //         } else {
    //             $data['purchase_items'][] = [
    //                 'purchase_item_id' => $item->id,
    //                 'purchase_id' => $item->purchase_id,
    //                 'qty' => $item->remaining(),
    //                 'price' => $item->rate
    //             ];
    //             $total_price += $product->quantity_worth($item->remaining(), $item->rate);
    //             $placeholder_qty -= $item->remaining();
    //         }
    //     }

    //     $average_price = $total_price / $qty;
    //     $data['average_price'] = $average_price;
    //     $data['total_price'] = $total_price;
    //     return $data;
    // }

    public static function return_purchase_ids_and_qty_for_the_sell($product_id, $qty)
    {
        // dd("HE");
        // if not enough stock return error
        $product = Product::find($product_id);
        // dd($product->stock());
        if ($product->stock() < $qty || $qty == 0) {
            return $data = [];
        }

        $purchase_items = PurchaseItem::where('product_id', $product_id)
            // ->where('remaining', '>', '0')
            ->get();
        // $purchase_items=$purchase_items->where()
        $purchase_items = $purchase_items->filter(function ($item) {
            // dd($item);
            if ($item->remaining() > 0) {
                return  $item;
            }
        })->values();


        $data = [];
        $total_price = 0;
        $placeholder_qty = $qty;

        foreach ($purchase_items as $item) {
            if ($placeholder_qty <= 0) {
                break;
            }
            // dd("remaining: ".$item->remaining());
            if ($item->remaining() >= $placeholder_qty) {
                $data['purchase_items'][] = [
                    'purchase_item_id' => $item->id,
                    'purchase_id' => $item->purchase_id,
                    'qty' => $placeholder_qty,
                    'price' => $item->rate
                ];

                $product = $item->product;

                $total_price += $product->quantity_worth($placeholder_qty, $item->rate);

                $placeholder_qty = 0;
            } else {
                $data['purchase_items'][] = [
                    'purchase_item_id' => $item->id,
                    'purchase_id' => $item->purchase_id,
                    'qty' => $item->remaining(),
                    'price' => $item->rate
                ];

                $total_price += $product->quantity_worth($item->remaining(), $item->rate);

                $placeholder_qty -= $item->remaining();
            }
        }

        $average_price = $total_price / $qty;

        $data['average_price'] = $average_price;
        $data['total_price'] = $total_price;

        return $data;
    }

    public static function add_new_pos_items_and_recalculate_cost($request, $pos)
    {
        // dd($request->all());
        if ($request->product_id) {
            foreach ($request->product_id as $key => $value) {
                $main_qty = 0;
                $sub_qty = 0;
                $returned_qty = 0;
                $returned_sub = 0;
                $damaged = 0;
                if ($request->main_qty && array_key_exists($value, $request->main_qty)) {
                    $main_qty = $request->main_qty[$value];
                }
                if ($request->sub_qty && array_key_exists($value, $request->sub_qty)) {
                    $sub_qty = $request->sub_qty[$request->product_id[$key]];
                }
                if ($request->old_returned && array_key_exists($value, $request->old_returned)) {
                    $returned_qty = $request->old_returned[$value];
                }
                if ($request->old_returned_sub_unit && array_key_exists($value, $request->old_returned_sub_unit)) {
                    $returned_sub = $request->old_returned_sub_unit[$value];
                }
                // dd($returned_sub);
                if ($request->damage && array_key_exists($value, $request->damage)) {
                    $damaged = $request->damage[$value];
                }

                $sub_total = $request->sub_total[$key];
                $ordered_sub_total = $request->subtotal_holder[$key];

                if ($ordered_sub_total == 0) {
                    $ordered_sub_total = $sub_total;
                }

                $product = Product::find($request->product_id[$key]);
                $ordered_qty = $product->to_sub_quantity($main_qty, $sub_qty);
                $actual_returned = $product->to_sub_quantity($returned_qty, $returned_sub);
                $qty = ($ordered_qty - $actual_returned);
                $actual_qty = $qty;
                if ($ordered_qty != $actual_returned) {
                    $purchase_distribution = StockService::return_purchase_ids_and_qty_for_the_sell($request->product_id[$key], $qty);

                    if (isset($purchase_distribution['purchase_items'])) {
                        $pos_item = PosItem::create([
                            'pos_id' => $pos->id,
                            'product_name' => $request->name[$key],
                            'product_id' => $request->product_id[$key],
                            'rate' => $request->rate[$key],
                            // 'unit_cost'    => $purchase_distribution['average_price'],
                            'total_purchase_cost' => $purchase_distribution['total_price'],
                            'main_unit_qty' => $main_qty,
                            'sub_unit_qty' => $sub_qty,
                            // 'ordered_qty' => $request->ordered_qty[$key],
                            'ordered_qty' => $ordered_qty,
                            'qty' => $qty,
                            'returned' => $returned_qty,
                            'returned_sub_unit' => $returned_sub,
                            'returned_qty' => $actual_returned,
                            'returned_value' => $request->returned_value[$key],
                            'damage' => $request->damage[$key],
                            'damaged_value' => $request->damaged_value[$key],
                            'discount_qty' => $request->discount_qty[$key],
                            'discount_return' => $request->discount_return[$key],
                            'sub_total' => $request->sub_total[$key],
                             // 'ordered_sub_total' => $ordered_sub_total,
                            'ordered_sub_total' => $request->subtotal_holder[$key]??0,
                        ]);
                        foreach ($purchase_distribution['purchase_items'] as $pd_key => $pd_value) {
                            $pos_item->stock()->create([
                                'purchase_id' => $pd_value['purchase_id'],
                                'purchase_item_id' => $pd_value['purchase_item_id'],
                                'product_id' => $request->product_id[$key],
                                'qty' => $pd_value['qty']
                            ]);
                        }
                        if ($request->damage[$key] > 0) {
                            $order_damage = new OrderDamageItem();
                            $order_damage->estimate_id = $request->estimate;
                            $order_damage->product_id = $request->product_id[$key];
                            $order_damage->qty = $request->damage[$key];
                            $order_damage->rate = $request->rate[$key];
                            // $order_damage->total = ($product->cost * $request->damage[$key]);
                            $order_damage->total = $request->damaged_value[$key];
                            $order_damage->save();
                        }
                        if ($returned_qty > 0) {
                            $order_return = new OrderReturnItem();
                            $order_return->estimate_id = $request->estimate;
                            $order_return->product_id = $request->product_id[$key];
                            $order_return->qty = $request->old_returned[$key];
                            $order_return->sub_qty = $request->old_returned_sub_unit[$key]??0;
                            $order_return->rate = $request->rate[$key];
                            $order_return->total = $request->returned_value[$key];
                            $order_return->save();
                        }
                    } else {
                        throw new \Exception('Low Stock');
                    }
                }
            }
        }
        // return $problems;
    }
    // When Sell Edited - Update
    public static function update_pos_items_and_recalculate_cost($request, $pos)
    {
        foreach ($request->old_id as $key => $value) {
            info($value);
            $main_qty = 0;
            $sub_qty = 0;
            $returned = 0;
            $damaged = 0;
            if ($request->old_main_qty && array_key_exists($value, $request->old_main_qty)) {
                $main_qty = $request->old_main_qty[$value];
            }
            if ($request->old_sub_qty && array_key_exists($value, $request->old_sub_qty)) {
                $sub_qty = $request->old_sub_qty[$value];
            }
            if ($request->old_returned && array_key_exists($value, $request->old_returned)) {
                $returned = $request->old_returned[$value];
            }
            if ($request->old_damage && array_key_exists($value, $request->old_damage)) {
                $damaged = $request->old_damage[$value];
            }

            $pos_item = PosItem::find($value);
            $product = $pos_item->product;
            $ordered_qty = $product->to_sub_quantity($main_qty, $sub_qty);
            $returned_qty = $product->to_sub_quantity($returned, 0);
            $damaged_qty = $product->to_sub_quantity($damaged, 0);
            $qty = ($ordered_qty - $returned_qty);
            $actual_qty = ($qty - $damaged_qty);
            // quantity changed
            if ($pos_item->qty != $qty) {
                if ($qty > $pos_item->qty) {
                    $new_quantity = $qty - $pos_item->qty;
                    foreach ($pos_item->stock as $stock_item) {
                        if ($stock_item->purchase_item->remaining() > $new_quantity) {
                            $existing_quantity = $stock_item->qty;
                            $pos_item->update([
                                'main_unit_qty' => $main_qty,
                                'sub_unit_qty' => $sub_qty,
                                'qty' => $actual_qty,
                                'rate' => $request->old_rate[$key],
                                'sub_total' => $request->old_sub_total[$key]
                            ]);
                            $stock_item->update([
                                'qty' => $existing_quantity + $new_quantity
                            ]);
                            break;
                        }
                    }
                    // check if stock table updated properly
                    $stock_quantity = $pos_item->stock()->sum('qty');
                    if ($stock_quantity == $qty) {
                    } else {
                        $purchase_distribution = StockService::return_purchase_ids_and_qty_for_the_sell($pos_item->product_id, $new_quantity, $actual_qty);
                        if (isset($purchase_distribution['purchase_items'])) {
                            $pos_item->update([
                                'main_unit_qty' => $main_qty,
                                'sub_unit_qty' => $sub_qty,
                                'qty' => $qty,
                                'rate' => $request->old_rate[$key],
                                'sub_total' => $request->old_sub_total[$key]
                            ]);
                            foreach ($purchase_distribution['purchase_items'] as $p_dist_key => $p_dist_value) {
                                $stock = $pos_item->stock()->where('purchase_item_id', $p_dist_value['purchase_item_id'])->first();
                                if ($stock) {
                                    $stock->update([
                                        'qty' => $stock->qty + $p_dist_value['qty']
                                    ]);
                                } else {
                                    $pos_item->stock()->create([
                                        'purchase_id' => $p_dist_value['purchase_id'],
                                        'purchase_item_id' => $p_dist_value['purchase_item_id'],
                                        'product_id' => $pos_item->product_id,
                                        'qty' => $p_dist_value['qty']
                                    ]);
                                }
                            }
                        } else {
                            session()->flash('warning', $product->name . ' Doesn\'t have enough stock. So could not be updated.');
                        }
                    }
                } else {
                    $extra_quantity = $pos_item->qty - $qty;
                    $stock = $pos_item->stock()->where('qty', '>=', $qty)->first();

                    if ($stock && $stock->qty > $extra_quantity) {
                        $stock->update([
                            'qty' => $stock->qty - $extra_quantity
                        ]);
                        $pos_item->update([
                            'main_unit_qty' => $main_qty,
                            'sub_unit_qty' => $sub_qty,
                            'qty' => $qty,
                            'rate' => $request->old_rate[$key],
                            'sub_total' => $request->old_sub_total[$key]
                        ]);
                    } else if ($stock && $stock->qty == $extra_quantity) {
                        $stock->delete();
                        $pos_item->update([
                            'main_unit_qty' => $main_qty,
                            'sub_unit_qty' => $sub_qty,
                            'qty' => $qty,
                            'rate' => $request->old_rate[$key],
                            'sub_total' => $request->old_sub_total[$key]
                        ]);
                    } else {
                        $place_holder_quantity = $extra_quantity;
                        foreach ($pos_item->stock as $stock_key => $stock_value) {
                            if ($stock_value->qty == $place_holder_quantity) {
                                $stock_value->delete();
                                $place_holder_quantity = 0;
                            } elseif ($stock_value->qty < $place_holder_quantity) {
                                // delete & update placeholder
                                $place_holder_quantity -= $stock_value->qty;
                                $stock_value->delete();
                            } else {
                                // stock quantity is greater than placeholder
                                $stock_value->update([
                                    'qty' => $stock_value->qty - $place_holder_quantity
                                ]);
                                $place_holder_quantity = 0;
                            }
                            if ($place_holder_quantity == 0) {
                                break;
                            }
                        }
                        $pos_item->update([
                            'main_unit_qty' => $main_qty,
                            'sub_unit_qty' => $sub_qty,
                            'qty' => $qty,
                            'rate' => $request->old_rate[$key],
                            'sub_total' => $request->old_sub_total[$key]
                        ]);
                    }
                }
            } elseif ($pos_item->rate != $request->old_rate[$key]) {
                $pos_item->update([
                    'rate' => $request->old_rate[$key],
                    'sub_total' => $request->old_sub_total[$key]
                ]);
            } else {
            }
            $pos_item->update_total_purchase_cost();
        }
    }
    public static function handle_return_stock($pos_item, $return_item, $qty)
    {
        $temp_quanity = $qty;
        foreach ($pos_item->stock as $stock) {
            if ($temp_quanity == 0) {
                break;
            }
            if ($stock->remaining() == $temp_quanity) {
                $return_item->stock()->create([
                    'purchase_id' => $stock->purchase_id,
                    'purchase_item_id' => $stock->purchase_item_id,
                    'stock_id' => $stock->id,
                    'product_id' => $stock->product_id,
                    'qty' => $temp_quanity,
                    'out' => 0,
                ]);
                break;
            } else if ($stock->remaining() > $temp_quanity) {
                $return_item->stock()->create([
                    'purchase_id' => $stock->purchase_id,
                    'purchase_item_id' => $stock->purchase_item_id,
                    'stock_id' => $stock->id,
                    'product_id' => $stock->product_id,
                    'qty' => $temp_quanity,
                    'out' => 0,
                ]);
                break;
            } else if ($stock->remaining() != 0 && $stock->remaining() < $temp_quanity) {
                $remaining_stock = $stock->remaining();
                $return_item->stock()->create([
                    'purchase_id' => $stock->purchase_id,
                    'purchase_item_id' => $stock->purchase_item_id,
                    'stock_id' => $stock->id,
                    'product_id' => $stock->product_id,
                    'qty' => $remaining_stock,
                    'out' => 0,
                ]);
                $temp_quanity -= $remaining_stock;
            }
        }
        // exit();
    }
}
