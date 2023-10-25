<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountJournal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Session;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAddItemForm(){
        return view('addItemForm');
    }

    public function addNewItem(Request $request){
        $request->validate([
            'item_name'       => 'required|min:3',
            'category'        => 'required',
            'barcode'         => 'required',
            'purchase_price'  => 'required|numeric',
            'sale_price'      => 'required|numeric',
        ],[
            'item_name.required'        => 'The Item Name Field is required',
            'item_name.min'             => 'Item Name must be at least 3 character',
            'category.required'         => 'The Category Field is required',
            'barcode.required'          => 'The Barcode Field is required',
            'purchase_price.required'   => 'The Purchase Field is required',
            'purchase_price.numeric'    => 'The Purchase Value must be Numeric',
            'sale_price.required'       => 'The Sale Field is required',
            'sale_price.numeric'        => 'The Sale Value must be Numeric',
        ]);
        $quantity       = (isset($request->quantity) && !empty($request->quantity))?$request->quantity:0;
        $result         = str_replace("&amp;", '&', $request->item_name); 
        $name           = str_replace("&quot;", '"', $result);
        $NewName        = str_replace("'", "\'", $name);
        $barcode        = str_replace("'", "\'", $request->barcode);
        $avgCost        = $request->purchase_price;
        $itemDuplicateName  = DB::table('item')->where('item_name', $NewName)->get();
        if(count($itemDuplicateName)>0){
            return redirect()->back()->with('error', 'Item Can Not Added With Duplicate Name');
        }
        $itemDuplicateCode  = DB::table('item')->where('item_code', $barcode)->get();
        if(count($itemDuplicateCode)>0){
            return redirect()->back()->with('error', 'Item Can Not Added With Duplicate Barcode');
        }
        $id = DB::table('item')->insertGetId([
            'item_name'         => $NewName,
            'item_code'         => $barcode,
            'category_id'       => $request->category,
            'type_id'           => 1,
            'picture'           => '',
            'sort_order'        => $this->getOrder($request->category),
            'quantity'          => $quantity,
            'reorder_point'     => 0,
            'part_number'       => '',
            'normal_price'      => $avgCost,
            'weight'            => 0,
            'avg_cost'          => $avgCost,
            'sale_price'        => $request->sale_price,
            'cogs_acc'          => 2,
            'sale_acc'          => 5,
            'unit'              => 1,
            'asset_acc'         => 1,
            'item_map_id'       => 0,
            'vendor'            => $request->acc_vendor_id,
            'added_date'        => Carbon::parse(now()->format('Y-m-d H:i:s')),
            'sale_unit'         => 1,
            'purchase_unit'     => 1
        ]);
        DB::table('unit_mapping')->insert([
            'item_id'         => $id,
            'uom_id'          => 0,
            'unit_id'         => 1,
            'conv_from'       => 1,
            'conv_to'         => 1,
            'sale_price'      => $request->sale_price,
            'created_date'    => Carbon::parse(now()->format('Y-m-d H:i:s')),
            'updated_date'    => Carbon::parse(now()->format('Y-m-d H:i:s')),
        ]);
        DB::table('uom_barcodes')->insert([
            'item_id'         => $id,
            'uom_id'          => 0,
            'barcode'         => $barcode,
            'upc'             => 1
        ]);
        DB::table('item_warehouse')->insert([
            'inv_id'         => 0,
            'item_id'        => $id,
            'qty'            => $quantity,
            'conv_from'      => 1,
            'warehouse_id'   => 1,
            'unit_id'        => 1,
            'invoice_type'   => 6,
            'invoice_status' => '{{Opening Quantity}}',
            'inv_date'       => Carbon::parse(now()->format('Y-m-d')),
        ]);
        if($quantity!=0){
            $total_asset = $quantity*$avgCost;
            $j_id = DB::table('account_journal')->insertGetId([
                'acc_id'         => 1,
                'journal_amount' => $total_asset,
                'journal_details'=> 'Item Stock Entry',
                'inv_id'         => 0,
                'item_id'        => $id,
                'currency_rate'  => 1,
                'currency_id'    => 1,
                'type'           => 'E',
                'entry_date'     => Carbon::parse(now()->format('Y-m-d H:i:s'))
            ]);
            DB::table('account_journal')->where('journal_id', $j_id)->update(['ref_id' => $j_id]);
            DB::table('account_journal')->insertGetId([
                'acc_id'         => 6,
                'journal_amount' => -1*$total_asset,
                'journal_details'=> 'Item Stock Entry',
                'inv_id'         => 0,
                'ref_id'         => $j_id,
                'item_id'        => 0,
                'currency_rate'  => 1,
                'currency_id'    => 1,
                'type'           => 'E',
                'entry_date'     => Carbon::parse(now()->format('Y-m-d H:i:s'))
            ]);
        }
        return redirect()->back()->with('success', 'Item Added Successfully');
    }
    public function getOrder($cat_id){
        $order = DB::table('item')->where('category_id', $cat_id)->get();
        return intval(count($order))+1;
    }
}
