@extends('index')
@section('title', 'Aursoft')
@section('contents')
@inject('carbon', 'Carbon\Carbon')
<div id="appCapsule">
    <div class="section mt-1">
        <div class="section-title text-center">
            Payment Collection By Regions
        </div>
        <div class="transactions mt-1">
            @if(count($report['records'])>0)
                @foreach ($report['records'] as  $record)
                    @if($record['is_type']=="group_name")
                        <a href="#" class="item bg-secondary" style="padding: 5px 10px !important">
                            <div class="detail">
                                <div><strong class="text-white">{{ $record['group_name'] }}</strong></div>
                            </div>
                        </a>
                    @endif
                    @if($record['is_type']=="payments")
                        <a href="#" class="item" style="width:100%; padding: 20px 10px !important">
                            <div class="detail w-100">
                                <div class="w-100">
                                    <strong class="w-100">{{ $record['count']; }} - {{ $record['customer_name']; }}</strong>
                                    <p class="text-warning w-50 float-start text-left">ACC-{{ $record['acc_name']; }}</p>
                                    <p class="text-warning w-25 float-start text-center">{{ $carbon::parse($record['entry_date'])->format('d M Y'); }}</p>
                                    <p class="text-warning w-25 float-end text-end">{{ $record['amount']; }}</p>
                                </div>
                            </div>
                        </a>
                    @endif
                    @if($record['is_type']=="group_total")
                        <a href="#" class="item bg-primary" style="width:100%; padding: 5px 10px !important">
                            <div class="detail w-100">
                                <div class="w-100">
                                    <strong class="w-70 text-white float-start">{{ $record['group_total']; }}</strong>
                                    <p class="text-white w-30 float-end">{{ $record['group_amount']; }}</p>
                                </div>
                            </div>
                        </a>
                    @endif
                    @if($record['is_type']=="grand_total")
                        <a href="#" class="item bg-warning" style="width:100%; padding: 20px 10px !important">
                            <div class="detail w-100">
                                <div class="w-100">
                                    <strong class="w-70 text-white float-start">{{ $record['grand_total']; }}</strong>
                                    <p class="text-white w-30 float-end">{{ $record['grand_total_amount']; }}</p>
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach
            @else
                <a href="#" class="item" style="width:100%; padding: 20px 10px !important">
                    <strong class="text-center" style="width:100%;">No Record Found</strong>
                </a>
            @endif
        </div>
    </div>
</div>
@endsection



