@extends('index')
@section('title', 'Aursoft')
@section('contents')
@inject('carbon', 'Carbon\Carbon')
<div id="appCapsule">
    <div class="section mt-1">
        <div class="section-title text-center">
            Stock Report By Category
        </div>
        <div class="transactions mt-1">
            @if(count($report['records'])>0)
            @php $count = 1; @endphp
                @foreach ($report['records'] as  $record)
                    @if($record['is_type']=="category")
                        <a href="#" class="item bg-secondary" style="padding: 20px 10px !important">
                            <div class="detail" style="width: 80% !important">
                                <div><strong class="text-white">{{ $record['category_name'] }}</strong></div>
                            </div>
                        </a>
                    @endif
                    @if($record['is_type']=="entry")
                        <a href="#" class="item" style="width:100%; padding: 20px 10px !important">
                            <div class="detail" style="width: 80% !important">
                                <div>
                                    <strong>{{ $count }} - {{ $record['item_name']; }}</strong>
                                    <p>{{ $record['normal_price']; }} - {{ $record['sale_price']; }} - {{ $record['category_name']; }}</p>
                                </div>
                            </div>
                            <div class="right" style="width: 20% !important; text-align: right">
                                <div class="price text-warning">{{ $record['item_quantity']; }}</div>
                            </div>
                        </a>
                        @php $count++; @endphp
                    @endif
                    @if($record['is_type']=="cat_total")
                        <a href="#" class="item bg-primary" style="width:100%; padding: 20px 10px !important">
                            <div class="detail" style="width: 80% !important">
                                <div><strong class="text-white">{{ $record['cat_total']; }}</strong></div>
                            </div>
                            <div class="right" style="width: 20% !important; text-align: right">
                                <div class="price text-white">{{ $record['cat_qty']; }}</div>
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



