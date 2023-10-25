<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountJournal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Session;

class SaleReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showSaleReport(Request $request){
        $date_range = "";
        $catWhereArray = array();
        $itemWhereArray = array();
        $date = explode(' - ', $request->sale_date_range);
        $fromDate = Carbon::parse($date[0])->format('Y-m-d');
        $toDate = Carbon::parse($date[1])->format('Y-m-d');
        if(isset($fromDate) && isset($toDate) ){
            if(!empty($fromDate) && !empty($toDate)){
                $start_date = $fromDate;
                $end_date = $toDate;
                $date_range = "pos_invoice.invoice_date >= '". $start_date ."' AND pos_invoice.invoice_date < '". $end_date ."' + INTERVAL 1 DAY";
            }
        }
        if($request->sale_category>0){
            $catWhereArray = ['item.category_id' => $request->sale_category];
        }
        if($request->sale_item>0){
            $itemWhereArray = ['pos_invoice_detail.inv_item_id' => $request->sale_item];
        }
        $saleItems = DB::table('pos_invoice_detail')
                    ->select(
                        'pos_invoice_detail.inv_item_id AS item_id',  
                        'item.item_name', 
                        'item.item_code',   
                        'pos_invoice_detail.item_quantity',  
                        DB::raw('SUM(pos_invoice_detail.inv_item_subTotal) as item_sale'),
                        DB::raw('SUM(pos_invoice_detail.item_quantity*pos_invoice_detail.item_purchase_price) as item_purchase'),
                        'category.name AS category_name',
                        )
                    ->leftJoin('pos_invoice', 'pos_invoice_detail.inv_id', '=', 'pos_invoice.invoice_id')
                    ->leftJoin('item', 'pos_invoice_detail.inv_item_id', '=', 'item.id')
                    ->leftJoin('category', 'category.id', '=', 'item.category_id')
                    ->where('pos_invoice.invoice_status', '>=', 2)
                    ->where('pos_invoice.sale_return', 0)
                    ->where($catWhereArray)
                    ->where($itemWhereArray)
                    ->whereRaw($date_range)
                    ->groupBy('item.category_id')
                    ->groupBy('pos_invoice_detail.inv_item_id')
                    ->orderBy('category.name', 'ASC')
                    ->orderBy('item.item_name', 'ASC')
                    ->get();
                    $report = array();
                    $category = $catName = "";
                    $category_sales = $category_qty = $category_sale = $category_cost = $grand_total_qty = $grand_total_sale = $grand_total_cost = 0;
                    $count = 1;
        foreach ($saleItems as $key => $saleItem) {
            if($category!=$saleItem->category_name){
                if($category_sale!=0){
                    $report['records'][]     = array( 
                        'category_total'     => 'Total '.$category,   
                        'category_qty'       => number_format($category_qty,2,'.',''),   
                        'category_sale'      => number_format($category_sale,2,'.',''),                         
                        'category_cost'      => number_format($category_cost,2,'.',''),                              
                        'is_type'            => 'category_total'
                    );
                    $category_qty = $category_sale = $category_cost = 0;
                }
                $category = $saleItem->category_name;
            }

            if($catName!=$saleItem->category_name){
                $catName = $saleItem->category_name;
                $report['records'][] = array(
                    'category_name'  => $catName,                         
                    'is_type'        => 'category_name'
                );
            }

            $report['records'][]= array( 
                'count'         => $count,
                'item_id'       => $saleItem->item_id,
                'item_name'     => $saleItem->item_name,
                'item_quantity' => number_format($saleItem->item_quantity,2,'.',''),
                'item_sale'     => number_format($saleItem->item_sale,2,'.',''),                      
                'item_purchase' => number_format($saleItem->item_purchase,2,'.',''),
                'is_type'       => 'item_type',
            );
            $count++;
            $category_qty       = $category_qty + $saleItem->item_quantity;
            $category_sale      = $category_sale + $saleItem->item_sale;
            $category_cost      = $category_cost + $saleItem->item_purchase;

            $grand_total_qty    = $grand_total_qty + $saleItem->item_quantity;
            $grand_total_sale   = $grand_total_sale + $saleItem->item_sale;
            $grand_total_cost   = $grand_total_cost + $saleItem->item_purchase;
        }
        $report['records'][]    = array( 
            'category_total'    => 'Total '.$category,   
            'category_qty'      => number_format($category_qty,2,'.',''),   
            'category_sale'     => number_format($category_sale,2,'.',''),                         
            'category_cost'     => number_format($category_cost,2,'.',''),                              
            'is_type'           => 'category_total'
        );
        $report['records'][]    = array( 
            'grand_total'       => 'Grand Total',
            'grand_total_qty'   => number_format($grand_total_qty,2,'.',''),   
            'grand_total_sale'  => number_format($grand_total_sale,2,'.',''),                         
            'grand_total_cost'  => number_format($grand_total_cost,2,'.',''),                           
            'is_type'           => 'grand_total'
        );
        return view('showSaleReport', compact('report'));
    }
    public function showSaleInvoicesReport(Request $request){
        $date_range = "";
        $groupWhereArray = array();
        $customerWhereArray = array();
        $date = explode(' - ', $request->sale_rep_date_range);
        $fromDate = Carbon::parse($date[0])->format('Y-m-d');
        $toDate = Carbon::parse($date[1])->format('Y-m-d');
        if(isset($fromDate) && isset($toDate) ){
            if(!empty($fromDate) && !empty($toDate)){
                $start_date = $fromDate;
                $end_date = $toDate;
                $date_range = "pos_invoice.invoice_date >= '". $start_date ."' AND pos_invoice.invoice_date < '". $end_date ."' + INTERVAL 1 DAY";
            }
        }
        if($request->sale_cust_group>0){
            $groupWhereArray = ['customer.cust_group_id' => $request->sale_cust_group];
        }
        if($request->sale_customer>0){
            $customerWhereArray = ['customer.cust_id' => $request->sale_customer];
        }
        $saleInvoices = DB::table('pos_invoice')
                    ->select(
                        'pos_invoice.invoice_id',  
                        'pos_invoice.invoice_no',  
                        'pos_invoice.invoice_date',  
                        'pos_invoice.invoice_total',
                        'customer.cust_name',
                        'customer_groups.cust_group_name'
                        )
                    ->leftJoin('customer', 'pos_invoice.cust_id', '=', 'customer.cust_id')
                    ->leftJoin('customer_groups', 'customer.cust_group_id', '=', 'customer_groups.id')
                    ->where('pos_invoice.invoice_status', '>=', 1)
                    ->where('pos_invoice.sale_return', 0)
                    ->where($groupWhereArray)
                    ->where($customerWhereArray)
                    ->whereRaw($date_range)
                    ->groupBy('customer.cust_group_id')
                    ->groupBy('pos_invoice.invoice_id')
                    ->orderBy('customer_groups.cust_group_name', 'ASC')
                    ->orderBy('customer.cust_name', 'ASC')
                    ->get();
                    $report = array();
                    $custGroup = $groupName = "";
                    $group_sale = $grand_total_sale = 0;
                    $count = 1;

        foreach ($saleInvoices as $key => $saleInvoice) {
            if($custGroup!=$saleInvoice->cust_group_name){
                if($group_sale!=0){
                    $report['records'][]     = array( 
                        'group_total'        => 'Total '.$custGroup,     
                        'group_sale'         => number_format($group_sale,2,'.',''),                              
                        'is_type'            => 'group_total'
                    );
                    $group_sale = 0;
                }
                $custGroup = $saleInvoice->cust_group_name;
            }

            if($groupName!=$saleInvoice->cust_group_name){
                $groupName = $saleInvoice->cust_group_name;
                $report['records'][] = array(
                    'group_name'  => $groupName,                         
                    'is_type'        => 'group_name'
                );
            }

            $report['records'][]= array( 
                'count'         => $count,
                'customer_name'       => $saleInvoice->cust_name,
                'invoice_number'     => $saleInvoice->invoice_no,
                'invoice_id'     => $saleInvoice->invoice_id,
                'invoice_date' => $saleInvoice->invoice_date,
                'invoice_total'     => number_format($saleInvoice->invoice_total,2,'.',''),
                'is_type'       => 'invoices',
            );
            $count++;
            $group_sale      = $group_sale + $saleInvoice->invoice_total;
            $grand_total_sale   = $grand_total_sale + $saleInvoice->invoice_total;
        }
        $report['records'][]    = array( 
            'group_total'        => 'Total '.$custGroup,     
            'group_sale'         => number_format($group_sale,2,'.',''),                              
            'is_type'            => 'group_total'
        );
        $report['records'][]    = array( 
            'grand_total'       => 'Grand Total',   
            'grand_total_sale'  => number_format($grand_total_sale,2,'.',''),                          
            'is_type'           => 'grand_total'
        );
        return view('showSaleInvoicesReport', compact('report'));
    }
}
