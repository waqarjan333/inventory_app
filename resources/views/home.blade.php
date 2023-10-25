@extends('index')
@section('title', 'Aursoft')
@section('contents')
@inject('carbon', 'Carbon\Carbon')
@php $monthlysalevalue = $monthNames = $monthlyrecoveryvalue = $R_monthNames = ''; @endphp
@foreach ($currentYearsales as $key => $currentYearsale)
    @php 
    $monthlysalevalue .= $currentYearsale->journal_amount-($currentYeardiscount[$key]->journal_amount ?? 0).',';

    $monthNames .= '"'.$currentYearsale->monthname.'",'; 
    @endphp
@endforeach
@foreach ($currentYearrecoveries as $currentYearrecovery)
    @php 
    $monthlyrecoveryvalue .= $currentYearrecovery->journal_amount.','; 
    $R_monthNames .= '"'.$currentYearrecovery->monthname.'",'; 
    @endphp
@endforeach
<style type="text/css">
.highcharts-figure, .highcharts-data-table table {
    min-width: 320px;
    max-width: 800px;
    margin: 1em auto;
}
.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
}
.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}
.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}

.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
}

.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}

.highcharts-data-table tr:hover {
    background: #f1f7ff;
}
</style>
    <!-- App Capsule -->
    <div id="appCapsule">
        <!-- Wallet Card -->
        <div class="section wallet-card-section pt-1">
            @if (\Session::has('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-1 text-center" role="alert">
                    {!! \Session::get('error') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (\Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show mb-1 text-center" role="alert">
                    {!! \Session::get('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="wallet-card">
                <!-- Balance -->
                <div class="balance">
                    <div class="left">
                        <span class="title">Total Cash</span>
                        <h1 class="total">PKR {{ DB::table('account_journal')->where(['acc_id' => -1])->sum('journal_amount'); }}</h1>
                    </div>
                    <div class="right">
                        <a href="{{ route('new.Sale.Invoice') }}" class="button">
                            <ion-icon name="add-outline"></ion-icon>
                        </a>
                    </div>
                </div>
                <!-- * Balance -->
                <!-- Wallet Footer -->
                <div class="wallet-footer">
                    <div class="item">
                        <a href="{{ route('show.Register', ['type' => 'Customer']) }}" class="action-button">
                            <div class="icon-wrapper bg-danger">
                                <ion-icon name="person-outline"></ion-icon>
                            </div>
                            <strong>Customers</strong>
                        </a>
                    </div>
                    <div class="item">
                        <a href="{{ route('show.Register', ['type' => 'Vendor']) }}" class="action-button">
                            <div class="icon-wrapper">
                                <ion-icon name="person-outline"></ion-icon>
                            </div>
                            <strong>Vendors</strong>
                        </a>
                    </div>
                    <div class="item">
                        <a href="{{ route('show.Register', ['type' => 'Bank']) }}" class="action-button">
                            <div class="icon-wrapper bg-success">
                                <ion-icon name="cash-outline"></ion-icon>
                            </div>
                            <strong>Cash & Bank</strong>
                        </a>
                    </div>
                    <div class="item">
                        <a href="{{ route('show.Register', ['type' => 'Expense']) }}" class="action-button">
                            <div class="icon-wrapper bg-warning">
                                <ion-icon name="cash-outline"></ion-icon>
                            </div>
                            <strong>Expenses</strong>
                        </a>
                    </div>
                    <div class="item">
                        <a href="{{ route('show.Register', ['type' => 'Loan']) }}" class="action-button">
                            <div class="icon-wrapper bg-warning">
                                <ion-icon name="cash-outline"></ion-icon>
                            </div>
                            <strong>Loan</strong>
                        </a>
                    </div>

                </div>
            </div>
        </div>
        <!-- Wallet Card -->

        <!-- Stats -->
        <div class="section">
            <div class="wallet-card mt-2">
                <div class="form-group basic" style="padding: 0px 0px !important;">
                    <input type="text" class="form-control text-success" value="{{ old('dashboard_date_range') }}" name="dashboard_date_range" id="dashboard_date_range" />
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="section">
            <div class="row mt-2">
                <div class="col-6">
                    <div class="stat-box">
                        <div class="title" id="SaleText">{{ date('F') }} Sale</div>
                        <div class="value text-success" id="SaleValue"> {{ number_format($currentMonthsale-$currentMonthDiscount, 2) }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box">
                        <div class="title" id="ExpenseText">{{ date('F') }} Expenses</div>
                        <div class="value text-danger" id="ExpenseValue"> {{ number_format($currentMonthexpences, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <div class="stat-box">
                        <div class="title" id="IncomeText">{{ date('F') }} Income</div>
                        <div class="value" id="IncomeValue"> {{ number_format($currentMonthsale-$currentMonthDiscount-$currentMonthsaleCost, 2) }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box">
                        <div class="title" id="RecoveryText">{{ date('F') }} Recovery</div>
                        <div class="value" id="RecoveryValue"> {{ number_format($currentMonthcustPay, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- * Stats -->

        <div class="section mt-2 mb-2">
            <div class="card">
                <div class="card-body">
                    <div id="chart-sale"></div>
                </div>
            </div>
        </div>

        <div class="section mt-2 mb-2">
            <div class="card">
                <div class="card-body">
                    <div id="chart-recovery"></div>
                </div>
            </div>
        </div>
        <!-- app footer -->
        <div class="appFooter mt-4">
            <div class="footer-title">
                Copyright © aursoft 2023. All Rights Reserved.
            </div>
        </div>
        <!-- * app footer -->

    </div>
<script>
var today = new Date();
        var month = today.toLocaleString('default', { month: 'long' });
        // Chart Line
        Highcharts.chart('chart-sale', {
            chart: {
                type: 'column'
            },
            title: {
                text: today.getFullYear()+' Sale By Month-Wise',
            },
            xAxis: {
                categories: [@php echo rtrim($monthNames, ',') @endphp]
            },
            yAxis: {
                    title: {
                        text: null
                    }
                },
            legend: {
                enabled: true
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}'
                    }
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
            },
            series: [{
                name: 'Sales',
                colorByPoint: true,
                data: [@php echo rtrim($monthlysalevalue, ',') @endphp]
            }]
        });

        Highcharts.chart('chart-recovery', {
            chart: {
                type: 'column'
            },
            title: {
                text: today.getFullYear()+' Recovery By Month-Wise',
            },
            xAxis: {
                categories: [@php echo rtrim($R_monthNames, ',') @endphp]
            },
            yAxis: {
                    title: {
                        text: null
                    }
                },
            legend: {
                enabled: true
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}'
                    }
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
            },
            series: [{
                name: 'Recovery',
                colorByPoint: true,
                data: [@php echo rtrim($monthlyrecoveryvalue, ',') @endphp]
            }]
        });
</script>
@endsection


