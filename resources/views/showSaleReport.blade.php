@extends('index')
@section('title', 'Aursoft')
@section('contents')
@inject('carbon', 'Carbon\Carbon')
<div id="appCapsule">
    <div class="section mt-1">
        <div class="section-title text-center">
            Stock Report By Ware-House
        </div>
        @php $category = ""; @endphp
        <div class="transactions mt-1">
            @if(count($report['records'])>0)
                @foreach ($report['records'] as  $record)
                    @if($record['is_type']=="category_name")
                        <a href="#" class="item bg-secondary" style="padding: 5px 10px !important">
                            <div class="detail">
                                <div><strong class="text-white">{{ $record['category_name'] }}</strong></div>
                            </div>
                        </a>
                    @endif
                    @if($record['is_type']=="item_type")
                        <a href="#" class="item" style="width:100%; padding: 20px 10px !important">
                            <div class="detail w-100">
                                <div class="w-100">
                                    <strong class="w-100">{{ $record['count']; }} - {{ $record['item_name']; }}</strong>
                                    <p class="text-warning w-25 float-start text-left">{{ $record['item_quantity']; }}</p>
                                    <p class="text-warning w-25 float-start text-center">{{ $record['item_sale']; }}</p>
                                    <p class="text-warning w-25 float-start text-center">{{ $record['item_purchase']; }}</p>
                                    <p class="text-warning w-25 float-start text-center">{{ $record['item_sale']-$record['item_purchase']; }}</p>
                                </div>
                            </div>
                        </a>
                    @endif
                    @if($record['is_type']=="category_total")
                        <a href="#" class="item bg-primary" style="width:100%; padding: 20px 10px !important">
                            <div class="detail w-100">
                                <div class="w-100">
                                    <strong class="w-100 text-white">{{ $record['category_total']; }}</strong>
                                    <p class="text-white w-25 float-start text-left">{{ $record['category_qty']; }}</p>
                                    <p class="text-white w-25 float-start text-center">{{ $record['category_sale']; }}</p>
                                    <p class="text-white w-25 float-start text-center">{{ $record['category_cost']; }}</p>
                                    <p class="text-white w-25 float-start text-center">{{ $record['category_sale']-$record['category_cost']; }}</p>
                                </div>
                            </div>
                        </a>
                    @endif
                    @if($record['is_type']=="grand_total")
                        <a href="#" class="item bg-warning" style="width:100%; padding: 20px 10px !important">
                            <div class="detail w-100">
                                <div class="w-100">
                                    <strong class="w-100 text-white">{{ $record['grand_total']; }}</strong>
                                    <p class="text-white w-25 float-start text-left">{{ $record['grand_total_sale']; }}</p>
                                    <p class="text-white w-25 float-start text-center">{{ $record['grand_total_sale']; }}</p>
                                    <p class="text-white w-25 float-start text-center">{{ $record['grand_total_cost']; }}</p>
                                    <p class="text-white w-25 float-start text-center">{{ $record['grand_total_sale']-$record['grand_total_cost']; }}</p>
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



