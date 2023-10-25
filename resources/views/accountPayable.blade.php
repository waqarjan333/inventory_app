@extends('index')
@section('title', 'Aursoft')
@section('contents')
@inject('carbon', 'Carbon\Carbon')
<div id="appCapsule">
    <div class="section mt-1">
            <div class="section-title">
                <div class="detail" style="width: 70% !important; float:left">
                    <strong>Account Payable</strong>
                </div>
                <div class="right text-warning" style="width: 30% !important; float:left; text-align: right">{{ number_format($totalAccountPayable, 2) }}</div>
            </div>
        <div class="transactions mt-1">
            @php $count = 1; @endphp
            @foreach ($account_payable as $accountPay)
                <a href="#" class="item" style="width:100%; padding: 20px 10px !important">
                    <div class="detail" style="width: 70% !important">
                        <div>
                            <strong>{{ $count }} - {{ $accountPay['vendor_name'] }}</strong>
                        </div>
                    </div>
                    <div class="right" style="width: 30% !important; text-align: right">
                        <div class="price text-warning">{{ number_format($accountPay['amount'], 2) }}</div>
                    </div>
                </button>
                </a>
                @php $count++; @endphp
            @endforeach
        </div>
    </div>
</div>
@endsection



