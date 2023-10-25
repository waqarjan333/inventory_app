@extends('index')
@section('title', 'Aursoft')
@section('contents')
@inject('carbon', 'Carbon\Carbon')
<div id="appCapsule">
    <div class="section mt-1">
        <div class="section-title">
            {{ $acc_id->vendor_name }}
            @if(count($accounts_array)>0)
                <a href="{{ route('show.Vendor.Register.Pay', $acc_id->vendor_id) }}" class="btn btn-primary" style="float:right; margin-top:-5px;">Pay</a>
            @endif
        </div>
        <div class="transactions mt-1">
            @foreach ($accounts_array['register'] as $accountArray)
                <a href="#" class="item" style="width:100%; padding: 20px 10px !important">
                    <div class="detail" style="width: 50% !important">
                        <div>
                            <strong>{{ $carbon::parse($accountArray['date'])->format('d M Y') }} - {{ $accountArray['account'] }}</strong>
                            <p>@if($accountArray['number']!='') {{ $accountArray['number'] }} @else {{ $accountArray['detail'] }}  @endif</p>
                        </div>
                    </div>
                    @if($accountArray['detail']!='Previous Balance')
                        <div class="payment text-center" style="width: 25% !important;">
                            @if($accountArray['increase']!="")
                                <div class="price text-success"><strong>{{ number_format($accountArray['increase'], 2) }}</strong></div>
                            @elseif($accountArray['decrease']!="")
                                <div class="price text-danger"><strong>{{ number_format($accountArray['decrease'], 2) }}</strong></div>
                            @endif
                        </div>
                    @endif
                    <div class="right" style="width: 25% !important; text-align: right">
                        <div class="price text-warning">{{ number_format($accountArray['balance'], 2) }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection



