<?php

namespace App\Http\Controllers;




use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountJournal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Session;

use function PHPSTORM_META\type;

class RegisterController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    //Show Register
    public function showRegister($type){
        if($type=='Customer' || $type=='Vendor' || $type=='Bank' || $type=='Expense' || $type=='Loan'){
            if($type=='Customer'){
                $data = DB::table('customer')->where('cust_status', 1)->where('cust_group_id', 1)->where('cust_id', '>', 0)->orderBy('cust_name', 'ASC')->get();
            } elseif ($type == 'Vendor'){
                $data = DB::table('vendor')->orderBy('vendor_name', 'ASC')->get();
            } elseif ($type == 'Bank'){
                $data = DB::table('account_chart')->where('acc_type_id', 8)->get();
            } elseif ($type == 'Expense'){
                $data = DB::table('account_chart')->where('acc_type_id', 5)->get();
            } elseif ($type == 'Loan'){
                $data = DB::table('account_chart')->whereIn('acc_type_id', [14, 15])->get();
            } else {
                return redirect('/')->with('error', "You have no access to this Web Page");
            }
            return view('showRegister', compact('data', 'type'));
        } else {
            return redirect('/')->with('error', "You have no access to this Web Page");
        }
    }

    //Show Register Details
    public function showRegisterDetails(Request $request){
        $type = $request->type;
        if($request->type=='Customer'){
            $request->validate([
                'customer' => 'required',
                'date_range' => 'required',
            ],[
                'customer.required' => 'The Customer Field is required',
                'date_range.required' => 'The Date Field is required',
            ]);
            $date = explode(' - ', $request->date_range);
            $fromDate = Carbon::parse($date[0])->format('Y-m-d');
            $toDate = Carbon::parse($date[1])->format('Y-m-d');
            $accounts_array = array();
            $balance = 0;
            $i=0;
            $add_sale = 0;
            $sale_return = false;
            $acc_id = DB::table('customer')->select('cust_acc_id AS acc_id', 'cust_name AS acc_name')->where('cust_id', $request->customer)->first();
            $pre_row = DB::table('account_journal')
                    ->select('customer.cust_name', 'account_chart.acc_name', DB::raw('SUM(account_journal.journal_amount) as pre_total'))
                    ->join('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                    ->join('customer', 'customer.cust_acc_id', '=', 'account_journal.acc_id')
                    ->where('account_journal.acc_id', '=', $acc_id->acc_id)
                    ->where('account_journal.entry_date', '<', $fromDate)
                    ->groupBy('customer.cust_name', 'account_chart.acc_name', 'account_journal.acc_id')
                    ->first();
            if($pre_row!=NULL and $pre_row->pre_total!=NULL and $pre_row->pre_total!=0 ){
                $accounts_array['register'][] = array(
                        'id' => '',                                                        
                        'payee'=> $pre_row->cust_name,
                        'acc_id' => '',
                        'number'=> "",                            
                        'ref_id'=> "",                            
                        'date'      => $date[0],
                        'account'       => '',         
                        'detail'       =>  'Previous Balance',
                        'increase'       => "",
                        'decrease'       => "",
                        'balance'       => $pre_row->pre_total
                );
                $balance = $pre_row->pre_total;
            }
            
            $custRecs = DB::table('account_journal')
                    ->select('account_journal.*', 'customer.cust_name', 'customer.cust_mobile', 'account_chart.acc_name')
                    ->join('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                    ->join('customer', 'customer.cust_acc_id', '=', 'account_journal.acc_id')
                    ->where('account_journal.acc_id', '=', $acc_id->acc_id)
                    ->whereRaw("account_journal.entry_date >= '". $fromDate ."' AND account_journal.entry_date < '". $toDate ."' + INTERVAL 1 DAY")
                    // ->where('account_journal.entry_date', '>=', $fromDate)
                    // ->where('account_journal.entry_date', '<', $toDate)
                    ->orderBy('account_journal.entry_date', 'ASC')
                    ->orderBy('account_journal.type', 'DESC')
                    ->get();
                    
            $register_accounts = array();
            foreach ($custRecs as $custRec) {
                $custRef = DB::table('account_journal')
                        ->select('account_journal.*', 'account_chart.acc_name')
                        ->join('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                        ->where('account_journal.acc_id', '!=', $custRec->acc_id)
                        ->where('account_journal.ref_id', '=', $custRec->ref_id)
                        ->first();

                $register_accounts[] = array(
                    'journal_id'             => $custRef->journal_id,
                    'acc_name'             => $custRef->acc_name,
                    'acc_id'             => $custRef->acc_id,
                    'description'            => $custRef->journal_details,
                    'cust_name'             => $custRec->cust_name,
                    'cust_mobile'             => $custRec->cust_mobile,
                    'ref_id'                  => $custRec->ref_id,
                    'journal_type'             => $custRec->type,
                    'num'                  => $custRec->inv_id*-1,
                    'entry_date'                  => $custRec->entry_date,                        
                    'journal_amount'                => $custRec->journal_amount
                );
            }
            
            foreach ($register_accounts as $result) {                                      
                $increase = $result['journal_amount']>0?$result['journal_amount']:0;
                $decrease =  $result['journal_amount']<0?-1*$result['journal_amount']:0;
                $balance = $balance + $increase + -1*($decrease);                   
                if($result["num"]>0 &&  $i+1 < count($register_accounts) && $register_accounts[$i+1]["num"]==$result["num"] && (($register_accounts[$i+1]["acc_id"]==$result["acc_id"] && ($register_accounts[$i+1]["acc_id"]=="5"  || $register_accounts[$i + 1]["acc_id"] == "11")) || $register_accounts[$i+1]["journal_type"]=="DIS")){                                                                 
                if($register_accounts[$i]["journal_type"]=="DIS"){
                    $add_sale = $add_sale - $increase ;                       
                    } else {
                        if($increase>0){
                        $add_sale = $add_sale +$increase ;                       
                        } else {
                        $add_sale = $add_sale +$decrease ;                       
                        $sale_return = true;
                        }
                    }                        
                } else {                                
                    if($sale_return==false){                                                   
                        $increase = $increase +$add_sale;
                        $add_sale = 0; 
                        if($result["journal_type"]=="DIS"){                                                                                    
                            $increase = $increase - $decrease;
                            $decrease = 0;
                        }
                    } else {
                        $sale_return = false;
                        $decrease = $decrease +$add_sale;
                        $add_sale = 0; 
                        if($result["journal_type"]=="DIS"){                                                                                    
                            $decrease = $decrease - $increase;
                            $increase = 0;
                        }
                    }
                    $msg = '';
                    $sales_rep_name = '';
                    $invNo = '';
                    $desc = DB::table('pos_invoice')->select('invoice_no', 'invoice_type')->where('invoice_id', $result['num'])->first();
                    if($desc){
                        $invoice_no_prefix = "";
                        if($desc->invoice_type!= NULL){
                            if($desc->invoice_type=='1'){
                                $invoice_no_prefix = "POS-";
                            } elseif($desc->invoice_type=='2'){
                                $invoice_no_prefix = "SALE-";
                            } elseif($desc->invoice_type=='3'){
                                $invoice_no_prefix = "POS-RET-";
                            } elseif($desc->invoice_type=='4'){
                                $invoice_no_prefix = "SALE-RET-";
                            }
                        }
                        $invNo = $invoice_no_prefix.$desc->invoice_no;
                    }
                    $saleRef = DB::table('salesrep_detail')
                                    ->select('salesrep_detail.salesrep_id', 'salesrep.salesrep_name')
                                    ->join('salesrep', 'salesrep.id', '=', 'salesrep_detail.salesrep_id')
                                    ->where('salesrep_detail.ref_id','=', $result['ref_id'])
                                    ->first();
                    if($saleRef){
                        $sales_rep_name = " - Sales Rep ( ".$saleRef->salesrep_name." )";
                    } else {
                        $sales_rep_name = "";
                    }
                    $description = $result['num']?$msg:$result['description'];
                    $salesrepdetail = $sales_rep_name;
                    $accounts_array['register'][] = array(
                        'id' => $result['journal_id'],                                                        
                        'payee'=> $result['cust_name'],
                        'cust_mobile'=> $result['cust_mobile'],
                        'acc_id' => $result["acc_id"],
                        'number'=> $result['num']?$invNo:"",                            
                        'ref_id'=> $result['ref_id'],                            
                        'date'      => Carbon::parse($result['entry_date'])->format('Y-m-d'),
                        'account'       => $result['acc_name'],                            
                        'detail'       =>  $description . $salesrepdetail,
                        'increase'       => $increase==0?'':$increase,
                        'decrease'       =>  $decrease==0?'':$decrease ,
                        'balance'       => $balance
                    );
                } 
                    $i = $i + 1;
            }
        } elseif($request->type=='Vendor'){
            $request->validate([
                'vendor' => 'required',
                'date_range' => 'required'
            ],[
                'vendor.required' => 'The Vendor Field is required',
                'date_range.required' => 'The Date Field is required'
            ]);
            $date = explode(' - ', $request->date_range);
            $fromDate = Carbon::parse($date[0])->format('Y-m-d');
            $toDate = Carbon::parse($date[1])->format('Y-m-d');
            $accounts_array = array();
            $balance = 0;
            $i=0;
            $number ="";
            $add_sale = 0;
            $po_return = false;
            $acc_id = DB::table('vendor')->select('vendor_acc_id AS acc_id', 'vendor_name AS acc_name')->where('vendor_id', $request->vendor)->first();
            $pre_row = DB::table('account_journal')
                    ->select('vendor.vendor_name', 'account_chart.acc_name', DB::raw('SUM(account_journal.journal_amount) as pre_total'))
                    ->leftJoin('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                    ->leftJoin('vendor', 'vendor.vendor_acc_id', '=', 'account_journal.acc_id')
                    ->where('account_journal.acc_id', '=', $acc_id->acc_id)
                    ->where('account_journal.entry_date', '<', $fromDate)
                    ->groupBy('vendor.vendor_name', 'account_chart.acc_name', 'account_journal.acc_id')
                    ->first();
            if($pre_row and $pre_row->pre_total!=NULL and $pre_row->pre_total!=0 ){
                $accounts_array['register'][] = array(
                        'id' => '',                                                        
                        'payee'=> $pre_row->vendor_name,
                        'acc_id' => '',
                        'number'=> "",                            
                        'ref_id'=> "",                            
                        'date'      => $date[0],
                        'account'       => '',         
                        'detail'       =>  'Previous Balance',
                        'increase'       => "",
                        'decrease'       => "",
                        'balance'       => $pre_row->pre_total*-1
                );
                $balance = $pre_row->pre_total*-1;
            }
            
            $vendRecs = DB::table('account_journal')
                        ->select('account_journal.*', 'vendor.vendor_name', 'account_chart.acc_name')
                        ->leftJoin('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                        ->leftJoin('vendor', 'vendor.vendor_acc_id', '=', 'account_journal.acc_id')
                        ->where('account_journal.acc_id', '=', $acc_id->acc_id)
                        ->where('account_journal.type', '!=', 'PO_RET_A')
                        ->whereRaw("account_journal.entry_date >= '". $fromDate ."' AND account_journal.entry_date < '". $toDate ."' + INTERVAL 1 DAY")
                        ->orderBy('account_journal.entry_date', 'ASC')
                        ->orderBy('account_journal.type', 'DESC')
                        ->get();
                      
            $register_accounts = array();
            foreach ($vendRecs as $vendorRec) {
                $vendRef = DB::table('account_journal')
                        ->select('account_journal.*', 'account_chart.acc_name')
                        ->leftJoin('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                        ->where('account_journal.acc_id', '!=', $vendorRec->acc_id)
                        ->where('account_journal.ref_id', '=', $vendorRec->ref_id)
                        ->where('account_journal.type', '!=', 'PO_RET_A')
                        ->first();
                $amount = $vendorRec->journal_amount;
                if($vendorRec->type=="P_DIS"){
                    $amount = -1 * $amount;
                }
                $register_accounts[] = array(
                    'journal_id'             => $vendRef->journal_id,
                    'acc_name'             => $vendRef->acc_name,
                    'acc_id'             => $vendRef->acc_id,
                    'description'            => $vendRef->journal_details,
                    'vendor_name'             => $vendorRec->vendor_name,
                    'ref_id'                  => $vendorRec->ref_id,
                    'journal_type'             => $vendorRec->type,
                    'num'                  => $vendorRec->inv_id,
                    'entry_date'                  => $vendorRec->entry_date,                        
                    'journal_amount'                => $amount
                );
            }
            foreach ($register_accounts as $result) {                                      
                if(isset($data['loans']) && $data['loans']==1) {
                   $decrease = $result['journal_amount']>0?$result['journal_amount']:0;
                   $increase =  $result['journal_amount']<0?-1*$result['journal_amount']:0;                   
                } else {
                    $increase = $result['journal_amount']<0?-1*$result['journal_amount']:0;
                    $decrease =  $result['journal_amount']>0?$result['journal_amount']:0;                   
                }
                
                if($result['num']){
                   $number = $result['num']<0 ? -1*$result['num']:$result['num'];
                }

                if($result["journal_type"]=="P_DIS"){
                    $increase = -1 * $increase;
                }
                $balance = $balance + $increase + -1*($decrease);                  
                if( $result["num"]>0 && $i+1 < count($register_accounts) && $register_accounts[$i+1]["num"]==$result["num"] && $register_accounts[$i+1]["acc_id"]==$result["acc_id"] && $register_accounts[$i+1]["acc_id"]=="1"){
                    if($increase>0){
                        $add_sale = $add_sale +$increase;
                    } else {
                       $po_return = true;
                       $add_sale = $add_sale +$decrease;
                   }
                } else {
                    if($po_return==false){
                       $increase = $increase +$add_sale;
                    } else {
                       $po_return = false;
                       $decrease = $decrease +$add_sale;
                    }

                    if($result["acc_id"]==10 && $increase<0){
                         $increase = -1 * $increase;
                    }
                       
                    $add_sale = 0;
                    $sales_rep_name = '';
                     $saleRef = DB::table('salesrep_detail')
                                 ->select('salesrep_detail.salesrep_id', 'salesrep.salesrep_name')
                                 ->leftJoin('salesrep', 'salesrep.id', '=', 'salesrep_detail.salesrep_id')
                                 ->where('salesrep_detail.ref_id','=', $result['ref_id'])
                                 ->first();
                     if($saleRef){
                         $sales_rep_name = " - Sales Rep ( ".$saleRef->salesrep_name." )";
                     } else {
                         $sales_rep_name = "";
                     }
                    $desc = DB::table('po_invoice')->select('invoice_no', 'invoice_type')->where('invoice_id', $number)->first();
                    if($desc){
                        $inv_type = $desc->invoice_type;
                        $invoice_no_prefix = "";
                        if($inv_type!= NULL){
                            if($inv_type=='1'){
                                $invoice_no_prefix = "PO-";
                            } elseif($inv_type=='2'){
                                $invoice_no_prefix = "PO-RET-";
                            }
                        }
                        $invNo = $desc->invoice_no==NULL ? "": ($invoice_no_prefix.$desc->invoice_no) ;
                    }
                    $accounts_array['register'][] = array(
                        'id' => $result['journal_id'],                                                        
                        'payee'=> $result['vendor_name'],
                        'number'=> $result['num']?$invNo:"",       
                        'acc_id' => $result["acc_id"],
                        'ref_id'=> $result['ref_id'],                            
                        'date'      => Carbon::parse($result['entry_date'])->format('Y-m-d'),
                        'account'       => $result['acc_name'],   
                        'detail'       => $result['description'].$sales_rep_name,
                        'increase'       => $increase==0?'':$increase,
                        'decrease'       =>  $decrease==0?'':$decrease ,
                        'balance'       => $balance
                    );
                }
                   $i = $i + 1;
            }
        } elseif($request->type=='Expense'){
            $request->validate([
                'expense' => 'required',
                'date_range' => 'required'
            ],[
                'expense.required' => 'The Expense Field is required',
                'date_range.required' => 'The From Date Field is required'
            ]);
            $date = explode(' - ', $request->date_range);
            $fromDate = Carbon::parse($date[0])->format('Y-m-d');
            $toDate = Carbon::parse($date[1])->format('Y-m-d');
            $accounts_array = array();
            $i=0;
            $add_sale = 0;
            $sale_return = false;
            $acc_id = DB::table('account_chart')->where('acc_id', $request->expense)->first();
            $pre_row = DB::table('account_journal')
                        ->select('account_chart.acc_name', DB::raw('SUM(account_journal.journal_amount) as pre_total'))
                        ->join('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                        ->where('account_journal.acc_id', '=', $request->expense)
                        ->where('account_journal.entry_date', '<', $fromDate)
                        ->groupBy('account_chart.acc_name', 'account_journal.acc_id')
                        ->first();
            if($pre_row!=NULL and $pre_row->pre_total!=NULL and $pre_row->pre_total!=0 ){
                $accounts_array['register'][] = array(
                        'id' => '',                                                        
                        'payee'=> $pre_row->acc_name,
                        'acc_id' => '',
                        'number'=> "",                            
                        'ref_id'=> "",                            
                        'date'      => $date[0],
                        'account'       => $pre_row->acc_name.' Account',         
                        'detail'       =>  'Previous Balance',
                        'increase'       => "",
                        'decrease'       => "",
                        'balance'       => $pre_row->pre_total
                );
                $balance = $pre_row->pre_total;
            }
            $register_accounts = array();
            $expenseRecs = DB::table('account_journal')
                        ->select('account_journal.*', 'account_chart.acc_name')
                        ->join('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                        ->where('account_journal.acc_id', '=', $request->expense)
                        ->whereRaw("account_journal.entry_date >= '". $fromDate ."' AND account_journal.entry_date < '". $toDate ."' + INTERVAL 1 DAY")
                        ->orderBy('account_journal.entry_date', 'ASC')
                        ->orderBy('account_journal.journal_id', 'ASC')
                        ->get();
            foreach ($expenseRecs as $expenseRec) {
                $expenseRef = DB::table('account_journal')
                        ->select('account_journal.*', 'account_chart.acc_name')
                        ->join('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                        ->where('account_journal.acc_id', '!=', $expenseRec->acc_id)
                        ->where('account_journal.ref_id', '=', $expenseRec->ref_id)
                        ->first();
                if($expenseRef!=NULL){
                    $register_accounts[] = array(
                        'journal_id'             => $expenseRef->journal_id,
                        'acc_name'             => $expenseRec->acc_name,
                        'ref_id'                  => $expenseRec->ref_id,
                        'details'            => $expenseRec->journal_details,
                        'entry_date'                  => $expenseRec->entry_date,                        
                        'journal_amount'                => $expenseRec->journal_amount
                    );
                }
            }
            foreach ($register_accounts as $result) {  
                $amount = -1 * $result['journal_amount'];
                $increase = ($amount < 0) ? -1 * $amount : 0;
                $decrease = $amount > 0 ? $amount : 0;
                $balance = $balance + $increase + -1 * ($decrease);   
                $accounts_array['register'][] = array(
                    'id' => $result['journal_id'],                                                        
                    'payee'=> $result['acc_name'],
                    'acc_id' => '',
                    'number'=> '',                            
                    'ref_id'=> $result['ref_id'],                            
                    'date'      => Carbon::parse($result['entry_date'])->format('Y-m-d'),
                    'detail'       =>  $result['details'],
                    'account'       => $result['acc_name']. ' Account',  
                    'increase'       => $increase==0?'':$increase,
                    'decrease'       =>  $decrease==0?'':$decrease ,
                    'balance'       => $balance
                );
            }
        } elseif($request->type=='Bank'){
            $request->validate([
                'bank' => 'required',
                'date_range' => 'required'
            ],[
                'bank.required' => 'The Bank Field is required',
                'date_range.required' => 'The Date Field is required'
            ]);
            $date = explode(' - ', $request->date_range);
            $fromDate = Carbon::parse($date[0])->format('Y-m-d');
            $toDate = Carbon::parse($date[1])->format('Y-m-d');
            $accounts_array = array();
            $i=0;
            $add_sale = 0;
            $sale_return = false;
            $acc_id = DB::table('account_chart')->where('acc_id', $request->bank)->first();
            $pre_row = DB::table('account_journal')
                    ->select('account_chart.acc_name', DB::raw('SUM(account_journal.journal_amount) as pre_total'))
                    ->join('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                    ->where('account_journal.acc_id', '=', $request->bank)
                    ->where('account_journal.entry_date', '<', $fromDate)
                    ->groupBy('account_chart.acc_name', 'account_journal.acc_id')
                    ->first();
            if($pre_row!=NULL and $pre_row->pre_total!=NULL and $pre_row->pre_total!=0 ){
                $accounts_array['register'][] = array(
                        'id' => '',                                                        
                        'payee'=> $pre_row->acc_name,
                        'acc_id' => '',
                        'number'=> "",                            
                        'ref_id'=> "",                            
                        'date'      => $date[0],
                        'account'       => $pre_row->acc_name.' Account',         
                        'detail'       =>  'Previous Balance',
                        'increase'       => "",
                        'decrease'       => "",
                        'balance'       => $pre_row->pre_total
                );
                $balance = $pre_row->pre_total;
            }
            $register_accounts = array();
            $bankRecs = DB::table('account_journal')
                        ->select('account_journal.*', 'account_chart.acc_name')
                        ->join('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                        ->where('account_journal.acc_id', '=', $request->bank)
                        ->whereRaw("account_journal.entry_date >= '". $fromDate ."' AND account_journal.entry_date < '". $toDate ."' + INTERVAL 1 DAY")
                        ->orderBy('account_journal.journal_id', 'ASC')
                        ->get();
            foreach ($bankRecs as $bankRec) {
                $bankRef = DB::table('account_journal')
                        ->select('account_journal.*', 'account_chart.acc_name')
                        ->join('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                        ->where('account_journal.acc_id', '!=', $bankRec->acc_id)
                        ->where('account_journal.ref_id', '=', $bankRec->ref_id)
                        ->first();
                if($bankRef!=NULL){
                    $register_accounts[] = array(
                        'journal_id'             => $bankRef->journal_id,
                        'acc_name'             => $bankRec->acc_name,
                        'o_acc_name'             => $bankRef->acc_name,
                        'ref_id'                  => $bankRec->ref_id,
                        'num'                  => $bankRec->inv_id*-1,
                        'description'            => $bankRef->journal_details,
                        'acc_id'             => $bankRef->acc_id,
                        'details'            => $bankRec->journal_details,
                        'journal_type'             => $bankRec->type,
                        'entry_date'                  => $bankRec->entry_date,                        
                        'journal_amount'                => $bankRec->journal_amount
                    );
                }
            }
            foreach ($register_accounts as $result) {  
                $amount = -1 * $result['journal_amount'];
                $increase = ($amount < 0) ? -1 * $amount : 0;
                $decrease = $amount > 0 ? $amount : 0;
                $balance = $balance + $increase + -1 * ($decrease);   
                if ($result["num"] > 0 && $i + 1 < count($register_accounts) && $register_accounts[$i + 1]["num"] == $result["num"] && (($register_accounts[$i + 1]["acc_id"] == $result["acc_id"] && ($register_accounts[$i + 1]["acc_id"] == "5" || $register_accounts[$i + 1]["acc_id"] == "11")) || $register_accounts[$i + 1]["journal_type"] == "DIS")) {
                    if ($register_accounts[$i]["journal_type"] == "DIS") {
                        $add_sale = $add_sale - $increase;
                    } else {
                        if ($increase > 0) {
                            $add_sale = $add_sale + $increase;
                        } else {
                            $add_sale = $add_sale + $decrease;
                            $sale_return = true;
                        }
                    }
                } else { 
                    if($add_sale>0){                               
                        if($sale_return==false){                                                   
                            $increase = $increase +$add_sale;
                            $add_sale = 0; 
                            if($result["journal_type"]=="DIS"){                                                                                    
                                $increase = $increase - $decrease;
                                $decrease = 0;
                            }
                        } else {
                            $sale_return = false;
                            $decrease = $decrease +$add_sale;
                            $add_sale = 0; 
                            if($result["journal_type"]=="DIS"){                                                                                    
                                $decrease = $decrease - $increase;
                                $increase = 0;
                            }
                        }
                    }
                    $description = $result['details'];
                    $acc_name = $result['o_acc_name'];
                    $msg = '';
                    $invNo = '';
                    $sales_rep_name = '';
                    if($request->bank==-1){
                        if($result['num']){
                            $desc = DB::table('pos_invoice')->select('invoice_no', 'invoice_type', 'custom')->where('invoice_id', $result['num'])->first();
                            if($desc){
                                if($desc->custom!= NULL){
                                    $msg= "Sale Invoice total";
                                } else {
                                    $msg= $desc->custom;
                                }
                                $invoice_no_prefix = "";
                                if($desc->invoice_type!= NULL){
                                    if($desc->invoice_type=='1'){
                                        $invoice_no_prefix = "POS-";
                                    } elseif($desc->invoice_type=='2'){
                                        $invoice_no_prefix = "SALE-";
                                    } elseif($desc->invoice_type=='3'){
                                        $invoice_no_prefix = "POS-RET-";
                                    } elseif($desc->invoice_type=='4'){
                                        $invoice_no_prefix = "SALE-RET-";
                                    }
                                }
                                $invNo = $invoice_no_prefix.$desc->invoice_no;
                            }
                            $description = $result['num']?$invNo . " - " . $msg:$result['description'];
                            if ($result["journal_type"] == "DIS") {
                                $acc_name = "Sales";
                            }
                        } else if($result['journal_type']=='CUST_PAYME'){
                            if($description==""){
                                $description = "RECEIVED FROM CUSTOMER";
                            } else {
                                $description = "RECEIVED FROM CUSTOMER ( ". $description ." )";
                            }
                        } else if($result['journal_type']=='VENDOR_PAY'){
                            if($description==""){
                                $description = "PAID TO VENDOR";
                            } else {
                                $description = "PAID TO VENDOR ( ". $description ." )";
                            }
                        } else if($result['num'] ==0 && $result['journal_type']=='S'){
                            if($description==""){
                                $description = "CUSTOMER CHARGED";
                            } else {
                                $description = "CUSTOMER CHARGED ( ". $description ." )";
                            }
                        } else if($result['num'] ==0 && $result['journal_type']=='P'){
                            if($description==""){
                                $description = "VENDOR CHARGED";
                            } else {
                                $description = "VENDOR CHARGED ( ". $description ." )";
                            }
                        }
                    }
                    $accounts_array['register'][] = array(
                        'id' => $result['journal_id'],                                                        
                        'payee'=> $result['acc_name'],
                        'number'=> '',                            
                        'ref_id'=> $result['ref_id'],                            
                        'date'      => Carbon::parse($result['entry_date'])->format('Y-m-d'),
                        'detail'       =>  $description,
                        'account'       => $acc_name,  
                        'increase'       => $increase==0?'':$increase,
                        'decrease'       =>  $decrease==0?'':$decrease ,
                        'balance'       => $balance
                    );
                } 
                    $i = $i + 1;
            }
        } elseif($request->type=='Loan'){
            $request->validate([
                'loan' => 'required',
                'date_range' => 'required'
            ],[
                'loan.required' => 'The Loan Field is required',
                'date_range.required' => 'The From Date Field is required'
            ]);
            $date = explode(' - ', $request->date_range);
            $fromDate = Carbon::parse($date[0])->format('Y-m-d');
            $toDate = Carbon::parse($date[1])->format('Y-m-d');
            $accounts_array = array();
            $i=0;
            $add_sale = 0;
            $sale_return = false;
            $balance = 0;
            $acc_id = DB::table('account_chart')->where('acc_id', $request->loan)->first();
            $pre_row = DB::table('account_journal')
                        ->select('account_chart.acc_name', DB::raw('SUM(account_journal.journal_amount) as pre_total'))
                        ->join('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                        ->where('account_journal.acc_id', '=', $acc_id->acc_id)
                        ->where('account_journal.entry_date', '<', $fromDate)
                        ->groupBy('account_chart.acc_name', 'account_journal.acc_id')
                        ->first();
            if($pre_row!=NULL and $pre_row->pre_total!=NULL and $pre_row->pre_total!=0 ){
                $accounts_array['register'][] = array( 
                        'ref_id' => '',                          
                        'date'      => $date[0],
                        'account'       => '',         
                        'detail'       =>  'Previous Balance',
                        'increase'       => "",
                        'decrease'       => "",
                        'balance'       => $pre_row->pre_total
                );
                $balance = $pre_row->pre_total;
            }
            $loanRecs = DB::table('account_journal')
                            ->select('account_journal.*', 'account_chart.acc_name')
                            ->join('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                            ->where('account_journal.acc_id', '=', $acc_id->acc_id)
                            ->whereRaw("account_journal.entry_date >= '". $fromDate ."' AND account_journal.entry_date < '". $toDate ."' + INTERVAL 1 DAY")
                            ->orderBy('account_journal.entry_date', 'ASC')
                            ->orderBy('account_journal.type', 'DESC')
                            ->get();
                        
            $register_accounts = array();
            foreach ($loanRecs as $loanRec) {
                $loanRef = DB::table('account_journal')
                        ->select('account_journal.*', 'account_chart.acc_name')
                        ->join('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                        ->where('account_journal.acc_id', '!=', $loanRec->acc_id)
                        ->where('account_journal.ref_id', '=', $loanRec->ref_id)
                        ->first();

                $register_accounts[] = array(
                    'journal_id'             => $loanRef->journal_id,
                    'acc_name'             => $loanRef->acc_name,
                    'acc_id'             => $loanRef->acc_id,
                    'description'            => $loanRef->journal_details,
                    'ref_id'                  => $loanRec->ref_id,
                    'journal_type'             => $loanRec->type,
                    'num'                  => $loanRec->inv_id*-1,
                    'entry_date'                  => $loanRec->entry_date,                        
                    'journal_amount'                => $loanRec->journal_amount
                );
            }
            foreach ($register_accounts as $result) {  
                $amount = -1 * $result['journal_amount'];
                $increase = ($amount < 0) ? -1 * $amount : 0;
                $decrease = $amount > 0 ? $amount : 0;
                $balance = $balance + $increase + -1 * ($decrease);   
                $accounts_array['register'][] = array( 
                    'ref_id' => $result['ref_id'],                            
                    'date'      => Carbon::parse($result['entry_date'])->format('Y-m-d'),
                    'account'       => $result['acc_name']. ' Account',  
                    'detail'        =>  $result['description'],
                    'increase'       => $increase==0?'':$increase,
                    'decrease'       =>  $decrease==0?'':$decrease ,
                    'balance'       => $balance
                );
            }
        }
        return view('showRegisterDetails', compact('accounts_array', 'acc_id', 'type'));
    }

    //Show Register Payment Form
    public function showRegisterPay($acc_id, $type){
        $account = DB::table('account_chart')->where('acc_id', $acc_id)->first();
        if($account!=NULL){
            $Amount = DB::table('account_journal')->where('acc_id', $account->acc_id)->sum('journal_amount');
            if($Amount>0 || $Amount<0 || $Amount==0){
                if($type=='Vendor'){
                    $Balance = $Amount*-1;
                } else {
                    $Balance = $Amount;
                }
            } else {
                $Balance = 0;
            }
            return view('RegisterPay', compact('account', 'Balance', 'type'));
        } else {
            return redirect()->back()->with('error', 'No Register Found - Please try again');
        }
        
        
    }
    //Show Cutomer Payment By Region
    public function showPaymentCollectionRegion(Request $request){
        $groupWhereArray = array();
        $request->validate([
            'payment_collection_date_range' => 'required',
        ],[
            'payment_collection_date_range.required' => 'The Date Field is required',
        ]);
        if($request->pay_collection_cust_group>0){
            $groupWhereArray = ['customer.cust_group_id' => $request->pay_collection_cust_group];
        }
        $date = explode(' - ', $request->payment_collection_date_range);
        $fromDate = Carbon::parse($date[0])->format('Y-m-d');
        $toDate = Carbon::parse($date[1])->format('Y-m-d');
        $payments = DB::table('account_journal')
                    ->select(
                        'account_journal.acc_id', 
                        'account_journal.journal_amount', 
                        'account_journal.entry_date', 
                        'account_journal.item_id AS customer_id', 
                        'customer.cust_name AS customer_name', 
                        'customer_groups.cust_group_name AS customer_group_name',
                        'customer_groups.id AS customer_group_id'
                        )
                    ->leftJoin('customer', 'account_journal.item_id', '=', 'customer.cust_id')
                    ->leftJoin('customer_groups', 'customer.cust_group_id', '=', 'customer_groups.id')
                    ->whereRaw("account_journal.entry_date >= '". $fromDate ."' AND account_journal.entry_date < '". $toDate ."' + INTERVAL 1 DAY")
                    ->where('account_journal.type', '=', 'CUST_PAYME')
                    ->where($groupWhereArray)
                    ->where('account_journal.journal_amount', '>', 0)
                    ->groupBy('account_journal.ref_id')
                    ->orderBy('account_journal.ref_id', 'ASC')
                    ->get();

                    $report = array();
                    $custGroup = $groupName = "";
                    $group_total = $grand_total_amount = 0;
                    $count = 1;
                       
        foreach ($payments as $key => $payment) {
            if($custGroup!=$payment->customer_group_name){
                if($group_total!=0){
                    $report['records'][]     = array( 
                        'group_total'        => 'Total '.$custGroup,     
                        'group_amount'         => number_format($group_total,2,'.',''),                              
                        'is_type'            => 'group_total'
                    );
                    $group_total = 0;
                }
                $custGroup = $payment->customer_group_name;
            }

            if($groupName!=$payment->customer_group_name){
                $groupName = $payment->customer_group_name;
                $report['records'][] = array(
                    'group_name'    => $groupName,                         
                    'is_type'       => 'group_name'
                );
            }
            $acc = DB::table('account_chart')->select('acc_name')->where('acc_id', $payment->acc_id)->first();
            $report['records'][]= array( 
                'count'             => $count,
                'customer_name'     => $payment->customer_name,
                'customer_id'       => $payment->customer_id,
                'amount'    => number_format($payment->journal_amount,2,'.',''),
                'acc_name'            => $acc->acc_name,
                'entry_date'            => $payment->entry_date,
                'is_type'           => 'payments',
            );
            $count++;
            $group_total      = $group_total + $payment->journal_amount;
            $grand_total_amount   = $grand_total_amount + $payment->journal_amount;
        }
        $report['records'][]    = array( 
            'group_total'        => 'Total '.$custGroup,     
            'group_amount'         => number_format($group_total,2,'.',''),                              
            'is_type'            => 'group_total'
        );
        $report['records'][]    = array( 
            'grand_total'       => 'Grand Total',   
            'grand_total_amount'  => number_format($grand_total_amount,2,'.',''),                          
            'is_type'           => 'grand_total'
        );
        //dd($report);
        return view('showPaymentCollectionRegion', compact('report'));
    }
    public function showPaymentCollectionRepresentative(Request $request){
        $groupWhereArray = array();
        $request->validate([
            'payment_collection_salesrep_date_range' => 'required',
        ],[
            'payment_collection_salesrep_date_range.required' => 'The Date Field is required',
        ]);
        if($request->pay_collection_salesrep>0){
            $groupWhereArray = ['customer.cust_group_id' => $request->pay_collection_salesrep];
        }
        $date = explode(' - ', $request->payment_collection_salesrep_date_range);
        $fromDate = Carbon::parse($date[0])->format('Y-m-d');
        $toDate = Carbon::parse($date[1])->format('Y-m-d');
        $payments = DB::table('salesrep')
                    ->select(
                        'account_journal.acc_id', 
                        'account_journal.ref_id', 
                        'account_journal.journal_amount', 
                        'account_journal.entry_date', 
                        'account_journal.item_id AS customer_id', 
                        'customer.cust_name AS customer_name', 
                        'salesrep.salesrep_name AS salesrep_name',
                        'salesrep.id AS salesrep_id'
                        )
                    ->leftJoin('salesrep_detail', 'salesrep_detail.salesrep_id', '=', 'salesrep.id')
                    ->leftJoin('account_journal', 'salesrep_detail.ref_id', '=', 'account_journal.ref_id')
                    ->leftJoin('customer', 'customer.cust_id', '=', 'account_journal.item_id')
                    ->whereRaw("account_journal.entry_date >= '". $fromDate ."' AND account_journal.entry_date < '". $toDate ."' + INTERVAL 1 DAY")
                    ->where('account_journal.type', '=', 'CUST_PAYME')
                    ->where($groupWhereArray)
                    ->where('account_journal.journal_amount', '>', 0)
                    ->groupBy('account_journal.ref_id')
                    ->orderBy('salesrep.id', 'ASC')
                    ->get();

                    $report = array();
                    $represntative="";
                    $rep_total=0;
                    $grand_total_amount=0;
                    $count = 1;
        //dd($payments);               
        foreach ($payments as $key => $payment) {
            if($represntative!=$payment->salesrep_name){
                if($rep_total !=0) {
                    $report['records'][] = array( 
                        'rep_total'        => 'Total '.$represntative,     
                        'rep_amount'         => number_format($rep_total,2,'.',''),                              
                        'is_type'            => 'rep_total'
                    );
                }
                $rep_total  =0;      
                $represntative = $payment->salesrep_name;
                $report['records'][]     = array( 
                    'represntative_name' => $represntative,                         
                    'is_type'            => 'represntative'
                );
            }
            $acc = DB::table('account_chart')->select('acc_name')->where('acc_id', $payment->acc_id)->first();
            $report['records'][]  = array( 
                'count'           => $count,
                'customer_name'   => $payment->customer_name,
                'customer_id'     => $payment->customer_id,
                'amount'          => $payment->journal_amount,
                'acc_name'        => $acc->acc_name,
                'entry_date'      => $payment->entry_date,
                'is_type'         => 'payments'
            );
            $count++;
            $rep_total = $rep_total + $payment->journal_amount;
            $grand_total_amount    = $grand_total_amount + $payment->journal_amount;
        }
        $report['records'][] = array( 
            'rep_total'        => 'Total '.$represntative,     
            'rep_amount'         => number_format($rep_total,2,'.',''),                              
            'is_type'            => 'rep_total'
        );

        $report['records'][]    =  array( 
            'grand_total'       => 'Grand Total',   
            'grand_total_amount'  => number_format($grand_total_amount,2,'.',''),                          
            'is_type'           => 'grand_total'
        ); 
        //dd($report);
        return view('showPaymentCollectionRepresentative', compact('report'));
    }
    
    //Register Payment
    public function RegisterPayment(Request $request){
        if($request->Regtype=="Customer"){
            $request->validate([
                'date_paid' => 'required',
                'type' => 'required',
                'paid_total' => 'required',
                'payment_method' => 'required',
            ],[
                'date_paid.required' => 'The Paid Date Field is required',
                'type.required' => 'The Type Field is required',
                'paid_total.required' => 'The Pay Field is required',
                'payment_method.required' => 'The Payment Method Field is required',
            ]);
            if($request->acc_id>0){  
                if($request->remarks==''){
                    $desc = ' ';
                } else {
                    $desc = $request->remarks;
                }        
                $payment_date = $request->date_paid.' '.Carbon::parse(now())->format('H:i:s');  
                $cust_id = DB::table('customer')->where('cust_acc_id', $request->acc_id)->first();                      
                $id = 0;
                if($request->type=="1"){     
                    $id = DB::table('account_journal')->insertGetId([
                        'ref_id' => 0,
                        'inv_id' => 0,
                        'acc_id' => $request->payment_method,
                        'item_id' => $cust_id->cust_id,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => $request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'CUST_PAYME',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                    DB::table('account_journal')->where('journal_id', $id)->update(['ref_id' => $id]);
                    DB::table('account_journal')->insert([
                        'ref_id' => $id,
                        'inv_id' => 0,
                        'acc_id' => $request->acc_id,
                        'item_id' => $cust_id->cust_id,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => -1*$request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'CUST_PAYME',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                } elseif($request->type=="2"){ 
                    $id = DB::table('account_journal')->insertGetId([
                        'ref_id' => 0,
                        'inv_id' => 0,
                        'acc_id' => $request->acc_id,
                        'item_id' => $cust_id->cust_id,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => $request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'S',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                    DB::table('account_journal')->where('journal_id', $id)->update(['ref_id' => $id]);
                    DB::table('account_journal')->insert([
                        'ref_id' => $id,
                        'inv_id' => 0,
                        'acc_id' => $request->payment_method,
                        'item_id' => $cust_id->cust_id,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => -1*$request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'S',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                } elseif($request->type=="3"){ 
                    $discountAccount = DB::table('account_chart')->where(['acc_name' => 'Discount', 'acc_description' => '{{DISCOUNT_ACCOUNT_SYSTEM}}'])->first();
                    $disc_acc_id = $discountAccount->acc_id;
                    $id = DB::table('account_journal')->insertGetId([
                        'ref_id' => 0,
                        'inv_id' => 0,
                        'acc_id' => $disc_acc_id,
                        'item_id' => $cust_id->cust_id,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => $request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'DIS',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                    DB::table('account_journal')->where('journal_id', $id)->update(['ref_id' => $id]);
                    DB::table('account_journal')->insert([
                        'ref_id' => $id,
                        'inv_id' => 0,
                        'acc_id' => $request->acc_id,
                        'item_id' => $cust_id->cust_id,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => -1*$request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'DIS',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                }
                if($request->refresentative>0){
                    DB::table('salesrep_detail')->insert([
                        'salesrep_id' => $request->refresentative,
                        'ref_id' => $id,
                        'type_id' => 1,
                        'payment_type' => $request->payment_method,
                        'updated_date' => $payment_date
                    ]);
                }
                return redirect('/showRegister/'.$request->Regtype)->with('success', 'Amount '.$request->paid_total.' Successfully Recieved in '. $cust_id->cust_name. ' Register.'  );
            } else {
                return redirect('/showRegister/'.$request->Regtype)->with('error', 'Customer account not found');
            }
        } elseif($request->Regtype=="Vendor"){
            $request->validate([
                'date_paid' => 'required',
                'type' => 'required',
                'paid_total' => 'required',
                'payment_method' => 'required',
            ],[
                'date_paid.required' => 'The Paid Date Field is required',
                'type.required' => 'The Type Field is required',
                'paid_total.required' => 'The Paid Total Field is required',
                'payment_method.required' => 'The Payment Method Field is required',
            ]);
            if($request->remarks==''){
                $desc = ' ';
            } else {
                $desc = $request->remarks;
            }  
            //dd(Carbon::parse(now())->format('H:i:s'));
            if($request->acc_id>0){          
                $payment_date = $request->date_paid.' '.Carbon::parse(now())->format('H:i:s');                        
                $id = 0;
                $vendor_id = DB::table('vendor')->where('vendor_acc_id', $request->acc_id)->first();
                if($request->type=="1"){     
                    $id = DB::table('account_journal')->insertGetId([
                        'ref_id' => 0,
                        'inv_id' => 0,
                        'acc_id' => $request->payment_method,
                        'item_id' => $vendor_id->vendor_id,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => -1*$request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'VENDOR_PAY',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                    DB::table('account_journal')->where('journal_id', $id)->update(['ref_id' => $id]);
                    DB::table('account_journal')->insert([
                        'ref_id' => $id,
                        'inv_id' => 0,
                        'acc_id' => $request->acc_id,
                        'item_id' => $vendor_id->vendor_id,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => $request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'VENDOR_PAY',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                } elseif($request->type=="2"){ 
                    $id = DB::table('account_journal')->insertGetId([
                        'ref_id' => 0,
                        'inv_id' => 0,
                        'acc_id' => $request->payment_method,
                        'item_id' => $vendor_id->vendor_id,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => $request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'P',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                    DB::table('account_journal')->where('journal_id', $id)->update(['ref_id' => $id]);
                    DB::table('account_journal')->insert([
                        'ref_id' => $id,
                        'inv_id' => 0,
                        'acc_id' => $request->acc_id,
                        'item_id' => $vendor_id->vendor_id,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => -1*$request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'P',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                } elseif($request->type=="3"){ 
                    $discountAccount = DB::table('account_chart')->where(['acc_name' => 'Purchase Discount', 'acc_description' => '{{PURCHASE_DISCOUNT_INCOME_ACCOUNT_SYSTEM}}'])->first();
                    $disc_acc_id = $discountAccount->acc_id;
                    $id = DB::table('account_journal')->insertGetId([
                        'ref_id' => 0,
                        'inv_id' => 0,
                        'acc_id' => $disc_acc_id,
                        'item_id' => $vendor_id->vendor_id,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => -1*$request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'P_DIS',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                    DB::table('account_journal')->where('journal_id', $id)->update(['ref_id' => $id]);
                    DB::table('account_journal')->insert([
                        'ref_id' => $id,
                        'inv_id' => 0,
                        'acc_id' => $request->acc_id,
                        'item_id' => $vendor_id->vendor_id,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => $request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'P_DIS',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                }
                if($request->refresentative>0){
                    DB::table('salesrep_detail')->insert([
                        'salesrep_id' => $request->refresentative,
                        'ref_id' => $id,
                        'type_id' => 1,
                        'payment_type' => $request->payment_method,
                        'updated_date' => $payment_date
                    ]);
                }
                return redirect('/showRegister/'.$request->Regtype)->with('success', 'Amount '.$request->paid_total.' Successfully Paid in '. $vendor_id->vendor_name. ' Register.'  );
            } else {
                return redirect('/showRegister/'.$request->Regtype)->with('error', 'Customer account not found');
            }
        } elseif($request->Regtype=="Bank"){ 
            $request->validate([
                'date_paid' => 'required',
                'paid_total' => 'required',
                'payment_method' => 'required',
            ],[
                'date_paid.required' => 'The Deposite Date Field is required',
                'paid_total.required' => 'The Pay Field is required',
                'payment_method.required' => 'The Payment Method Field is required',
            ]);
            if($request->acc_id!=$request->payment_method){
                $acc_id = DB::table('account_chart')->where('acc_id', $request->acc_id)->first();
                if($acc_id!=NULL){  
                    if($request->remarks==''){
                        $desc = ' ';
                    } else {
                        $desc = $request->remarks;
                    }        
                    $payment_date = $request->date_paid.' '.Carbon::parse(now())->format('H:i:s');    
                                        
                    $id = 0;
                    $id = DB::table('account_journal')->insertGetId([
                        'ref_id' => 0,
                        'inv_id' => 0,
                        'acc_id' => $request->payment_method,
                        'item_id' => 0,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => -1*($request->paid_total),
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'BANK',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                    DB::table('account_journal')->where('journal_id', $id)->update(['ref_id' => $id]);
                    DB::table('account_journal')->insert([
                        'ref_id' => $id,
                        'inv_id' => 0,
                        'acc_id' => $request->acc_id,
                        'item_id' => 0,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => $request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'BANK',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                    return redirect('/showRegister/'.$request->Regtype)->with('success', 'Amount '.$request->paid_total.' Successfully Recieved in '. $request->acc_name. ' Register.'  );
                } else {
                    return redirect('/showRegister/'.$request->Regtype)->with('error', 'Bank account not found');
                }
            } else {
                return redirect('/showRegister/'.$request->Regtype)->with('error', 'Can Not Deposite In Same Account');
            }
        } elseif($request->Regtype=="Expense"){ 
            $request->validate([
                'date_paid' => 'required',
                'type' => 'required',
                'paid_total' => 'required',
                'payment_method' => 'required',
            ],[
                'date_paid.required' => 'The Paid Date Field is required',
                'type.required' => 'The Type Field is required',
                'paid_total.required' => 'The Pay Field is required',
                'payment_method.required' => 'The Payment Method Field is required',
            ]);
            
            $acc_id = DB::table('account_chart')->where('acc_id', $request->acc_id)->first();
                if($acc_id!=NULL){  
                    if($request->remarks==''){
                        $desc = ' ';
                    } else {
                        $desc = $request->remarks;
                    }        
                    $payment_date = $request->date_paid.' '.Carbon::parse(now())->format('H:i:s');    
                                        
                    $id = 0;
                    $id = DB::table('account_journal')->insertGetId([
                        'ref_id' => 0,
                        'inv_id' => 0,
                        'acc_id' => $request->payment_method,
                        'item_id' => 0,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => -1*($request->paid_total),
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'EXPENSE',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                    DB::table('account_journal')->where('journal_id', $id)->update(['ref_id' => $id]);
                    DB::table('account_journal')->insert([
                        'ref_id' => $id,
                        'inv_id' => 0,
                        'acc_id' => $request->acc_id,
                        'item_id' => 0,
                        'journal_details' => $desc,
                        'currency_rate' => 1,
                        'journal_amount' => $request->paid_total,
                        'currency_id' => 1,
                        'entry_date' => $payment_date,
                        'type' => 'EXPENSE',
                        'time' => Carbon::parse(now())->format('H:i:s'),
                    ]);
                    return redirect('/showRegister/'.$request->Regtype)->with('success', 'Amount '.$request->paid_total.' Successfully Paid in '. $request->acc_name. ' Register.'  );
                } else {
                    return redirect('/showRegister/'.$request->Regtype)->with('error', 'Expense account not found');
                }
        } elseif($request->Regtype=="Loan"){
            $request->validate([
                'date_paid' => 'required',
                'type' => 'required',
                'paid_total' => 'required',
                'payment_method' => 'required',
            ],[
                'date_paid.required' => 'The Paid Date Field is required',
                'type.required' => 'The Type Field is required',
                'paid_total.required' => 'The Pay Field is required',
                'payment_method.required' => 'The Payment Method Field is required',
            ]);
            
                if($request->remarks==''){
                    $desc = ' ';
                } else {
                    $desc = $request->remarks;
                }        
                $payment_date = $request->date_paid.' '.Carbon::parse(now())->format('H:i:s');                        
                $id = 0;
                $type = 1;
                if($request->acc_id>0){
                    if($request->acc_type_id==14){
                        if($request->type=="1"){     
                            $id = DB::table('account_journal')->insertGetId([
                                'ref_id' => 0,
                                'inv_id' => 0,
                                'acc_id' => $request->payment_method,
                                'item_id' => 0,
                                'journal_details' => $desc,
                                'currency_rate' => 1,
                                'journal_amount' => -1*$request->paid_total,
                                'currency_id' => 1,
                                'entry_date' => $payment_date,
                                'type' => 'R_LOAN',
                                'time' => Carbon::parse(now())->format('H:i:s'),
                            ]);
                            DB::table('account_journal')->where('journal_id', $id)->update(['ref_id' => $id]);
                            DB::table('account_journal')->insert([
                                'ref_id' => $id,
                                'inv_id' => 0,
                                'acc_id' => $request->acc_id,
                                'item_id' => 0,
                                'journal_details' => $desc,
                                'currency_rate' => 1,
                                'journal_amount' => $request->paid_total,
                                'currency_id' => 1,
                                'entry_date' => $payment_date,
                                'type' => 'R_LOAN',
                                'time' => Carbon::parse(now())->format('H:i:s'),
                            ]);
                        } elseif($request->type=="2"){ 
                            $id = DB::table('account_journal')->insertGetId([
                                'ref_id' => 0,
                                'inv_id' => 0,
                                'acc_id' => $request->acc_id,
                                'item_id' => 0,
                                'journal_details' => $desc,
                                'currency_rate' => 1,
                                'journal_amount' => -1*$request->paid_total,
                                'currency_id' => 1,
                                'entry_date' => $payment_date,
                                'type' => 'S',
                                'time' => Carbon::parse(now())->format('H:i:s'),
                            ]);
                            DB::table('account_journal')->where('journal_id', $id)->update(['ref_id' => $id]);
                            DB::table('account_journal')->insert([
                                'ref_id' => $id,
                                'inv_id' => 0,
                                'acc_id' => $request->payment_method,
                                'item_id' => 0,
                                'journal_details' => $desc,
                                'currency_rate' => 1,
                                'journal_amount' => $request->paid_total,
                                'currency_id' => 1,
                                'entry_date' => $payment_date,
                                'type' => 'S',
                                'time' => Carbon::parse(now())->format('H:i:s'),
                            ]);
                        } 
                        $type = 1;
                    } elseif($request->acc_type_id==15){
                        if($request->type=="1"){     
                            $id = DB::table('account_journal')->insertGetId([
                                'ref_id' => 0,
                                'inv_id' => 0,
                                'acc_id' => $request->payment_method,
                                'item_id' => 0,
                                'journal_details' => $desc,
                                'currency_rate' => 1,
                                'journal_amount' => $request->paid_total,
                                'currency_id' => 1,
                                'entry_date' => $payment_date,
                                'type' => 'VENDOR_PAY',
                                'time' => Carbon::parse(now())->format('H:i:s'),
                            ]);
                            DB::table('account_journal')->where('journal_id', $id)->update(['ref_id' => $id]);
                            DB::table('account_journal')->insert([
                                'ref_id' => $id,
                                'inv_id' => 0,
                                'acc_id' => $request->acc_id,
                                'item_id' => 0,
                                'journal_details' => $desc,
                                'currency_rate' => 1,
                                'journal_amount' => -1*$request->paid_total,
                                'currency_id' => 1,
                                'entry_date' => $payment_date,
                                'type' => 'VENDOR_PAY',
                                'time' => Carbon::parse(now())->format('H:i:s'),
                            ]);
                        } elseif($request->type=="2"){ 
                            $id = DB::table('account_journal')->insertGetId([
                                'ref_id' => 0,
                                'inv_id' => 0,
                                'acc_id' => $request->payment_method,
                                'item_id' => 0,
                                'journal_details' => $desc,
                                'currency_rate' => 1,
                                'journal_amount' => -1*$request->paid_total,
                                'currency_id' => 1,
                                'entry_date' => $payment_date,
                                'type' => 'P',
                                'time' => Carbon::parse(now())->format('H:i:s'),
                            ]);
                            DB::table('account_journal')->where('journal_id', $id)->update(['ref_id' => $id]);
                            DB::table('account_journal')->insert([
                                'ref_id' => $id,
                                'inv_id' => 0,
                                'acc_id' => $request->acc_id,
                                'item_id' => 0,
                                'journal_details' => $desc,
                                'currency_rate' => 1,
                                'journal_amount' => $request->paid_total,
                                'currency_id' => 1,
                                'entry_date' => $payment_date,
                                'type' => 'P',
                                'time' => Carbon::parse(now())->format('H:i:s'),
                            ]);
                        } 
                        $type = 2;
                    }

                    if($request->refresentative>0){
                        DB::table('salesrep_detail')->insert([
                            'salesrep_id' => $request->refresentative,
                            'ref_id' => $id,
                            'type_id' => $type,
                            'payment_type' => $request->payment_method,
                            'updated_date' => $payment_date
                        ]);
                    }
                    return redirect('/showRegister/'.$request->Regtype)->with('success', 'Amount '.$request->paid_total.' Successfully Recieved in '. $request->acc_name. ' Register.'  );
                } else {
                    return redirect('/showRegister/'.$request->Regtype)->with('error', 'Loan Account Not Found');
                }
        }
    }

    //Show Account Receivable Report
    public function showAccountReceivable(Request $request){
        $request->validate([
            'acc_rec_cust_group' => 'required'
        ],[
            'acc_rec_cust_group.required' => 'The Customer Group Field is required'
        ]);
        $whereArray = array();
        if($request->acc_rec_cust_group>0){
            $whereArray = ['customer.cust_group_id' => $request->acc_rec_cust_group];
        }

        if($request->acc_rec_customer>0){
            $whereArray = ['customer.cust_id' => $request->acc_rec_customer];
        }
        $account_receivable = array();
        $totalAccountReceivable = $totallastPayment = $customerBalance = $lastPayment = 0; 
        $lastPaymentDate = '';
        $accountReceivables = DB::table('customer')
                            ->select('customer.cust_id', 
                                    'customer.cust_name', 
                                    'customer.cust_mobile', 
                                    'customer.cust_group_id', 
                                    'customer.cust_acc_id',
                                    'customer_groups.cust_group_name',
                                    'account_chart.opening_balance'
                                    )
                            ->join('customer_groups', 'customer.cust_group_id', '=', 'customer_groups.id')
                            ->join('account_chart', 'customer.cust_acc_id', '=', 'account_chart.acc_id')
                            ->where('customer.cust_id', '>', 0)
                            ->where($whereArray)
                            ->orderByRaw("customer.cust_group_id ASC, customer.cust_name ASC")
                            ->get();
        foreach ($accountReceivables as $accountReceivable) {
            $customerAmount = DB::table('account_journal')->where('acc_id', $accountReceivable->cust_acc_id)->sum('journal_amount');
            
            if($customerAmount>0 || $customerAmount<0 || $customerAmount==0){
                $customerBalance = $customerAmount;
            } else {
                $customerBalance = 0;
            }

            $customerLastPayment = DB::table('account_journal')->where(['acc_id' => $accountReceivable->cust_acc_id, 'type' => 'CUST_PAYME'])->select('journal_amount', 'entry_date')->orderByDesc('journal_id')->limit(1)->first();
            if($customerLastPayment){
                if($customerLastPayment->journal_amount<0 || $customerLastPayment->journal_amount>0 || $customerLastPayment->journal_amount==0){
                    $lastPayment = -1*$customerLastPayment->journal_amount;
                    $Date = explode(' ', $customerLastPayment->entry_date);
                    $lastPaymentDate = $Date[0];
                } else {
                    $lastPayment = 0;
                    $lastPaymentDate = '';
                }
            } else {
                $lastPayment = 0;
                $lastPaymentDate = '';
            }
                $account_receivable[] = array(
                    'cust_id'             => $accountReceivable->cust_id,
                    'cust_name'             => $accountReceivable->cust_name,
                    'cust_mobile'             => $accountReceivable->cust_mobile,
                    'amount'            => $customerBalance,
                    'last_amount'            => $lastPayment,
                    'last_amount_date'            => $lastPaymentDate,
                    'cust_group_id'            => $accountReceivable->cust_group_id,
                    'cust_group_name'            => $accountReceivable->cust_group_name,
                );
                if($customerBalance>1){
                    $totalAccountReceivable = $totalAccountReceivable + $customerBalance;
                    $totallastPayment = $totallastPayment + $lastPayment;
                }
                
            
        }
        return view('accountReceivable', compact('account_receivable', 'totalAccountReceivable', 'totallastPayment'));

    }

    //Show Account Payable Report
    public function showAccountPayable(Request $request){
        $whereArray = array();
        if($request->acc_pay_vendor>0){
            $whereArray = ['vendor.vendor_id' => $request->acc_pay_vendor];
        }
        $account_payable = array();
        $totalAccountPayable = $vendorBalance = 0; 

        $accountPayables = DB::table('vendor')
                            ->select('vendor.vendor_id', 
                                    'vendor.vendor_name',  
                                    'vendor.vendor_acc_id',
                                    'account_chart.opening_balance'
                                    )
                            ->join('account_chart', 'vendor.vendor_acc_id', '=', 'account_chart.acc_id')
                            ->where('vendor.vendor_id', '>', 0)
                            ->where($whereArray)
                            ->orderBy("vendor.vendor_name", "ASC")
                            ->get();
        foreach ($accountPayables as $accountPayable) {
            $vendorAmount = DB::table('account_journal')->where('acc_id', $accountPayable->vendor_acc_id)->sum('journal_amount');
            $amount = -1*($vendorAmount);
            if($amount>1 || $amount < -1){
                $account_payable[] = array(
                    'vendor_id'             => $accountPayable->vendor_id,
                    'vendor_name'             => $accountPayable->vendor_name,
                    'amount'            => $amount,
                );
                
                $totalAccountPayable = $totalAccountPayable + $amount;
            }
                
            
        }
        return view('accountPayable', compact('account_payable', 'totalAccountPayable'));
    }

    //Show Balance Sheet
    public function balanceSheet(){
        
        $report = array();
        $assets = DB::table('account_journal')
                    ->select('account_chart.acc_name AS account_name', 'account_chart.acc_type_id AS type_id', 'account_chart.acc_id', DB::raw('SUM(account_journal.journal_amount) as amount'))
                    ->leftJoin('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                    ->leftJoin('account_type', 'account_type.acc_type_id', '=', 'account_chart.acc_type_id')
                    ->leftJoin('account_heads', 'account_heads.acc_head_id', '=', 'account_type.head_id')
                    ->where('account_heads.acc_head_id', '=', '1')
                    ->groupBy('account_chart.acc_id', 'account_chart.acc_name', 'account_chart.acc_type_id')
                    ->get();
        $total_assets = $total_cur_assets = $report_line_salereturn = $receiveable_loans = 0;
        $report['records'][] = array( 
            'title'       =>'ASSETS',                         
            'is_type'     => 'head_asset'
        );
        if(count($assets)>0){
            $report['records'][] = array( 
                'title'       =>'Current Assets',                         
                'is_type'     => 'current_asset'
            );                  
            $report['records'][] = array( 
                'title'       =>'Other Current Assets',                         
                'is_type'     => 'other_asset'
            );
            $acc_rec = 0;
            foreach ($assets as $result) {
                if($result->type_id!=1){
                    if($result->type_id==14){
                        $receiveable_loans = $receiveable_loans + $result->amount;
                    }
                    else{
                        $report['records'][] = array( 
                            'title'       =>$result->account_name,                         
                            'amount'      =>number_format($result->amount,2,'.',','),                                                       
                            'is_type'     => 'asset'                            
                        );  
                    }
                } else {
                  $acc_rec = $acc_rec + $result->amount;
                } 
                $total_cur_assets = $total_cur_assets + $result->amount;
                $total_assets = $total_assets + $result->amount;
            }

            if($acc_rec!=0){
                $report['records'][] = array( 
                    'title'       =>'Account Receivable',                         
                    'amount'      =>number_format($acc_rec,2,'.',','),                                                     
                    'is_type'     => 'asset'                            
                ); 
            }

            if($receiveable_loans!=0){
                $report['records'][] = array( 
                    'title'       =>'Receivable Loans',                         
                    'amount'      =>number_format($receiveable_loans,2,'.',','),                                                     
                    'is_type'     => 'asset'                            
                ); 
            }

            $report['records'][] = array( 
                'title'       =>'Total Current Assets',                         
                'amount'     =>number_format($total_cur_assets-$report_line_salereturn,2,'.',','),                         
                'is_type'       => 'total_cur_asset'
            );
        }

        $report['records'][] = array( 
            'title'       =>'TOTAL ASSETS',    
            'amount'     =>number_format($total_assets-$report_line_salereturn,2,'.',','),   
            'is_type'       => 'total_asset'
        );

        $report['records'][] = array( 
            'title'       =>'LIABILITIES & EQUITY',                         
            'is_type'       => 'head_leq'
        ); 

        $liabilities = DB::table('account_journal')
                        ->select('account_chart.acc_name AS account_name', 'account_chart.acc_type_id AS type_id', 'account_chart.acc_id', DB::raw('SUM(account_journal.journal_amount) as amount'))
                        ->leftJoin('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                        ->leftJoin('account_type', 'account_type.acc_type_id', '=', 'account_chart.acc_type_id')
                        ->leftJoin('account_heads', 'account_heads.acc_head_id', '=', 'account_type.head_id')
                        ->where('account_heads.acc_head_id', '=', '2')
                        ->groupBy('account_chart.acc_id', 'account_chart.acc_name', 'account_chart.acc_type_id')
                        ->get();
        $total_liabilities = $total_cur_liabilities =  $payable_loans = 0;
        if(count($liabilities)>0){
            $report['records'][] = array( 
                'title'       =>'Liabilities',                         
                'is_type'       => 'head_l'
            ); 
            $report['records'][] = array( 
                'title'       =>'Current Liabilities',                         
                'is_type'       => 'current_l'
            );
            $acc_payable = 0;
            foreach ($liabilities as $liability) {
                if($liability->type_id!=2){
                    if($liability->type_id==15){
                        $payable_loans = $payable_loans + $liability->amount;
                    } else {  
                        $report['records'][] = array( 
                            'title'       =>$liability->account_name,                         
                            'amount'       =>number_format($liability->amount,2,'.',','),                              
                            'is_type'       => 'liability'
                        );  
                    }
                } else {
                    $acc_payable = $acc_payable + $liability->amount; 
                }
                $total_cur_liabilities = $total_cur_liabilities + $liability->amount;
                $total_liabilities = $total_liabilities + $liability->amount;
            }
            if($acc_payable!=0){ 
                $report['records'][] = array( 
                    'title'       =>'Account Payable',                         
                    'amount'       =>number_format(-1*$acc_payable,2,'.',','),                                                     
                    'is_type'       => 'liability'                            
                );  
            }
            if($payable_loans!=0){
                $report['records'][] = array( 
                    'title'       =>'Payable Loans',                         
                    'amount'       =>number_format($payable_loans,2,'.',','),                                                     
                    'is_type'       => 'liability'                            
                );  
            }
            $report['records'][] = array( 
                'title'       =>'Total Current Liabilities',            
                'amount'     =>number_format($total_cur_liabilities,2,'.',','),  
                'is_type'       => 'total_cur_l'
            ); 
            $report['records'][] = array( 
                'title'       =>'TOTAL LIABILITIES',                         
                'amount'     =>number_format($total_liabilities,2,'.',','),  
                'is_type'       => 'total_l'
            ); 
        }

        $equities = DB::table('account_journal')
                        ->select('account_chart.acc_name AS account_name', DB::raw('SUM(account_journal.journal_amount) as amount'))
                        ->leftJoin('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                        ->leftJoin('account_type', 'account_type.acc_type_id', '=', 'account_chart.acc_type_id')
                        ->leftJoin('account_heads', 'account_heads.acc_head_id', '=', 'account_type.head_id')
                        ->where('account_heads.acc_head_id', '=', '3')
                        ->groupBy('account_chart.acc_id', 'account_chart.acc_name', 'account_chart.acc_type_id')
                        ->get();
        $total_equities = 0;
        if(count($equities)>0){
            $report['records'][] = array( 
                'title'       =>'EQUITY',                         
                'is_type'     => 'head_equity'
            );
            foreach ($equities as $equity) {
                $report['records'][] = array( 
                    'title'       =>$equity->account_name,                         
                    'amount'      =>number_format($equity->amount*-1,2,'.',','),                           
                    'is_type'     => 'equities'
                );                          
                $total_equities = $total_equities + $equity->amount;
            }
                     
        }

        $income = DB::table('account_journal')
                    ->leftJoin('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                    ->leftJoin('account_type', 'account_type.acc_type_id', '=', 'account_chart.acc_type_id')
                    ->where('account_type.acc_type_id', '=', '4')
                    ->sum('account_journal.journal_amount');

        $expense = DB::table('account_journal')
                    ->leftJoin('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                    ->leftJoin('account_type', 'account_type.acc_type_id', '=', 'account_chart.acc_type_id')
                    ->where('account_type.acc_type_id', '=', '5')
                    ->sum('account_journal.journal_amount');
        
        
        $cogs = DB::table('account_journal')
                    ->select('account_chart.acc_name AS account_name', DB::raw('SUM(account_journal.journal_amount) as amount'))
                    ->leftJoin('account_chart', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                    ->leftJoin('account_type', 'account_type.acc_type_id', '=', 'account_chart.acc_type_id')
                    ->where('account_type.acc_type_id', '=', '7')
                    ->sum('account_journal.journal_amount');
        $incomeAMount = $income == NULL ? 0 : $income;
        $expenseAmount = $expense == NULL ? 0 : $expense;
        $cogsAmount = $cogs == NULL ? 0 : $cogs;
        
        $net_income =  $incomeAMount + $expenseAmount + $cogsAmount;

        if($net_income!=0){
            $report['records'][] = array( 
                'title'       =>'Net Income',                         
                'amount'      =>number_format($net_income*-1,2,'.',','),                           
                'is_type'     => 'equities'
            );                         
            $total_equities = $total_equities + $net_income;
        }

        if($total_equities!=0){
            $report['records'][] = array( 
                'title'       =>'Total Equity',                         
                'amount'      =>number_format($total_equities*-1,2,'.',','),   
                'is_type'     => 'total_equity'
            ); 
        }

        $total_liabilities_equites = $total_equities + $total_liabilities;
        $report['records'][] = array( 
            'title'       =>'TOTAL LIABILITIES & EQUITY',             
            'amount'      =>number_format($total_liabilities_equites*-1,2,'.',','),     
            'is_type'     => 'total_le'
        );
        //dd($report);

        return view('balanceSheet', compact('report'));
                    
    }

    //Get Customer By Regions
    public function getCustomersByGroup($group_id){
        $data = DB::table('customer')->where('cust_id', '>', 1)->where('cust_group_id',$group_id)->orderBy('cust_name', 'ASC')->get();
        return response()->json(['data' => $data]);
    }

    
}
