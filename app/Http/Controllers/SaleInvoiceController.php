<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountJournal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Session;


class SaleInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function newSaleInvoice($invoice_id=NULL){
        if($invoice_id!=NULL && $invoice_id>0){
            $invoice = DB::table('pos_invoice')->where('invoice_id',$invoice_id)->whereIn('invoice_type', [2,4,5])->first();
            $invoiceDetails = DB::table('pos_invoice_detail')->where('inv_id',$invoice_id)->get();
            $invoiceItems = $invoiceDetails->count();
        } else {
            $invoice = NULL;
            $invoiceDetails = NULL;
            $invoiceItems = 0;
        }
        return view('saleInvoice', compact('invoice_id', 'invoice', 'invoiceDetails', 'invoiceItems'));
    }

    public function createSaleInvoice(Request $request){
        $request->validate([
            'inv_customer'=> 'required',
            'sale_invoice_item' => 'required',
            'item_uom'          => 'required|numeric',
            'quantity'          => 'required|numeric|min:1',
            'unit_price'        => 'required|numeric|min:1',
            'sub_total'         => 'required|numeric|min:1',
        ]);

        $id = 0;
        $customerDetails = DB::table('customer')->where('cust_id', $request->inv_customer)->first();
        if($request->invoice_id!=NULL && $request->invoice_id>0){
            $prebalance = DB::table('account_journal')->where('acc_id', $customerDetails->cust_acc_id)->where('inv_id', '!=', $request->invoice_id*-1)->sum('journal_amount');
            if($prebalance>=2){
                $prev = $prebalance;
            } else {
                $prev = 1;
            }
            $id = $request->invoice_id;
            DB::table('pos_invoice')->where('invoice_id', $request->invoice_id)
                ->update([
                    'cust_id' => $request->inv_customer, 
                    'cust_name' => $customerDetails->cust_name,
                    'cust_mobile_no' => $customerDetails->cust_mobile,
                    'salesrep_id' => $request->refresentative
                ]);
        } else {
            $invNumber = DB::table('pos_invoice')->select('invoice_no')->where('invoice_type', 2)->orderBy('invoice_no', 'DESC')->first();
            $prebalance = DB::table('account_journal')->where(['acc_id' => $customerDetails->cust_acc_id])->sum('journal_amount');
            if($prebalance>=2){ $prev = $prebalance; } else { $prev = 1; }
            $id = DB::table('pos_invoice')->insertGetId([
                'cust_id' => $request->inv_customer,
                'invoice_date' => Carbon::parse(now()->format('Y-m-d H:i:s')),
                'updated_date' => Carbon::parse(now()->format('Y-m-d H:i:s')),
                'paid_date' => Carbon::parse(now()->format('Y-m-d H:i:s')),
                'invoice_no' => $invNumber->invoice_no+1,
                'invoice_status' => 1,
                'invoice_type' => 2,
                'in_print' => 1,
                'cust_name' => $customerDetails->cust_name,
                'cust_mobile_no' => $customerDetails->cust_mobile,
                'discount' => 0,
                'discount_invoice' => 0,
                'invoice_paid' => 0,
                'in_email' => 0,
                'previous_balance' => $prev,
                'salesrep_id' => $request->refresentative,
                'custom' => '',
                'invoice_name' => '{{Sales Order}}',
                'invoice_total' => 0,
                'last_po_id' => 0,
            ]);
        }
        $itemDetails = DB::table('item')->where('id', $request->sale_invoice_item)->first();
        $itemUnitDetails = DB::table('unit_mapping')
                            ->select(
                                'unit_mapping.unit_id',
                                'unit_mapping.conv_from',
                                'units.name AS unit_name'
                                )
                                ->leftJoin('units', 'unit_mapping.unit_id', '=', 'units.id')
                                ->where('unit_mapping.unit_id', $request->item_uom)->where('unit_mapping.item_id', $request->sale_invoice_item)
                                ->first();
                                $invItemQty =  $request->quantity*$itemUnitDetails->conv_from; 
                                $dic = $request->item_discount ? $request->item_discount : 0;
            DB::table('pos_invoice_detail')->insert([
                'inv_id' => $id,
                'inv_item_id' => $request->sale_invoice_item,
                'inv_item_name' => $itemDetails->item_name,
                'item_quantity' => $request->quantity,
                'conv_from' => $itemUnitDetails->conv_from,
                'inv_item_quantity' => $invItemQty,
                'bonus_qty' => 0,
                'unit_id' => $itemUnitDetails->unit_id,
                'warehouse_id' => $request->warehouse,
                'unit_name' => $itemUnitDetails->unit_name,
                'inv_item_price' => $request->net_price,
                'item_purchase_price' => $invItemQty*$itemDetails->normal_price,
                'inv_item_subTotal' => $request->sub_total,
                'inv_item_discount' => $dic
            ]);
            DB::table('pos_invoice')->where('invoice_id', $id)
                ->update(['invoice_total' => DB::table('pos_invoice_detail')->where(['inv_id' => $id])->sum('inv_item_subTotal')]);

            DB::table('item_warehouse')->insert([
                'inv_id' => $id,
                'item_id' => $request->sale_invoice_item,
                'qty' => -1*$request->quantity,
                'conv_from' => $itemUnitDetails->conv_from,
                'warehouse_id' => $request->warehouse,
                'unit_id' => $itemUnitDetails->unit_id,
                'invoice_type' => 2,
                'invoice_status' => "{{Sale Order}}",
                'inv_date' => Carbon::parse(now())->format('Y-m-d')
            ]);
            //Journal Entries For Item Sale Prices
            $assetsValue = $request->quantity*$request->unit_price;
            $journalID = DB::table('account_journal')->insertGetId([
                'acc_id' => $customerDetails->cust_acc_id,
                'journal_amount' => $assetsValue,
                'journal_details' => 'Sale Item',
                'inv_id' => $id,
                'item_id' => $request->sale_invoice_item,
                'currency_rate' => 1,
                'type' => 'S',
                'currency_id' => 1,
                'entry_date' => Carbon::parse(now())->format('Y-m-d H:i:s')
            ]);
            DB::table('account_journal')->where('journal_id', $journalID)->update(['ref_id' => $journalID]);
            DB::table('account_journal')->insertGetId([
                'acc_id' => $itemDetails->asset_acc,
                'journal_amount' => -1*$assetsValue,
                'journal_details' => 'Sale Item',
                'inv_id' => $id,
                'ref_id' => $journalID,
                'item_id' => 0,
                'currency_rate' => 1,
                'type' => 'S',
                'currency_id' => 1,
                'entry_date' => Carbon::parse(now())->format('Y-m-d H:i:s')
            ]);

            //Journal Entries For Item Costs
            $costsValue = ($request->quantity*$itemUnitDetails->conv_from)*$itemDetails->normal_price;
            $costJournalID = DB::table('account_journal')->insertGetId([
                'acc_id' => $itemDetails->cogs_acc,
                'journal_amount' => $costsValue,
                'journal_details' => 'Sale Item',
                'inv_id' => $id,
                'item_id' => 0,
                'currency_rate' => 1,
                'type' => 'S',
                'currency_id' => 1,
                'entry_date' => Carbon::parse(now())->format('Y-m-d H:i:s')
            ]);
            DB::table('account_journal')->where('journal_id', $costJournalID)->update(['ref_id' => $costJournalID]);
            DB::table('account_journal')->insert([
                'acc_id' => $itemDetails->asset_acc,
                'journal_amount' => -1*$costsValue,
                'journal_details' => 'Sale Item',
                'inv_id' => $id,
                'ref_id' => $costJournalID,
                'item_id' => 0,
                'currency_rate' => 1,
                'type' => 'S',
                'currency_id' => 1,
                'entry_date' => Carbon::parse(now())->format('Y-m-d H:i:s')
            ]);


            //Journal Entries For Discount Amount
            $invDisc = DB::table('pos_invoice')->select('discount_invoice')->where(['invoice_id' => $id])->first();
            $itemDiscount = DB::table('pos_invoice_detail')->where('inv_id', '=', $id)->select(DB::raw('SUM((item_quantity*inv_item_price)-(inv_item_subTotal)) as item_dis'))->first();
            //dd($itemDiscount->item_dis);
            $dis = ($invDisc->discount_invoice ?? 0) + ($itemDiscount->item_dis ?? 0);
            if($dis>0){
                DB::table('account_journal')->where('inv_id', -1*$id)->where('type', '=', 'DIS')->delete();
                $discAcc = DB::table('account_chart')->select('acc_id')->where('acc_name', '=', 'Discount')->where('acc_description', '=', '{{DISCOUNT_ACCOUNT_SYSTEM}}')->first();
                $disJournalID = DB::table('account_journal')->insertGetId([
                    'acc_id' => $discAcc->acc_id,
                    'journal_amount' => $dis,
                    'journal_details' => 'Sale Item',
                    'inv_id' => $id,
                    'item_id' => 0,
                    'currency_rate' => 1,
                    'type' => 'DIS',
                    'currency_id' => 1,
                    'entry_date' => Carbon::parse(now())->format('Y-m-d H:i:s')
                ]);
                DB::table('account_journal')->where('journal_id', $disJournalID)->update(['ref_id' => $disJournalID]);
                DB::table('account_journal')->insert([
                    'acc_id' => $customerDetails->cust_acc_id,
                    'journal_amount' => -1*$dis,
                    'journal_details' => 'Sale Item',
                    'inv_id' => $id,
                    'ref_id' => $disJournalID,
                    'item_id' => 0,
                    'currency_rate' => 1,
                    'type' => 'DIS',
                    'currency_id' => 1,
                    'entry_date' => Carbon::parse(now())->format('Y-m-d H:i:s')
                ]);
            }
            return redirect('newSaleInvoice/'.$id)->with('success', 'Invoice Created');
    }

    
}
