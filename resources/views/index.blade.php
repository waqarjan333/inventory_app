<!doctype html>
<html lang="en">
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>Aursoft Private Limited</title>
    <meta name="description" content="Aursoft Private Limite| Make Your Business Easy">
    <meta name="keywords" content="POS, Inventory, Stock, Billing" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/icon/192x192.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">
    <script src="{{ asset('assets/js/highcharts.js') }}"></script>


</head>
<body>
    <!-- loader -->
    <div id="loader">
        <img src="{{ asset('assets/img/loading-icon.png') }}" alt="icon" class="loading-icon">
    </div>
    <!-- * loader -->
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="#" class="headerButton" data-bs-toggle="modal" data-bs-target="#sidebarPanel">
                <ion-icon name="menu-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">
            Aursoft Private Limited
        </div>
    </div>
    <!-- * App Header -->
    <!-- App Capsule -->
    @yield('contents')
    <!-- * App Capsule -->
    <!-- App Bottom Menu -->
    <div class="appBottomMenu">
        <a href="{{ route('logout') }}" class='item' onclick="event.preventDefault(); document.getElementById('logout-form1').submit();">
            <div class="col">
                <ion-icon name="log-out-outline"></ion-icon>
                <strong>Logout</strong>
            </div>
            <form id="logout-form1" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </a>
        <a href="{{ route('balanceSheet') }}" class="item">
            <div class="col">
                <ion-icon name="card-outline"></ion-icon>
                <strong>Balance Sheet</strong>
            </div>
        </a>
        <a href="{{ route('home') }}" class="item">
            <div class="col">
                <ion-icon name="home-outline"></ion-icon>
                <strong>Home</strong>
            </div>
        </a>
        <a href="#" class="item" data-bs-toggle="modal" data-bs-target="#accountPayable">
            <div class="col">
                <ion-icon name="card-outline"></ion-icon>
                <strong>Account Payable</strong>
            </div>
        </a>
        <a href="#" class="item" data-bs-toggle="modal" data-bs-target="#accountReceivable">
            <div class="col">
                <ion-icon name="card-outline"></ion-icon>
                <strong>Account Receivable</strong>
            </div>
        </a>
    </div>
    <!-- * App Bottom Menu -->
    <!-- App Sidebar -->
    <div class="modal fade panelbox panelbox-left" id="sidebarPanel" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <!-- profile box -->
                    <div class="profileBox pt-2 pb-2">
                        <div class="in">
                            <strong>Aursoft Private Limited</strong>
                        </div>
                        <a href="#" class="btn btn-link btn-icon sidebar-close" data-bs-dismiss="modal">
                            <ion-icon name="close-outline"></ion-icon>
                        </a>
                    </div>
                    <!-- * profile box -->
                    <!-- balance -->
                    <div class="sidebar-balance">
                        <div class="listview-title">Total Cash</div>
                        <div class="in">
                            <h1 class="amount">PKR {{ DB::table('account_journal')->where(['acc_id' => -1])->sum('journal_amount') }}</h1>
                        </div>
                    </div>
                    <!-- * balance -->

                    <!-- action group -->
                    <div class="action-group">
                        <a href="{{ route('show.Register', ['type' => 'Customer']) }}" class="action-button">
                            <div class="in">
                                <div class="iconbox">
                                    <ion-icon name="person-outline"></ion-icon>
                                </div>
                                Customer
                            </div>
                        </a>
                        <a href="{{ route('show.Register', ['type' => 'Vendor']) }}" class="action-button">
                            <div class="in">
                                <div class="iconbox">
                                    <ion-icon name="person-outline"></ion-icon>
                                </div>
                                Vendor
                            </div>
                        </a>
                        <a href="{{ route('show.Register', ['type' => 'Bank']) }}" class="action-button">
                            <div class="in">
                                <div class="iconbox">
                                    <ion-icon name="cash-outline"></ion-icon>
                                </div>
                                Cash & Bank
                            </div>
                        </a>
                        <a href="{{ route('show.Register', ['type' => 'Expense']) }}" class="action-button">
                            <div class="in">
                                <div class="iconbox">
                                    <ion-icon name="cash-outline"></ion-icon>
                                </div>
                                Expenses
                            </div>
                        </a>
                        <a href="{{ route('show.Register', ['type' => 'Loan']) }}" class="action-button">
                            <div class="in">
                                <div class="iconbox">
                                    <ion-icon name="cash-outline"></ion-icon>
                                </div>
                                Loan
                            </div>
                        </a>
                    </div>
                    <!-- * action group -->

                    <!-- menu -->
                    <div class="listview-title mt-1">Menu</div>
                    <ul class="listview flush transparent no-line image-listview">
                        <li>
                            <a style="min-height: 40px !important; padding: 0px 16px !important;" href="{{ route('home') }}" class="item">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="home-outline"></ion-icon>
                                </div>
                                <div class="in">Home</div>
                            </a>
                        </li>
                        <li>
                            <a style="min-height: 40px !important; padding: 0px 16px !important;" href="{{ route('balanceSheet') }}" class="item">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="card-outline"></ion-icon>
                                </div>
                                <div class="in">Balance Sheet</div>
                            </a>
                        </li>
                        <li>
                            <a style="min-height: 40px !important; padding: 0px 16px !important;" href="#" class="item" data-bs-toggle="modal" data-bs-target="#accountReceivable">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="document-text-outline"></ion-icon>
                                </div>
                                <div class="in">Account Receivable</div>
                            </a>
                        </li>
                        <li>
                            <a style="min-height: 40px !important; padding: 0px 16px !important;" href="#" class="item" data-bs-toggle="modal" data-bs-target="#paymentCollectionByRegion">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="document-text-outline"></ion-icon>
                                </div>
                                <div class="in">Pay Collection By Region</div>
                            </a>
                        </li>
                        <li>
                            <a style="min-height: 40px !important; padding: 0px 16px !important;" href="#" class="item" data-bs-toggle="modal" data-bs-target="#paymentCollectionByRepresentative">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="document-text-outline"></ion-icon>
                                </div>
                                <div class="in">Pay Collection By Rep</div>
                            </a>
                        </li>

                        <li>
                            <a style="min-height: 40px !important; padding: 0px 16px !important;" href="#" class="item" data-bs-toggle="modal" data-bs-target="#accountPayable">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="apps-outline"></ion-icon>
                                </div>
                                <div class="in">Account Payable</div>
                            </a>
                        </li>
                        <li>
                            <a style="min-height: 40px !important; padding: 0px 16px !important;" href="#" class="item" data-bs-toggle="modal" data-bs-target="#stockReport">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="apps-outline"></ion-icon>
                                </div>
                                <div class="in">Stock Report By Ware House</div>
                            </a>
                        </li>
                        <li>
                            <a style="min-height: 40px !important; padding: 0px 16px !important;" href="#" class="item" data-bs-toggle="modal" data-bs-target="#categorystockReport">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="apps-outline"></ion-icon>
                                </div>
                                <div class="in">Stock Report By Category</div>
                            </a>
                        </li>
                        <li>
                            <a style="min-height: 40px !important; padding: 0px 16px !important;" href="#" class="item" data-bs-toggle="modal" data-bs-target="#saleReport">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="apps-outline"></ion-icon>
                                </div>
                                <div class="in">Sale Report</div>
                            </a>
                        </li>
                        <li>
                            <a style="min-height: 40px !important; padding: 0px 16px !important;" href="#" class="item" data-bs-toggle="modal" data-bs-target="#saleInvoicesReport">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="apps-outline"></ion-icon>
                                </div>
                                <div class="in">Sale Invoices</div>
                            </a>
                        </li>
                        <li>
                            <a style="min-height: 40px !important; padding: 0px 16px !important;" href="{{ route('show.Add.Item.Form') }}" class="item">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="apps-outline"></ion-icon>
                                </div>
                                <div class="in">Add Item</div>
                            </a>
                        </li>
                        <li>
                            <a style="min-height: 40px !important; padding: 0px 16px !important;" href="{{ route('logout') }}" class='item' onclick="event.preventDefault(); document.getElementById('logout-form1').submit();">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="log-out-outline"></ion-icon>
                                </div>
                                <div class="in">
                                    Logout
                                </div>
                                <form id="logout-form1" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- * App Sidebar -->
    
    <!-- Account Payable -->
    <div class="modal fade action-sheet" id="accountPayable" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Account Payable</h5>
                    </div>
                    <div class="modal-body">
                        <div class="action-sheet-content">
                            <form method="POST" action="{{ route('show.Account.Payable') }}">@csrf

                                <div class="form-group boxed">
                                    <label class="label">Vendor</label>
                                    <select class="form-control custom-select" name="acc_pay_vendor">
                                        <option value="">Select Vendor</option>
                                        @foreach (DB::table('vendor')->where('vendor_status', '=', 1)->get() as $vendor)
                                        <option @if(old('acc_pay_vendor')==$vendor->vendor_id) selected @endif value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group basic">
                                    <button type="submit" class="btn btn-primary btn-block btn-lg">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <!-- * Account Payable -->

    <!-- Account Receivable -->
    <div class="modal fade action-sheet" id="accountReceivable" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Account Receivable</h5>
                    </div>
                    <div class="modal-body">
                        <div class="action-sheet-content">
                            <form method="POST" action="{{ route('show.Account.Receivable') }}">@csrf

                                <div class="form-group boxed">
                                    <label class="label">Customer Group</label>
                                    <select class="form-control custom-select" name="acc_rec_cust_group" id="acc_rec_cust_group">
                                        @foreach (DB::table('customer_groups')->get() as $custGroup)
                                        <option @if(old('acc_rec_cust_group')==$custGroup->id) selected @endif value="{{ $custGroup->id }}">{{ $custGroup->cust_group_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('acc_rec_cust_group'))
                                        <span class="text-danger">{{ $errors->first('acc_rec_cust_group') }}</span>
                                    @endif
                                </div>

                                <div class="form-group boxed">
                                    <label class="label">Customer</label>
                                    <select class="form-control custom-select" name="acc_rec_customer" id="acc_rec_customer">
                                        <option value="">Select Customer</option>
                                        @foreach (DB::table('customer')->where('cust_status', 1)->where('cust_group_id', 1)->where('cust_id', '>', 0)->get() as $customer)
                                        <option @if(old('acc_rec_customer')==$customer->cust_id) selected @endif value="{{ $customer->cust_id }}">{{ $customer->cust_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('acc_rec_customer'))
                                        <span class="text-danger">{{ $errors->first('acc_rec_customer') }}</span>
                                    @endif
                                </div>

                                <div class="form-group basic">
                                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="acc_rec_search">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <!-- * Account Receivable -->
    <!-- Payment Collection By Region -->
    <div class="modal fade action-sheet" id="paymentCollectionByRegion" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payment Collection By Region</h5>
                    </div>
                    <div class="modal-body">
                        <div class="action-sheet-content">
                            <form method="POST" action="{{ route('show.Payment.Collection.Region') }}">@csrf

                                <div class="form-group boxed">
                                    <label class="label">Customer Group</label>
                                    <select class="form-control custom-select" name="pay_collection_cust_group">
                                        <option value="">Select Group</option>
                                        @foreach (DB::table('customer_groups')->get() as $custGroup)
                                        <option @if(old('pay_collection_cust_group')==$custGroup->id) selected @endif value="{{ $custGroup->id }}">{{ $custGroup->cust_group_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('pay_collection_cust_group'))
                                        <span class="text-danger">{{ $errors->first('pay_collection_cust_group') }}</span>
                                    @endif
                                </div>
                                <div class="form-group basic">
                                    <label class="label">Date</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control text-success" value="{{ old('payment_collection_date_range') }}" name="payment_collection_date_range" id="payment_collection_date_range" />
                                    </div>
                                </div>

                                

                                <div class="form-group basic">
                                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="acc_rec_search">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <!-- * Payment Collection By Region -->

    <!-- Payment Collection BY Representative -->
    <div class="modal fade action-sheet" id="paymentCollectionByRepresentative" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payment Collection By Representative</h5>
                    </div>
                    <div class="modal-body">
                        <div class="action-sheet-content">
                            <form method="POST" action="{{ route('show.Payment.Collection.Representative') }}">@csrf

                                <div class="form-group boxed">
                                    <label class="label">Representative</label>
                                    <select class="form-control custom-select" name="pay_collection_salesrep">
                                        <option value="">Select Representative</option>
                                        @foreach (DB::table('salesrep')->get() as $salesrep)
                                        <option @if(old('pay_collection_salesrep')==$salesrep->id) selected @endif value="{{ $salesrep->id }}">{{ $salesrep->salesrep_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('pay_collection_salesrep'))
                                        <span class="text-danger">{{ $errors->first('pay_collection_salesrep') }}</span>
                                    @endif
                                </div>
                                <div class="form-group basic">
                                    <label class="label">Date</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control text-success" value="{{ old('payment_collection_salesrep_date_range') }}" name="payment_collection_salesrep_date_range" id="payment_collection_salesrep_date_range" />
                                    </div>
                                </div>

                                

                                <div class="form-group basic">
                                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="acc_rec_search">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <!-- * Payment Collection BY Representative -->

    <!-- Stock Report By Warehouse-->
    <div class="modal fade action-sheet" id="stockReport" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Stock Report</h5>
                    </div>
                    <div class="modal-body">
                        <div class="action-sheet-content">
                            <form method="POST" action="{{ route('show.Stock.Report') }}">@csrf

                                <div class="form-group boxed">
                                    <label class="label">Ware House</label>
                                    <select class="form-control custom-select" name="warehouse" id="warehouse">
                                        <option value="">Select Ware House</option>
                                        @foreach (DB::table('warehouses')->where('warehouse_isactive', 1)->orderBy('warehouse_name', 'ASC')->get() as $warehouse)
                                        <option @if(old('warehouse')==$warehouse->warehouse_id) selected @endif value="{{ $warehouse->warehouse_id }}">{{ $warehouse->warehouse_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('warehouse'))
                                        <span class="text-danger">{{ $errors->first('warehouse') }}</span>
                                    @endif
                                </div>

                                <div class="form-group boxed">
                                    <label class="label">Items</label>
                                    <select class="form-control custom-select" name="item" id="item">
                                        <option value="">Select Item</option>
                                        @foreach (DB::table('item')->where('item_status', 1)->where('type_id', 1)->orderBy('item_name', 'ASC')->get() as $item)
                                        <option @if(old('item')==$item->id) selected @endif value="{{ $item->id }}">{{ $item->item_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('item'))
                                        <span class="text-danger">{{ $errors->first('item') }}</span>
                                    @endif
                                </div>

                                <div class="form-group basic">
                                    <label class="label">Date</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control text-success" value="{{ old('stock_date') }}" name="stock_date" id="stock_date" />
                                    </div>
                                    @if ($errors->has('stock_date'))
                                        <span class="text-danger">{{ $errors->first('stock_date') }}</span>
                                    @endif
                                </div>

                                <div class="form-group basic">
                                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="acc_rec_search">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <!-- * Stock Report By Warehouse -->

    <!-- Stock Report By Category -->
    <div class="modal fade action-sheet" id="categorystockReport" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Stock Report</h5>
                    </div>
                    <div class="modal-body">
                        <div class="action-sheet-content">
                            <form method="POST" action="{{ route('show.Category.Stock.Report') }}">@csrf

                                <div class="form-group boxed">
                                    <label class="label">Category</label>
                                    <select class="form-control custom-select" name="category" id="category">
                                        <option value="">Select Category</option>
                                        @foreach (DB::table('category')->whereIn('parent_id', [0,1])->where('status', 1)->orderBy('name', 'ASC')->get() as $category)
                                        <option @if(old('category')==$category->id) selected @endif value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group boxed">
                                    <label class="label">Items</label>
                                    <select class="form-control custom-select" name="cat_item" id="cat_item">
                                        <option value="">Select Item</option>
                                        @foreach (DB::table('item')->where('item_status', 1)->where('type_id', 1)->where('category_id', 1)->orderBy('item_name', 'ASC')->get() as $item)
                                        <option @if(old('item')==$item->id) selected @endif value="{{ $item->id }}">{{ $item->item_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group basic">
                                    <label class="label">Date</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control text-success" value="{{ old('cat_stock_date') }}" name="cat_stock_date" id="cat_stock_date" />
                                    </div>
                                </div>

                                <div class="form-group basic">
                                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="cat_rec_search">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <!-- * Stock Report By Category -->

    <!-- Sale Report By Category -->
    <div class="modal fade action-sheet" id="saleReport" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Sale Report</h5>
                    </div>
                    <div class="modal-body">
                        <div class="action-sheet-content">
                            <form method="POST" action="{{ route('show.Sale.Report') }}">@csrf

                                <div class="form-group boxed">
                                    <label class="label">Category</label>
                                    <select class="form-control custom-select" name="sale_category" id="sale_category">
                                        <option value="">Select Category</option>
                                        @foreach (DB::table('category')->whereIn('parent_id', [0,1])->where('status', 1)->orderBy('name', 'ASC')->get() as $category)
                                        <option @if(old('category')==$category->id) selected @endif value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group boxed">
                                    <label class="label">Items</label>
                                    <select class="form-control custom-select" name="sale_item" id="sale_item">
                                        <option value="">Select Item</option>
                                        @foreach (DB::table('item')->where('item_status', 1)->where('type_id', 1)->where('category_id', 1)->orderBy('item_name', 'ASC')->get() as $item)
                                        <option @if(old('item')==$item->id) selected @endif value="{{ $item->id }}">{{ $item->item_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group basic">
                                    <label class="label">Date</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control text-success" value="{{ old('sale_date_range') }}" name="sale_date_range" id="sale_date_range" />
                                    </div>
                                </div>

                                <div class="form-group basic">
                                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="sale_cat_search">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <!-- * Sale Report By Category -->

    <!-- Sale Invoices Report By Category -->
    <div class="modal fade action-sheet" id="saleInvoicesReport" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Sale Invoices Report</h5>
                    </div>
                    <div class="modal-body">
                        <div class="action-sheet-content">
                            <form method="POST" action="{{ route('show.Sale.Invoices.Report') }}">@csrf

                                <div class="form-group boxed">
                                    <label class="label">Customer Group</label>
                                    <select class="form-control custom-select" name="sale_cust_group" id="sale_cust_group">
                                        <option value="">Select Group</option>
                                        @foreach (DB::table('customer_groups')->get() as $custGroup)
                                        <option @if(old('sale_cust_group')==$custGroup->id) selected @endif value="{{ $custGroup->id }}">{{ $custGroup->cust_group_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group boxed">
                                    <label class="label">Customer</label>
                                    <select class="form-control custom-select" name="sale_customer" id="sale_customer">
                                        <option value="">Select Customer</option>
                                        @foreach (DB::table('customer')->where('cust_status', 1)->where('cust_id', '>', 0)->get() as $customer)
                                        <option @if(old('sale_customer')==$customer->cust_id) selected @endif value="{{ $customer->cust_id }}">{{ $customer->cust_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('sale_customer'))
                                        <span class="text-danger">{{ $errors->first('sale_customer') }}</span>
                                    @endif
                                </div>

                                <div class="form-group basic">
                                    <label class="label">Date</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control text-success" value="{{ old('sale_rep_date_range') }}" name="sale_rep_date_range" id="sale_rep_date_range" />
                                    </div>
                                </div>

                                <div class="form-group basic">
                                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="sale_rep_search">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <!-- * Sale Report By Category -->
    

    <!-- iOS Add to Home Action Sheet -->
    <div class="modal inset fade action-sheet ios-add-to-home" id="ios-add-to-home-screen" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add to Home Screen</h5>
                    <a href="#" class="close-button" data-bs-dismiss="modal">
                        <ion-icon name="close"></ion-icon>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="action-sheet-content text-center">
                        <div class="mb-1"><img src="{{ asset('assets/img/icon/192x192.png') }}" alt="image" class="imaged w64 mb-2">
                        </div>
                        <div>
                            Install <strong>Inventory</strong> on your home screen.
                        </div>
                        <div>
                            Tap <ion-icon name="share-outline"></ion-icon> and Add to homescreen.
                        </div>
                        <div class="mt-2">
                            <button class="btn btn-primary btn-block" data-bs-dismiss="modal">CLOSE</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- * iOS Add to Home Action Sheet -->


    <!-- Android Add to Home Action Sheet -->
    <div class="modal inset fade action-sheet android-add-to-home" id="android-add-to-home-screen" tabindex="-1"
        role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add to Home Screen</h5>
                    <a href="#" class="close-button" data-bs-dismiss="modal">
                        <ion-icon name="close"></ion-icon>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="action-sheet-content text-center">
                        <div class="mb-1">
                            <img src="{{ asset('assets/img/icon/192x192.png') }}" alt="image" class="imaged w64 mb-2">
                        </div>
                        <div>
                            Install <strong>Inventory</strong> on your home screen.
                        </div>
                        <div>
                            Tap <ion-icon name="ellipsis-vertical"></ion-icon> and Add to homescreen.
                        </div>
                        <div class="mt-2">
                            <button class="btn btn-primary btn-block" data-bs-dismiss="modal">CLOSE</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('assets/js/plugins/splide/splide.min.js') }}"></script>
    <script src="{{ asset('assets/js/base.js') }}"></script>
   
    
    
    
    <script>
    AddtoHome("2000", "once");    
    $(function(){
        var todaydate = moment();
        function getMonthName(monthNumber) {
            const date = new Date();
            date.setMonth(monthNumber - 1);

            return date.toLocaleString('en-US', {
                month: 'long',
            });
        }
        function cb(todaydate) {
            $('#dashboard_date_range, #date_range, #sale_date_range').val(todaydate.format('YYYY-MM-DD') + ' - ' + todaydate.format('YYYY-MM-DD'));
        }
        function cbs(todaydate) {
            $('#stock_date, #date_paid, #cat_stock_date').val(todaydate.format('YYYY-MM-DD'));
        }
        
        $('#stock_date, #cat_stock_date').daterangepicker({
            startDate: todaydate,
            endDate: todaydate, 
            autoclose: true,
            showDropdowns:true,
            singleDatePicker: true,
            drops:'up',
            locale: {
                    format: 'YYYY-MM-DD'
                }
        }, cbs);
        $('#date_paid').daterangepicker({
            startDate: todaydate,
            endDate: todaydate, 
            autoclose: true,
            showDropdowns:true,
            singleDatePicker: true,
            drops:'down',
            locale: {
                    format: 'YYYY-MM-DD'
                }
        }, cbs);
        $('#dashboard_date_range, #date_range, #sale_date_range, #sale_rep_date_range, #payment_collection_date_range, #payment_collection_salesrep_date_range').daterangepicker({
            startDate: todaydate,
            endDate: todaydate, 
            autoclose: true,
            showDropdowns:true,
            drops:'up',
            locale: {
                    format: 'YYYY-MM-DD'
                },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            }
        }, cb);
        cb(todaydate);
        cbs(todaydate);

        $('#dashboard_date_range').on('apply.daterangepicker', function(ev, picker) {

            if(picker.startDate.format('YYYY-MM-DD') == '' || picker.endDate.format('YYYY-MM-DD') == ''){
                return false;
            }
            $.ajax({
                url:'getDashboarData/',
                type:'post',
                dataType:'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "startDate": picker.startDate.format('YYYY-MM-DD'),
                    "endDate": picker.endDate.format('YYYY-MM-DD')
                },
                success:function (response) {
                    $("#SaleText").text(picker.startDate.format('DD-MM-YYYY')+' To '+picker.endDate.format('DD MM YYYY')+' Sale');
                    $("#SaleValue").text((response.data.todaysale-response.data.discount).toFixed(2));

                    $("#ExpenseText").text(picker.startDate.format('DD-MM-YYYY')+' To '+picker.endDate.format('DD MM YYYY')+' Expense');
                    $("#ExpenseValue").text(response.data.expences.toFixed(2));

                    $("#IncomeText").text(picker.startDate.format('DD-MM-YYYY')+' To '+picker.endDate.format('DD MM YYYY')+' Income');
                    $("#IncomeValue").text((response.data.todaysale-response.data.discount-response.data.todaysaleCost).toFixed(2));

                    $("#RecoveryText").text(picker.startDate.format('DD-MM-YYYY')+' To '+picker.endDate.format('DD MM YYYY')+' Recovery');
                    $("#RecoveryValue").text(response.data.custPay.toFixed(2));


                    $('#loader').hide();
                }
            })
        });
        
        $('#stock_date, #cat_stock_date, #sale_date_range, #sale_rep_date_range, #payment_collection_date_range, #payment_collection_salesrep_date_range').on('show.daterangepicker', function(ev, picker) {
            $(".daterangepicker").css("z-index", "300000");
        });

        $('#stock_date, #cat_stock_date, #sale_date_range, #sale_rep_date_range, #payment_collection_date_range, #payment_collection_salesrep_date_range').on('hide.daterangepicker', function(ev, picker) {
            $(".daterangepicker").css("z-index", "");
        });

        $("#refresentative").change(function (){ 
            if(this.value=="" || this.value==0 || this.value=="0"){
                $('#inv_rep').val('');
            } else {
                $('#inv_rep').val(this.value);
            }
        });

        $("#acc_rec_cust_group").change(function () {
            var id = this.value;
            $('#acc_rec_customer').find('option').not(':first').remove();
            $('#acc_rec_search').text('Waiting...');
            $('#acc_rec_search').attr("disabled", true);
            $("#accountReceivable").modal('hide');
            $('#loader').show();
            $.ajax({
                url:'getCustomersByGroup/'+id,
                type:'get',
                dataType:'json',
                success:function (response) {
                    var len = 0;
                    if (response.data != null) {
                        len = response.data.length;
                    }
                    if (len>0) {
                        for (var i = 0; i<len; i++) {
                            var id = response.data[i].cust_id;
                            var name = response.data[i].cust_name;
                            var option = "<option value='"+id+"'>"+name+"</option>";
                            $("#acc_rec_customer").append(option);
                        }
                    }
                    $('#loader').hide();
                    $("#accountReceivable").modal('show');
                    $('#acc_rec_search').removeAttr("disabled");
                    $('#acc_rec_search').text('Search');

                }
             })
        });

        $("#sale_cust_group").change(function () {
            var id = this.value;
            $('#sale_customer').find('option').not(':first').remove();
            $('#sale_rep_search').text('Waiting...');
            $('#sale_rep_search').attr("disabled", true);
            $("#saleInvoicesReport").modal('hide');
            $('#loader').show();
            $.ajax({
                url:'getCustomersByGroup/'+id,
                type:'get',
                dataType:'json',
                success:function (response) {
                    var len = 0;
                    if (response.data != null) {
                        len = response.data.length;
                    }
                    if (len>0) {
                        for (var i = 0; i<len; i++) {
                            var id = response.data[i].cust_id;
                            var name = response.data[i].cust_name;
                            var option = "<option value='"+id+"'>"+name+"</option>";
                            $("#sale_customer").append(option);
                        }
                    }
                    $('#loader').hide();
                    $("#saleInvoicesReport").modal('show');
                    $('#sale_rep_search').removeAttr("disabled");
                    $('#sale_rep_search').text('Search');

                }
             })
        });

        $("#warehouse").change(function (){
            if(this.value>0){
                var id = this.value;
            } else {
                var id = 0;
            }
            $('#item').find('option').not(':first').remove();
            $('#acc_rec_search').text('Waiting...');
            $('#acc_rec_search').attr("disabled", true);
            $('#loader').show();
            $("#stockReport").modal('hide');
            $.ajax({
                url:"{{ url('getItemsByWarehouse') }}/"+id,
                type:'get',
                dataType:'json',
                success:function (response) {
                    var len = 0;
                    if (response.data != null) {
                        len = response.data.length;
                    }
                    if (len>0) {
                        for (var i = 0; i<len; i++) {
                            var id = response.data[i].id;
                            var name = response.data[i].item_name+" - "+response.data[i].item_code;
                            var option = "<option value='"+id+"'>"+name+"</option>";
                            $("#item").append(option);
                        }
                    }
                    $('#loader').hide();
                    $("#stockReport").modal('show');
                    $('#acc_rec_search').removeAttr("disabled");
                    $('#acc_rec_search').text('Search');
                    
                }
             })
        });

        $("#category").change(function (){
            if(this.value>0){
                var id = this.value;
            } else {
                var id = 0;
            }
            $('#cat_item').find('option').not(':first').remove();
            $('#cat_rec_search').text('Waiting...');
            $('#cat_rec_search').attr("disabled", true);
            $('#loader').show();
            $("#categorystockReport").modal('hide');
            $.ajax({
                url:"{{ url('getItemsByCategory') }}/"+id,
                type:'get',
                dataType:'json',
                success:function (response) {
                    var len = 0;
                    if (response.data != null) {
                        len = response.data.length;
                    }
                    if (len>0) {
                        for (var i = 0; i<len; i++) {
                            var id = response.data[i].id;
                            var name = response.data[i].item_name+" - "+response.data[i].item_code;
                            var option = "<option value='"+id+"'>"+name+"</option>";
                            $("#cat_item").append(option);
                        }
                    }
                    $('#loader').hide();
                    $("#categorystockReport").modal('show');
                    $('#cat_rec_search').removeAttr("disabled");
                    $('#cat_rec_search').text('Search');
                    
                }
             })
        });

        $("#sale_category").change(function (){
            if(this.value>0){
                var id = this.value;
            } else {
                var id = 0;
            }
            $('#sale_item').find('option').not(':first').remove();
            $('#sale_cat_search').text('Waiting...');
            $('#sale_cat_search').attr("disabled", true);
            $('#loader').show();
            $("#saleReport").modal('hide');
            $.ajax({
                url:"{{ url('getItemsByCategory') }}/"+id,
                type:'get',
                dataType:'json',
                success:function (response) {
                    var len = 0;
                    if (response.data != null) {
                        len = response.data.length;
                    }
                    if (len>0) {
                        for (var i = 0; i<len; i++) {
                            var id = response.data[i].id;
                            var name = response.data[i].item_name+" - "+response.data[i].item_code;
                            var option = "<option value='"+id+"'>"+name+"</option>";
                            $("#sale_item").append(option);
                        }
                    }
                    $('#loader').hide();
                    $("#saleReport").modal('show');
                    $('#sale_cat_search').removeAttr("disabled");
                    $('#sale_cat_search').text('Search');
                    
                }
             })
        });

        $("#cust_group").change(function (){
            var id = this.value;
            $('#customer').find('option').not(':first').remove();
            $('#search').text('Waiting...');
            $('#search').attr("disabled", true);
            $('#loader').show();
            $.ajax({
                url:"{{ url('getCustomersByGroup') }}/"+id,
                type:'get',
                dataType:'json',
                success:function (response) {
                    var len = 0;
                    if (response.data != null) {
                        len = response.data.length;
                    }
                    if (len>0) {
                        for (var i = 0; i<len; i++) {
                            var id = response.data[i].cust_id;
                            var name = response.data[i].cust_name;
                            var option = "<option value='"+id+"'>"+name+"</option>";
                            $("#customer").append(option);
                        }
                    }
                    $('#loader').hide();
                    $('#search').removeAttr("disabled");
                    $('#search').text('Search');
                }
             })
        });

        $("#inv_cust_group").change(function (){
            var id = this.value;
            $('#inv_customer').find('option').not(':first').remove();
            $('#loader').show();
            $.ajax({
                url:"{{ url('getCustomersByGroup') }}/"+id,
                type:'get',
                dataType:'json',
                success:function (response) {
                    var len = 0;
                    if (response.data != null) {
                        len = response.data.length;
                    }
                    if (len>0) {
                        for (var i = 0; i<len; i++) {
                            var id = response.data[i].cust_id;
                            var name = response.data[i].cust_name;
                            var option = "<option value='"+id+"'>"+name+"</option>";
                            $("#inv_customer").append(option);
                        }
                    }
                    $('#loader').hide();
                }
             })
        });

        $("#sale_invoice_category").change(function (){
            if(this.value>0){
                var id = this.value;
            } else {
                var id = 0;
            }
            $('#sale_invoice_item').find('option').not(':first').remove();
            $.ajax({
                url:"{{ url('getItemsByCategory') }}/"+id,
                type:'get',
                dataType:'json',
                success:function (response) {
                    var len = 0;
                    if (response.data != null) {
                        len = response.data.length;
                    }
                    if (len>0) {
                        for (var i = 0; i<len; i++) {
                            var id = response.data[i].id;
                            var name = response.data[i].item_name+" - "+response.data[i].item_code;
                            var option = "<option value='"+id+"'>"+name+"</option>";
                            $("#sale_invoice_item").append(option);
                        }
                    }
                }
             })
        });

        $("#sale_invoice_item").change(function (){
            if(this.value>0){
                var id = this.value;
            } else {
                var id = 0;
            }
            $('#item_uom').find('option').not(':first').remove();
            $.ajax({
                url:"{{ url('getItemsDetails') }}/"+id,
                type:'get',
                dataType:'json',
                success:function (response) {
                    var len = 0;
                    var select = "";
                    var salePrice = "";
                    if (response.unit != null) {
                        len = response.unit.length;
                    }
                    if (len>0) {
                        for (var i = 0; i<len; i++) {
                            var unit_id = response.unit[i].unit_id;
                            var unit_name = response.unit[i].name;
                            if(response.data.sale_unit==unit_id){
                                select = "selected";
                                salePrice = response.unit[i].sale_price;
                            } else {
                                select = "";
                                salePrice = response.data.sale_price;
                            }
                            var option = "<option item_id="+response.data.id+" "+select+" value='"+unit_id+"'>"+unit_name+"</option>";
                            $("#item_uom").append(option);
                        }
                    }
                    if (response.data != null) {
                        $("#quantity").val(1);
                        $("#item_discount").val('');
                        $("#unit_price").val(salePrice.toFixed(4));
                        $("#net_price").val(salePrice);
                        $("#sub_total").val(salePrice.toFixed(4));
                    }
                }
             })
        });

        $("#item_uom").change(function (){
            if(this.value>0){
                var unit_id = this.value;
            } else {
                var unit_id = 0;
            }
            var item_id = $('option:selected', this).attr('item_id');
            console.log(unit_id);
            console.log(item_id);
            $.ajax({
                url:"{{ url('getUnitPrice') }}/"+item_id+"/"+unit_id,
                type:'get',
                dataType:'json',
                success:function (response) {
                    var len = 0;
                    if (response.data != null) {
                        console.log(response.data);
                        $("#quantity").val(1);
                        $("#item_discount").val('');
                        $("#unit_price").val(response.data.toFixed(4));
                        $("#net_price").val(response.data);
                        $("#sub_total").val(response.data.toFixed(4));
                    }
                    
                }
             })
        });

        $('#quantity').on('input',function(e){
            var total = parseFloat(this.value) * parseFloat($("#unit_price").val());
            if (isNaN(total) == false) {
                $("#sub_total").val(total.toFixed(2));
            } else {
                $("#sub_total").val('');
            }
        });

        $('#unit_price').on('input',function(e){
            if(parseFloat(this.value)>=parseFloat($("#net_price").val()) && $("#item_discount").val()==''){
                $("#item_discount").val('');
                var total = (parseFloat($("#quantity").val()) * parseFloat(this.value));
                $("#sub_total").val(total.toFixed(2));
            } else {
                var valueDiff = parseFloat($("#net_price").val()) - parseFloat(this.value);
                var discountValue = (valueDiff/parseFloat($("#net_price").val()))*100;
                if(discountValue.toFixed(4)<=0){
                    $("#item_discount").val('');
                } else {
                    $("#item_discount").val(discountValue.toFixed(4));
                }
                var total = parseFloat(this.value)*parseFloat($("#quantity").val());;
                $("#sub_total").val(total.toFixed(2));
            }
        });
        
        $('#item_discount').on('input',function(e){
            if(this.value=='' || this.value==0){
                $("#unit_price").val(parseFloat($("#net_price").val()));
                $("#sub_total").val(parseFloat($("#net_price").val())*parseFloat($("#quantity").val()));
            } else {
                var valueDiff = (parseFloat($("#item_discount").val())/100) * parseFloat($("#net_price").val());
                var unitValue = parseFloat($("#net_price").val())-valueDiff.toFixed(6);
                $("#unit_price").val(unitValue);
                $("#sub_total").val(unitValue*parseFloat($("#quantity").val()));
            }
        });

        $('#sub_total').on('input',function(e){
            var unitPrice = this.value/parseFloat($("#quantity").val());
            $("#unit_price").val(unitPrice.toFixed(2));
            if($("#item_discount").val()=='' || $("#item_discount").val()==0 || $("#item_discount").val()=='0.00'){
                $("#item_discount").val('');
            } else {
                var valueDiff = parseFloat($("#net_price").val()) - parseFloat($("#unit_price").val());
                var discountvalue = (valueDiff/parseFloat($("#net_price").val()))*100;
                if(discountvalue.toFixed(4)<=0){
                    $("#item_discount").val('');
                } else {
                    $("#item_discount").val(discountvalue.toFixed(4));
                }
                
            }
        });
    });
</script>

</body>

</html>