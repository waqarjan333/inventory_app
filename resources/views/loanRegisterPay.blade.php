@extends('index')
@section('title', 'Aursoft')
@section('contents')
@inject('carbon', 'Carbon\Carbon')
<div id="appCapsule">
    <div class="section" style="background-color: #FFFFFF;">
        
        @if (\Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show mt-1 text-center" role="alert">
                {!! \Session::get('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @php $flag = $acc_id->acc_type_id==14 ? " (L/R)": " (L/P)"; @endphp
        <div class="section-title text-center">{{ $acc_id->acc_name.$flag }}</div>
        <div class="transactions">
            <form method="POST" action="{{ route('loan.Register.Payment') }}">@csrf
                <div class="form-group basic">
                    <label class="label">Date</label>
                    <div class="input-group date">
                        <input type="text" class="form-control text-success" value="{{ old('date_paid') }}" name="date_paid" id="loan_register_pay_date" />
                    </div>
                    @if ($errors->has('date_paid'))
                        <span class="text-danger">{{ $errors->first('date_paid') }}</span>
                    @endif
                </div>
                <div class="form-group boxed">
                    <label class="label" for="account1">Type</label>
                    <select class="form-control custom-select" name="type">
                        <option value="1">Payment</option>
                        <option value="2">Return</option>
                    </select>
                    @if ($errors->has('type'))
                        <span class="text-danger">{{ $errors->first('type') }}</span>
                    @endif
                </div>
                <input type="hidden" name="acc_id" value="{{ $acc_id->acc_id }}" />
                <input type="hidden" name="acc_name" value="{{ $acc_id->acc_name }}" />
                <input type="hidden" name="acc_type_id" value="{{ $acc_id->acc_type_id }}" />
                <div class="form-group boxed">
                    <label class="label" for="account1">Balance</label>
                    <input type="text" class="form-control" value="{{ number_format($loanBalance, 2) }}" readonly />
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



