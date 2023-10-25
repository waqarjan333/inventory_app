@extends('index')
@section('title', 'Aursoft')
@section('contents')
@inject('carbon', 'Carbon\Carbon')
<div id="appCapsule">
    <div class="section" style="background-color: #FFFFFF;">
        <div class="section-title text-center">{{ $acc_id->vendor_name }}</div>
        <div class="transactions">
            <form method="POST" action="{{ route('vendor.Register.Payment') }}">@csrf
                <div class="form-group basic">
                    <label class="label">Date</label>
                    <div class="input-group date">
                        <input type="text" class="form-control text-success" value="{{ old('date_paid') }}" name="date_paid" id="vendor_register_pay_date" />
                    </div>
                    @if ($errors->has('date_paid'))
                        <span class="text-danger">{{ $errors->first('date_paid') }}</span>
                    @endif
                </div>
                <div class="form-group boxed">
                    <label class="label" for="account1">Type</label>
                    <select class="form-control custom-select" name="type">
                        <option value="1">Payment</option>
                        <option value="2">Charge</option>
                        <option value="3">Discount</option>
                    </select>
                    @if ($errors->has('type'))
                        <span class="text-danger">{{ $errors->first('type') }}</span>
                    @endif
                </div>
                <input type="hidden" name="vendor_acc_id" value="{{ $acc_id->vendor_acc_id }}" />
                <input type="hidden" name="vendor_id" value="{{ $acc_id->vendor_id }}" />
                <input type="hidden" name="vendor_name" value="{{ $acc_id->vendor_name }}" />
                <div class="form-group boxed">
                    <label class="label" for="account1">Balance</label>
                    <input type="text" class="form-control" value="{{ number_format($vendorBalance*-1, 2) }}" readonly />
                </div>
                <div class="form-group boxed">
                    <label class="label" for="account1">Pay</label>
                    <input type="text" class="form-control" name="paid_total" autofocus />
                    @if ($errors->has('paid_total'))
                        <span class="text-danger">{{ $errors->first('paid_total') }}</span>
                    @endif
                </div>
                <div class="form-group boxed">
                    <label class="label">Payment Method</label>
                    <select class="form-control custom-select" name="payment_method">
                        <option value="-1">Cash</option>
                        @foreach (DB::table('account_chart')->where(['acc_type_id' => 8])->get() as $paymentMethod)
                            <option value="{{ $paymentMethod->acc_id }}">{{ $paymentMethod->acc_name }}</option>        
                        @endforeach
                    </select>
                    @if ($errors->has('payment_method'))
                        <span class="text-danger">{{ $errors->first('payment_method') }}</span>
                    @endif
                </div>
                <div class="form-group boxed">
                    <label class="label">Refresentative</label>
                    <select class="form-control custom-select" name="refresentative">
                        @foreach (DB::table('salesrep')->get() as $saleRef)
                            <option value="{{ $saleRef->id }}">{{ $saleRef->salesrep_name }}</option>        
                        @endforeach
                    </select>
                    @if ($errors->has('refresentative'))
                        <span class="text-danger">{{ $errors->first('refresentative') }}</span>
                    @endif
                </div>
                <div class="form-group boxed">
                    <label class="label" for="account1">Remarks</label>
                    <textarea name="remarks" class="form-control"> </textarea>
                </div>
                <div class="form-group basic">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">Search</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection



