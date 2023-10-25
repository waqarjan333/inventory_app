@extends('index')
@section('title', 'Aursoft')
@section('contents')
@inject('carbon', 'Carbon\Carbon')
<div id="appCapsule">
    <div class="section mt-1">

        @if (\Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show mt-1 text-center" role="alert">
                {!! \Session::get('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="section-title text-center">
            {{ $type }}'s Register
        </div>
        <div class="transactions mt-1">
            <form method="POST" action="{{ route('show.Register.Details') }}">@csrf
                <input type="hidden" name="type" value="{{ $type }}" />
                @if($type=='Customer')
                <div class="form-group boxed">
                    <label class="label" for="account1">Customer Group</label>
                    <select class="form-control custom-select" id="cust_group" name="cust_group">
                        @foreach (DB::table('customer_groups')->get() as $custGroup)
                        <option @if(old('cust_group')==$custGroup->id) selected @endif value="{{ $custGroup->id }}">{{ $custGroup->cust_group_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group boxed">
                    <label class="label">Customer</label>
                    <select class="form-control custom-select" name="customer" id="customer">
                        <option value="">Select Customer</option>
                        @foreach ($data as $customer)
                        <option @if(old('customer')==$customer->cust_id) selected @endif value="{{ $customer->cust_id }}">{{ $customer->cust_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('customer'))
                        <span class="text-danger">{{ $errors->first('customer') }}</span>
                    @endif
                </div>
                @elseif ($type=='Vendor')
                <div class="form-group boxed">
                    <label class="label">Vendor</label>
                    <select class="form-control custom-select" name="vendor" >
                        <option value="">Select Vendor</option>
                        @foreach ($data as $vendor)
                        <option @if(old('vendor')==$vendor->vendor_id) selected @endif value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('vendor'))
                        <span class="text-danger">{{ $errors->first('vendor') }}</span>
                    @endif
                </div>
                @elseif ($type=='Bank')
                <div class="form-group boxed">
                    <label class="label">Cash & Bank</label>
                    <select class="form-control custom-select" name="bank">
                        <option value="">Select Account</option>
                        <option value="-1">Cash</option>
                        @foreach ($data as $bank)
                        <option @if(old('bank')==$bank->acc_id) selected @endif value="{{ $bank->acc_id }}">{{ $bank->acc_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('bank'))
                        <span class="text-danger">{{ $errors->first('bank') }}</span>
                    @endif
                </div>
                @elseif ($type=='Expense')
                <div class="form-group boxed">
                    <label class="label">Expenses</label>
                    <select class="form-control custom-select" name="expense">
                        <option value="">Select Expense</option>
                        @foreach ($data as $expense)
                        <option @if(old('expense')==$expense->acc_id) selected @endif value="{{ $expense->acc_id }}">{{ $expense->acc_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('expense'))
                        <span class="text-danger">{{ $errors->first('expense') }}</span>
                    @endif
                </div>
                @elseif ($type=='Loan')
                <div class="form-group boxed">
                    <label class="label">Loan Account</label>
                    <select class="form-control custom-select" name="loan">
                        <option value="">Select Loan</option>
                        @foreach ($data as $loan)
                        @php $flag = $loan->acc_type_id==14 ? " (L/R)": " (L/P)"; @endphp
                        <option @if(old('loan')==$loan->acc_id) selected @endif value="{{ $loan->acc_id }}">{{ $loan->acc_name.$flag }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('loan'))
                        <span class="text-danger">{{ $errors->first('loan') }}</span>
                    @endif
                </div>
                @endif
                <div class="form-group basic">
                    <label class="label">Date Range</label>
                    <div class="input-group date">
                        <input type="text" class="form-control text-success" value="{{ old('date_range') }}" name="date_range" id="date_range" />
                    </div>
                    @if ($errors->has('date_range'))
                        <span class="text-danger">{{ $errors->first('date_range') }}</span>
                    @endif
                </div>

                <div class="form-group basic">
                    <a href="{{ url('/') }}" class="btn btn-danger btn-lg col-3 float-left me-3" id="cust_search">Back</a>
                    <button type="submit" class="btn btn-primary btn-lg col-8" id="cust_search">Search</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection




