@extends('index')
@section('title', 'Aursoft')
@section('contents')
@inject('carbon', 'Carbon\Carbon')
<!-- App Capsule -->
    <div id="appCapsule" class="full-height">

        <div class="section mt-2 mb-2">


            <div class="listed-detail mt-3">
                <h3 class="text-center mt-2">Balance Sheet</h3>
                <p class="text-center">As {{ $carbon::parse(now())->format('d M Y') }}</p>
            </div>
            <ul class="listview flush transparent simple-listview no-space mt-3">
                @foreach ($report['records'] as $rep)
                    @if($rep['is_type']=="head_asset")
                        <li><strong  class="text-center" style="width: 100%;">{{ $rep['title'] }}</strong></li>
                    @endif
                    @if($rep['is_type']=="current_asset")
                        <li><strong>{{ $rep['title'] }}</strong></li>
                    @endif
                    @if($rep['is_type']=="other_asset")
                        <li><strong>{{ $rep['title'] }}</strong></li>
                    @endif
                    @if($rep['is_type']=="asset")
                        <li><strong>{{ $rep['title'] }}</strong><span>{{ $rep['amount'] }}</span></li>
                    @endif
                    @if($rep['is_type']=="total_cur_asset")
                        <li><strong>{{ $rep['title'] }}</strong><span class="text-success">{{ $rep['amount'] }}</span></li>
                    @endif
                    @if($rep['is_type']=="total_asset")
                        <li><strong>{{ $rep['title'] }}</strong><span class="text-success">{{ $rep['amount'] }}</span></li>
                        <li></li>
                    @endif
                     
                    @if($rep['is_type']=="head_leq")
                        <li><strong  class="text-center" style="width: 100%;">{{ $rep['title'] }}</strong></li>
                    @endif
                    @if($rep['is_type']=="head_l")
                        <li><strong>{{ $rep['title'] }}</strong></li>
                    @endif
                    @if($rep['is_type']=="current_l")
                        <li><strong>{{ $rep['title'] }}</strong></li>
                    @endif
                    @if($rep['is_type']=="liability")
                        <li><strong>{{ $rep['title'] }}</strong><span>{{ $rep['amount'] }}</span></li>
                    @endif
                    @if($rep['is_type']=="total_cur_l")
                        <li><strong>{{ $rep['title'] }}</strong><span>{{ $rep['amount'] }}</span></li>
                    @endif
                    @if($rep['is_type']=="total_l")
                        <li><strong>{{ $rep['title'] }}</strong><span class="text-success">{{ $rep['amount'] }}</span></li>
                        <li></li>
                    @endif
                    @if($rep['is_type']=="head_equity")
                        <li><strong  class="text-center" style="width: 100%;">{{ $rep['title'] }}</strong></li>
                    @endif
                    @if($rep['is_type']=="equities")
                        <li><strong>{{ $rep['title'] }}</strong><span>{{ $rep['amount'] }}</span></li>
                    @endif
                    @if($rep['is_type']=="total_equity")
                        <li><strong>{{ $rep['title'] }}</strong><span>{{ $rep['amount'] }}</span></li>
                    @endif
                    @if($rep['is_type']=="total_le")
                        <li><strong>{{ $rep['title'] }}</strong><span class="text-success">{{ $rep['amount'] }}</span></li>
                    @endif
                @endforeach
                    
            </ul>


        </div>

    </div>
    <!-- * App Capsule -->
@endsection



