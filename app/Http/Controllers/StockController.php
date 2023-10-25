<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showStockReport(Request $request){
        $warehousesName = $itemwhere = array(); 
        $warehouse_qty = 0;
        $where = array();
        $report = array();
        $fromDate = Carbon::parse($request->stock_date)->format('Y-m-d');
        if(!empty($fromDate)){
            $date = "item_warehouse.inv_date <= '".$fromDate."'";
        }
        if($request->warehouse!=""){
             $where = ['item_warehouse.warehouse_id' => $request->warehouse];
        }
        if($request->item!=""){
            $itemwhere = ['item_warehouse.item_id' => $request->item];
        }
        //DB::enableQueryLog();
        $warehousesItems = DB::table('item_warehouse')
                    ->select(DB::raw(
                        "(sum(item_warehouse.qty*item_warehouse.conv_from)) itemQty"), 
                        'item_warehouse.item_id', 
                        'item.item_name', 
                        'item.item_code', 
                        'item.sale_price', 
                        'item.normal_price', 
                        'category.name AS category_name',
                        'warehouses.warehouse_name', 
                        'item_warehouse.warehouse_id' 
                    )
                    ->leftJoin('item', 'item_warehouse.item_id', '=', 'item.id')
                    ->leftJoin('category', 'item.category_id', '=', 'category.id')
                    ->leftJoin('warehouses', 'item_warehouse.warehouse_id', '=', 'warehouses.warehouse_id')
                    ->where('warehouses.warehouse_isactive','=', 1)
                    ->where('item.item_status', 1)
                    ->where('item.type_id', '!=', 3)
                    ->where($where)
                    ->whereRaw($date)
                    ->where($itemwhere)
                    ->having('itemQty', '>', 0)
                    ->groupBy('item_warehouse.warehouse_id')
                    ->groupBy('item_warehouse.item_id')
                    ->orderBy('warehouses.warehouse_name', 'ASC')
                    ->orderBy('item.item_name', 'ASC')
                    ->get();
                    
                  //dd(DB::getQueryLog());
        foreach ($warehousesItems as $warehousesItem){
            if($warehousesName != $warehousesItem->warehouse_name){
                if($warehouse_qty !=0) {                          
                    $report['records'][] = array( 
                         'ware_total'       =>'Total '.$warehousesName,   
                         'ware_qty'       => number_format($warehouse_qty,2,'.',''),                                                           
                         'is_type'       => 'ware_total'
                    );                            
                    $warehouse_qty = 0;                             
                }
                $warehousesName = $warehousesItem->warehouse_name;
                $report['records'][]= array(
                    'warehouse_name' => $warehousesName ,
                    'is_type'        => 'warehouse'
                );
            }

            $report['records'][] = array( 
                 'item_id'       =>$warehousesItem->item_id,
                 'item_name'     =>$warehousesItem->item_name,
                 'item_code'     =>$warehousesItem->item_code,
                 'sale_price'    =>$warehousesItem->sale_price,
                 'normal_price'  =>$warehousesItem->normal_price,
                 'item_quantity' =>$warehousesItem->itemQty,
                 'category_name' =>$warehousesItem->category_name,
                 'is_type'       => 'entry'
            );
            $warehouse_qty = $warehouse_qty + $warehousesItem->itemQty;
        } 
        if($warehouse_qty>0){
            $report['records'][] = array( 
                'ware_total'       =>'Total '.$warehousesName,   
                'ware_qty'       => number_format($warehouse_qty,2,'.',''),                                                           
                'is_type'       => 'ware_total'
            );
        }      
        return view('showStock', compact('report'));
                                                              
    }

    public function showCategoryStockReport(Request $request){
        $categoryName = $itemwhere = array(); 
        $category_qty = 0;
        $where = array();
        $report = array();
        $fromDate = Carbon::parse($request->stock_date)->format('Y-m-d');
        if(!empty($fromDate)){
            $date = "item_warehouse.inv_date <= '".$fromDate."'";
        }
        if($request->category!=""){
             $where = ['item.category_id' => $request->category];
        }
        if($request->item!=""){
            $itemwhere = ['item_warehouse.item_id' => $request->item];
        }
        //DB::enableQueryLog();
        $categoriesItems = DB::table('item_warehouse')
                    ->select(DB::raw(
                        "(sum(item_warehouse.qty*item_warehouse.conv_from)) itemQty"), 
                        'item_warehouse.item_id', 
                        'item.item_name', 
                        'item.item_code', 
                        'item.sale_price', 
                        'item.normal_price', 
                        'category.name AS category_name' 
                    )
                    ->leftJoin('item', 'item_warehouse.item_id', '=', 'item.id')
                    ->leftJoin('category', 'item.category_id', '=', 'category.id')
                    ->where('item.item_status', 1)
                    ->where('item.type_id', '!=', 3)
                    ->where($where)
                    ->whereRaw($date)
                    ->where($itemwhere)
                    ->having('itemQty', '>', 0)
                    ->groupBy('item.category_id')
                    ->groupBy('item_warehouse.item_id')
                    ->orderBy('category.name', 'ASC')
                    ->orderBy('item.item_name', 'ASC')
                    ->get();
                    
                  //dd(DB::getQueryLog());
        foreach ($categoriesItems as $categoriesItem){
            if($categoryName != $categoriesItem->category_name){
                if($category_qty !=0) {                          
                    $report['records'][] = array( 
                         'cat_total'       =>'Total '.$categoryName,   
                         'cat_qty'       => number_format($category_qty,2,'.',''),                                                           
                         'is_type'       => 'cat_total'
                    );                            
                    $category_qty = 0;                             
                }
                $categoryName = $categoriesItem->category_name;
                $report['records'][]= array(
                    'category_name' => $categoryName ,
                    'is_type'        => 'category'
                );
            }

            $report['records'][] = array( 
                 'item_id'       =>$categoriesItem->item_id,
                 'item_name'     =>$categoriesItem->item_name,
                 'item_code'     =>$categoriesItem->item_code,
                 'sale_price'    =>$categoriesItem->sale_price,
                 'normal_price'  =>$categoriesItem->normal_price,
                 'item_quantity' =>$categoriesItem->itemQty,
                 'category_name' =>$categoriesItem->category_name,
                 'is_type'       => 'entry'
            );
            $category_qty = $category_qty + $categoriesItem->itemQty;
        } 
        if($category_qty>0){
            $report['records'][] = array( 
                'cat_total'       =>'Total '.$categoryName,   
                'cat_qty'       => number_format($category_qty,2,'.',''),                                                           
                'is_type'       => 'cat_total'
            );
        }      
        return view('showCategoryStock', compact('report'));     
                                                              
    }

    //Get Customer By Regions
    public function getItemsByWarehouse($ware_id){
        $where = array();
        if($ware_id>0){
            $where = ['item_warehouse.warehouse_id' => $ware_id];
        }
        $data = DB::table('item_warehouse')
                ->select('item_warehouse.item_id AS id', 'item.item_name', 'item.item_code')
                ->leftJoin('item', 'item_warehouse.item_id', '=', 'item.id')
                ->where($where)
                ->where('item.item_status', 1)
                ->where('item.type_id', 1)
                ->groupBy('item_warehouse.item_id')
                ->orderBy('item.item_name', 'ASC')
                ->get();
        return response()->json(['data' => $data]);
    }
    //Get Item Details
    public function getItemsDetails($item_id){
        $where = array();
        if($item_id>0){
            $where = ['id' => $item_id];
        }
        $data = DB::table('item')
                ->where('item_status', 1)
                ->where('type_id', 1)
                ->where($where)
                ->first();
        $units = DB::table('unit_mapping')
                ->select('unit_mapping.uom_id', 'unit_mapping.unit_id', 'unit_mapping.conv_from', 'unit_mapping.conv_to', 'unit_mapping.sale_price', 'units.name')
                ->leftJoin('units', 'unit_mapping.unit_id', '=', 'units.id')
                ->where('unit_mapping.item_id', $item_id)
                ->get();
        return response()->json(['data' => $data, 'unit' => $units]);
    }

    public function getUnitPrice($item_id, $unit_id){
        $data = DB::table('unit_mapping')->select('sale_price')
                ->where('item_id', $item_id)
                ->where('unit_id', $unit_id)
                ->first();
        return response()->json(['data' => $data->sale_price]);
    }

    //Get Customer By Regions
    public function getItemsByCategory($cat_id){
        $where = array();
        if($cat_id>0){
            $where = ['category_id' => $cat_id];
        }
        $data = DB::table('item')
                ->select('id', 'item_name', 'item_code')
                ->where('item_status', 1)
                ->where('type_id', 1)
                ->where($where)
                ->groupBy('id')
                ->orderBy('item_name', 'ASC')
                ->get();
        return response()->json(['data' => $data]);
    }
}
