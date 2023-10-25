<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountJournal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class HomeController extends Controller
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
    public function index()
    {
        $currentMonthsale = DB::table('account_journal')
                    ->whereMonth('entry_date', date('m'))
                    ->whereYear('entry_date', date('Y'))
                    ->where('inv_id', '<', 0)
                    ->where('item_id', '>', 0)
                    ->where('type', '=', 'S')
                    ->sum('journal_amount');
        $currentMonthsaleReturn = DB::table('account_journal')
                    ->whereMonth('entry_date', date('m'))
                    ->whereYear('entry_date', date('Y'))
                    ->where('inv_id', '<', 0)
                    ->where('item_id', '>', 0)
                    ->where('type', '=', 'SALE_RET')
                    ->sum('journal_amount');
        $currentMonthsaleCost = DB::table('account_journal')
                    ->whereMonth('entry_date', date('m'))
                    ->whereYear('entry_date', date('Y'))
                    ->where('inv_id', '<', 0)
                    ->where('acc_id', '=', 2)
                    ->where('type', '=', 'S')
                    ->sum('journal_amount');
        $currentMonthsaleReturnCost = DB::table('account_journal')
                    ->whereMonth('entry_date', date('m'))
                    ->whereYear('entry_date', date('Y'))
                    ->where('inv_id', '<', 0)
                    ->where('acc_id', '=', 2)
                    ->where('type', '=', 'SALE_RET')
                    ->sum('journal_amount');
                    //DB::enableQueryLog();
        $currentMonthexpences = DB::table('account_chart')
                    ->whereMonth('account_journal.entry_date', date('m'))
                    ->whereYear('account_journal.entry_date', date('Y'))
                    ->where('account_chart.acc_type_id', 5)
                    ->where('account_journal.inv_id', 0)
                    ->join('account_journal', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                    ->sum('journal_amount');
                    //dd(DB::getQueryLog());
        $currentMonthcustPay = DB::table('account_journal')
                    ->whereMonth('entry_date', date('m'))
                    ->whereYear('entry_date', date('Y'))
                    ->where('acc_id', '=', -1)
                    ->where('type', '=', 'CUST_PAYME')
                    ->sum('journal_amount');
        $currentMonthDiscount = DB::table('account_journal')
                    ->whereMonth('entry_date', date('m'))
                    ->whereYear('entry_date', date('Y'))
                    ->where('inv_id', '<', 0)
                    ->where('acc_id', '=', 10)
                    ->where('type', '=', 'DIS')
                    ->sum('journal_amount');
        $currentYearsales = DB::table('account_journal')
                    ->select(DB::raw("(sum(journal_amount)) journal_amount"), DB::raw('YEAR(entry_date) year'), DB::raw('MONTHNAME(entry_date) monthname'), DB::raw('MONTH(entry_date) month'))
                    ->whereYear('entry_date', date('Y'))
                    ->where('inv_id', '<', 0)
                    ->where('item_id', '>', 0)
                    ->where('type', '=', 'S')
                    ->groupBy('year', 'month', 'monthname')
                    ->orderBY('month', 'ASC')
                    ->get();
        $currentYeardiscount = DB::table('account_journal')
                    ->select(DB::raw("(sum(journal_amount)) journal_amount"), DB::raw('YEAR(entry_date) year'), DB::raw('MONTHNAME(entry_date) monthname'), DB::raw('MONTH(entry_date) month'))
                    ->whereYear('entry_date', date('Y'))
                    ->where('inv_id', '<', 0)
                    ->where('acc_id', '=', 10)
                    ->where('type', '=', 'DIS')
                    ->groupBy('year', 'month', 'monthname')
                    ->orderBY('month', 'ASC')
                    ->get();
                    //dd($currentYeardiscount);
        $currentYearrecoveries = DB::table('account_journal')
                    ->select(DB::raw("(sum(journal_amount)) journal_amount"), DB::raw('YEAR(entry_date) year'), DB::raw('MONTHNAME(entry_date) monthname'), DB::raw('MONTH(entry_date) month'))
                    ->whereYear('entry_date', date('Y'))
                    ->where('acc_id', '=', -1)
                    ->where('type', '=', 'CUST_PAYME')
                    ->groupBy('year', 'month', 'monthname')
                    ->orderBY('month', 'ASC')
                    ->get();
        
        return view('home', compact('currentMonthsale', 'currentMonthsaleReturn', 'currentMonthsaleCost', 'currentMonthsaleReturnCost', 'currentMonthexpences', 'currentMonthcustPay', 'currentMonthDiscount', 'currentYearsales', 'currentYearrecoveries', 'currentYeardiscount'));
    }

    public function getDashboarData(Request $request) {
        $todaysale = DB::table('account_journal')
                    ->whereRaw("entry_date >= '". $request->startDate ."' AND entry_date < '". $request->endDate ."' + INTERVAL 1 DAY")
                    ->where('inv_id', '<', 0)
                    ->where('item_id', '>', 0)
                    ->where('type', '=', 'S')
                    ->sum('journal_amount');
        $todaysaleReturn = DB::table('account_journal')
                    ->whereRaw("entry_date >= '". $request->startDate ."' AND entry_date < '". $request->endDate ."' + INTERVAL 1 DAY")
                    ->where('inv_id', '<', 0)
                    ->where('item_id', '>', 0)
                    ->where('type', '=', 'SALE_RET')
                    ->sum('journal_amount');
        $todaysaleCost = DB::table('account_journal')
                    ->whereRaw("entry_date >= '". $request->startDate ."' AND entry_date < '". $request->endDate ."' + INTERVAL 1 DAY")
                    ->where('inv_id', '<', 0)
                    ->where('acc_id', '=', 2)
                    ->where('type', '=', 'S')
                    ->sum('journal_amount');
        $todaysaleReturnCost = DB::table('account_journal')
                    ->whereRaw("entry_date >= '". $request->startDate ."' AND entry_date < '". $request->endDate ."' + INTERVAL 1 DAY")
                    ->where('inv_id', '<', 0)
                    ->where('acc_id', '=', 2)
                    ->where('type', '=', 'SALE_RET')
                    ->sum('journal_amount');
        $expences = DB::table('account_chart')
                    ->whereRaw("entry_date >= '". $request->startDate ."' AND entry_date < '". $request->endDate ."' + INTERVAL 1 DAY")
                    ->where('account_chart.acc_type_id', 5)
                    ->join('account_journal', 'account_chart.acc_id', '=', 'account_journal.acc_id')
                    ->sum('journal_amount');
        $discount = DB::table('account_journal')
                    ->whereRaw("entry_date >= '". $request->startDate ."' AND entry_date < '". $request->endDate ."' + INTERVAL 1 DAY")
                    ->where('inv_id', '<', 0)
                    ->where('acc_id', '=', 10)
                    ->where('type', '=', 'DIS')
                    ->sum('journal_amount');
        $custPay = DB::table('account_journal')
                    ->whereRaw("entry_date >= '". $request->startDate ."' AND entry_date < '". $request->endDate ."' + INTERVAL 1 DAY")
                    ->where('acc_id', '=', -1)
                    ->where('type', '=', 'CUST_PAYME')
                    ->sum('journal_amount');
        $data = [
            'todaysale' => $todaysale,
            'todaysaleReturn' => $todaysaleReturn,
            'todaysaleCost' => $todaysaleCost,
            'todaysaleReturnCost' => $todaysaleReturnCost,
            'expences' => $expences,
            'custPay' => $custPay,
            'discount'=>$discount
        ];
        return response()->json(['data' => $data]);
    }
}
