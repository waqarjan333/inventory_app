@extends('index')
@section('title', 'Aursoft')
@section('contents')
@inject('carbon', 'Carbon\Carbon')
<div id="appCapsule">
    <div class="section" style="background-color: #FFFFFF;">
        @if (\Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-1 text-center" role="alert">
                {!! \Session::get('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (\Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show mt-1 text-center" role="alert">
                {!! \Session::get('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @php 
        $accountName = $account->acc_name; 
        if($type=='Customer'){ $accountName = str_replace("CUST_","", $account->acc_name); } 
        elseif($type=='Vendor'){ $accountName = str_replace("Vend_","", $account->acc_name); }
        else { $accountName = $account->acc_name; } 
        @endphp
        <div class="section-title text-center">{{ $accountName; }} - {{ $type }}</div>
        <div class="transactions">
            <form method="POST" action="{{ route('register.Payment') }}">@csrf
                <div class="form-group basic">
                    <label class="label">Date</label>
                    <div class="input-group date">
                        <input type="text" class="form-control text-success" value="{{ old('date_paid') }}" name="date_paid" id="date_paid" />
                    </div>
                    @if ($errors->has('date_paid'))
                        <span class="text-danger">{{ $errors->first('date_paid') }}</span>
                    @endif
                </div>
                @if($type=='Customer' || $type=='Vendor' || $type=='Expense' || $type=='Loan')
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
                @endif
                <input type="hidden" name="acc_id" value="{{ $account->acc_id }}" />
                <input type="hidden" name="acc_name" value="{{ $account->acc_name }}" />
                <input type="hidden" name="Regtype" value="{{ $type }}" />
                @if($type=='Customer' || $type=='Vendor' || $type=='Loan')
                <div class="form-group boxed">
                    <label class="label" for="account1">Balance</label>
                    <input type="text" class="form-control" value="{{ number_format($Balance, 2) }}" readonly />
                </div>
                @endif
                <div class="form-group boxed">
                    <label class="label" for="account1">@if($type=='Bank') Deposite @else Pay @endif</label>
                    <input type="text" class="form-control" name="paid_total" autofocus />
                    @if ($errors->has('paid_total'))
                        <span class="text-danger">{{ $errors->first('paid_total') }}</span>
                    @endif
                </div>
                <div class="form-group boxed">
                    <label class="label">Payment Method</label>
                    <select class="form-control custom-select" name="payment_method">
                        @if($account->acc_id!="-1")
                        <option value="-1">Cash</option>
                        @endif
                        @foreach (DB::table('account_chart')->where('acc_type_id', '=', 8)->where('acc_id', '!=', $account->acc_id)->get() as $paymentMethod)
                            <option value="{{ $paymentMethod->acc_id }}">{{ $paymentMethod->acc_name }}</option>        
                        @endforeach
                    </select>
                    @if ($errors->has('payment_method'))
                        <span class="text-danger">{{ $errors->first('payment_method') }}</span>
                    @endif
                </div>
                @if($type=='Customer' || $type=='Vendor' || $type=='Expense')
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
                @endif
                <div class="form-group boxed">
                    <label class="label" for="account1">Remarks</label>
                    <textarea name="remarks" class="form-control"> </textarea>
                </div>
                <div class="form-group basic">
                    <a href="{{ url('showRegister/'.$type) }}" class="btn btn-danger btn-lg col-3 float-left me-3" id="cust_search">Back</a>
                    <button type="submit" class="btn btn-primary btn-lg col-8">Pay</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection



