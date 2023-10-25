@extends('index')
@section('title', 'Aursoft')
@section('contents')
@inject('carbon', 'Carbon\Carbon')
<div id="appCapsule">
    <div class="section mt-1">
            <div class="section-title">
                <div class="detail" style="width: 50% !important; float:left">
                    <strong>{{ array_slice($account_receivable, -1)[0]['cust_group_name'] }}</strong>
                </div>

                <div class="payment text-center text-danger" style="width: 25% !important; float:left">{{ number_format($totallastPayment, 2) }}</div>

                <div class="right text-warning" style="width: 25% !important; float:left; text-align: right">{{ number_format($totalAccountReceivable, 2) }}</div>
            </div>
        <div class="transactions mt-1">
            @php $count = 1; @endphp
            @foreach ($account_receivable as $accountReceiv)
            @if($accountReceiv['amount']>1)
                <a href="#" class="item" style="width:100%; padding: 20px 10px !important">
                    <div class="detail" style="width: 50% !important">
                        <div>
                            <strong>{{ $count }} - {{ $accountReceiv['cust_name'] }}</strong>
                            <p>{{ $accountReceiv['cust_mobile'] }}</p>
                        </div>
                    </div>
                    <div class="payment text-center" style="width: 25% !important;">
                        <div class="price text-danger">
                            <strong>{{ number_format($accountReceiv['last_amount'], 2) }}</strong>
                            <p>{{ $accountReceiv['last_amount_date'] }}</p>
                        </div>
                    </div>
                    <div class="right" style="width: 25% !important; text-align: right">
                        <div class="price text-warning">{{ number_format($accountReceiv['amount'], 2) }}</div>
                    </div>
                </button>
                </a>
                @php $count++; @endphp
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection



