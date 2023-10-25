@extends('index')
@section('title', 'Aursoft')
@section('contents')
@inject('carbon', 'Carbon\Carbon')
<style>
    .saleInvoiceInput{
        height: 35px !important; 
        font-size: 14px !important;
    }
</style>
@php 
if($invoice) {
    if($invoice->cust_id){
        $custID = $invoice->cust_id;
        $refID = $invoice->salesrep_id;
    } else {
        $custID = 0;
        $refID = 0;
    }
} else {
    $custID = 0;
    $refID = 0;
}
@endphp
<div id="appCapsule">
    <form method="POST" action="{{ route('create.Sale.Invoice') }}">@csrf
        <div class="section mt-1">
            <div class="section-title text-left w-70 float-start">Create New Sale Invoice</div>
            <div class="transactions">
                <div class="form-group boxed" style="padding: 3px 0px !important">
                    <select class="form-control custom-select saleInvoiceInput" id="inv_cust_group" name="inv_cust_group">
                        @foreach (DB::table('customer_groups')->get() as $custGroup)
                        <option @if(old('inv_cust_group')==$custGroup->id) selected @endif value="{{ $custGroup->id }}">{{ $custGroup->cust_group_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group boxed" style="padding: 3px 0px !important">
                    <select class="form-control custom-select saleInvoiceInput"  name="inv_customer" id="inv_customer">
                        <option value="">Select Customer</option>
                        @foreach (DB::table('customer')->where('cust_status', 1)->where('cust_group_id', 1)->where('cust_id', '>', 0)->get() as $customer)
                            <option @if($custID==$customer->cust_id) selected @endif value="{{ $customer->cust_id }}">{{ $customer->cust_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('inv_customer'))
                        <span class="text-danger">{{ $errors->first('inv_customer') }}</span>
                    @endif
                </div>
                <div class="form-group boxed" style="padding: 3px 0px !important">
                    <select class="form-control custom-select saleInvoiceInput" name="refresentative" id="refresentative">
                        @foreach (DB::table('salesrep')->get() as $saleRef)
                            <option @if($refID==$saleRef->id) selected @endif value="{{ $saleRef->id }}">{{ $saleRef->salesrep_name }}</option>        
                        @endforeach
                    </select>
                    @if ($errors->has('refresentative'))
                        <span class="text-danger">{{ $errors->first('refresentative') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="section">
            <div class="section-title w-100">
                @if($invoice_id!=NULL || $invoice_id>0)
                    <p class="text-center mb-0">{{ 'INV-'.$invoice->invoice_no }}</p>
                @endif
            </div>
            <div class="card w-100">
                <div class="card-body">
                    <ul class="nav nav-tabs capsuled" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link text-success active" data-bs-toggle="tab" href="#addItems" role="tab">
                                Add Item
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-success" data-bs-toggle="tab" href="#itemsList" role="tab">
                                Item List
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-success" data-bs-toggle="tab" href="#invoicePayment" role="tab">
                                Payment
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content mt-1">
                        <div class="tab-pane fade show active" id="addItems" role="tabpanel">
                            <form method="POST" id="add_Item_To_Sale_Invoice">
                                <div class="form-group boxed">
                                    <label class="label">Category</label>
                                    <select class="form-control custom-select" name="sale_invoice_category" id="sale_invoice_category">
                                        <option value="">Select Category</option>
                                        @foreach (DB::table('category')->whereIn('parent_id', [0,1])->where('status', 1)->orderBy('name', 'ASC')->get() as $category)
                                        <option @if(old('sale_invoice_category')==$category->id) selected @endif value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group boxed">
                                    <label class="label">Items</label>
                                    <select class="form-control custom-select" name="sale_invoice_item" id="sale_invoice_item">
                                        <option value="">Select Item</option>
                                        @foreach (DB::table('item')->where('item_status', 1)->where('type_id', 1)->orderBy('item_name', 'ASC')->get() as $item)
                                        <option @if(old('item')==$item->id) selected @endif value="{{ $item->id }}">{{ $item->item_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('sale_invoice_item'))
                                        <span class="text-danger">{{ $errors->first('sale_invoice_item') }}</span>
                                    @endif
                                </div>
                                <div class="form-group boxed" style="width: 48% !important; float:left !important; margin-right:4% !important">
                                    <label class="label">UOM</label>
                                    <select class="form-control custom-select" name="item_uom" id="item_uom">
                                        <option value="">Select UOM</option>
                                    </select>
                                    @if ($errors->has('item_uom'))
                                        <span class="text-danger">{{ $errors->first('item_uom') }}</span>
                                    @endif
                                </div>
                                <div class="form-group boxed" style="width: 48% !important; float:left !important;">
                                    <label class="label">Ware House</label>
                                    <select class="form-control custom-select" name="warehouse" id="warehouse_id">
                                        @foreach (DB::table('warehouses')->orderBy('warehouse_name', 'ASC')->get() as $warehouse)
                                        <option @if(old('warehouse')==$warehouse->warehouse_id) selected @endif value="{{ $warehouse->warehouse_id }}">{{ $warehouse->warehouse_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group boxed" style="width: 48% !important; float:left !important; margin-right:4% !important">
                                    <label class="label">Quantity</label>
                                    <input type="text" class="form-control custom-select" name="quantity" id="quantity" value="1" />
                                    @if ($errors->has('quantity'))
                                        <span class="text-danger">{{ $errors->first('quantity') }}</span>
                                    @endif
                                </div>
                                <div class="form-group boxed" style="width: 48% !important; float:left !important;">
                                    <label class="label">Unit Price</label>
                                    <input type="text" class="form-control custom-select" name="unit_price" id="unit_price" />
                                    <input type="hidden" name="net_price" id="net_price" />
                                    <input type="hidden" name="invoice_id" id="invoice_id" value="{{ $invoice_id }}" />
                                    @if ($errors->has('unit_price'))
                                        <span class="text-danger">{{ $errors->first('unit_price') }}</span>
                                    @endif
                                </div>
                                <div class="form-group boxed" style="width: 48% !important; float:left !important; margin-right:4% !important">
                                    <label class="label">Discount</label>
                                    <input type="text" class="form-control custom-select" name="item_discount" id="item_discount" />
                                </div>
                                <div class="form-group boxed" style="width: 48% !important; float:left !important;">
                                    <label class="label">Sub Total</label>
                                    <input type="text" class="form-control custom-select" name="sub_total" id="sub_total" />
                                    @if ($errors->has('sub_total'))
                                        <span class="text-danger">{{ $errors->first('sub_total') }}</span>
                                    @endif
                                </div>
                                <div class="form-group basic">
                                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="add_item_to_invoice">@if($invoice_id==NULL || $invoice_id=="") Save Invoice @else Add Item @endif</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="itemsList" role="tabpanel">
                            <div class="transactions mt-1">
                                @php 
                                    $totalQuantity = $totalUnitAmount = $totalDiscount = 0;
                                @endphp
                                @if($invoice_id!=NULL || $invoice_id>0)
                                @if($invoiceItems > 0)
                                @foreach ($invoiceDetails as $key => $invoiceDetail)
                                @php $saleP = ($invoiceDetail->inv_item_discount/100)*$invoiceDetail->inv_item_price; @endphp
                                <a href="#" class="item" style="width:100%; padding: 20px 10px !important">
                                    <div class="detail" style="width: 70% !important">
                                        <div>
                                            <strong>{{ $key+1 }} - {{ $invoiceDetail->inv_item_name }}</strong>
                                            <p>{{ $invoiceDetail->unit_name }} - {{ $invoiceDetail->item_quantity }} - {{ $invoiceDetail->inv_item_price}} - {{ $invoiceDetail->inv_item_discount }}% - {{ $invoiceDetail->inv_item_price-$saleP }}</p>
                                        </div>
                                    </div>
                                    @php 
                                    $totalQuantity = $totalQuantity + $invoiceDetail->item_quantity; 
                                    $totalUnitAmount = $totalUnitAmount + ($invoiceDetail->inv_item_price*$invoiceDetail->item_quantity); 
                                    @endphp
                                    <div class="right" style="width: 30% !important; text-align: right">
                                        <div class="price text-warning">{{ $invoiceDetail->inv_item_subTotal}}</div>
                                    </div>
                                </a>
                                @endforeach
                                @else 
                                <a href="#" class="item" style="width:100%; padding: 20px 10px !important">
                                    <div class="detail">
                                        <p class="text-primary">No Item Found. Please Add Item To Invoice</p>
                                    </div>
                                </a>
                                @endif
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane fade" id="invoicePayment" role="tabpanel">
                            <div class="section mt-1 mb-1">
                                <div class="listed-detail mt-1">
                                    <p class="text-center">Invoice Detail As Amount</p>
                                </div>
                                @if($invoice!=NULL)
                                @php $balance = $invoice->invoice_total-$invoice->discount-$invoice->invoice_paid; @endphp
                                <ul class="listview flush transparent simple-listview no-space mt-1">
                                    <li><strong>Total</strong><span class="text-success">{{ $invoice->invoice_total }}</span></li>
                                    <li><strong>Discount</strong><span class="text-success">{{ $invoice->discount }}</span></li>
                                    <li><strong>Paid</strong><span class="text-success">{{ $invoice->invoice_paid }}</span></li>
                                    <li><strong>Balance</strong><span class="text-success">{{ $balance }}</span></li>
                                    <li><strong>Previous Balance</strong><span class="text-success">{{ $invoice->previous_balance }}</span></li>
                                    <li><strong>Grand Total</strong><span class="text-success">{{ $balance+$invoice->previous_balance }}</span></li>
                                </ul>
                                @endif
                            </div>
                            <div class="section mt-1 mb-1">
                                <div class="listed-detail mt-1">
                                    <p class="text-center">Invoice Detail As Items</p>
                                </div>
                                @if($invoiceItems>0)
                                <ul class="listview flush transparent simple-listview no-space mt-1">
                                    <li><strong>Total Item</strong><span class="text-success">{{ $invoiceItems }}</span></li>
                                    <li><strong>Total Quantities</strong><span class="text-success">{{ $totalQuantity }}</span></li>
                                    <li><strong>Total Unit Amount</strong><span class="text-success">{{ $totalUnitAmount }}</span></li>
                                    <li><strong>Total Item Discount</strong><span class="text-success">{{ $totalUnitAmount-($invoice->invoice_total ?? 0 ) }}</span></li>
                                </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection




