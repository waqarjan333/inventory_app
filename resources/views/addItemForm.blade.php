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

        @if (\Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-1 text-center" role="alert">
                {!! \Session::get('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="section-title text-center">Add New Item</div>
        <div class="transactions mt-1">
            <form method="POST" action="{{ route('add.New.Item') }}">@csrf
                <div class="form-group boxed">
                    <label class="label">Item Name</label>
                    <input type="text" class="form-control" name="item_name" value="{{ old('item_name') }}" />
                </div>
                <div class="form-group boxed">
                    <label class="label">Category</label>
                    <select class="form-control custom-select" name="category">
                        <option value="">Select Category</option>
                        @foreach (DB::table('category')->whereIn('parent_id', [0,1])->where('status', 1)->orderBy('name', 'ASC')->get() as $category)
                        <option @if(old('category')==$category->id) selected @endif value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group boxed">
                    <label class="label">Quantity On Hand</label>
                    <input type="text" class="form-control" name="quantity" value="{{ old('quantity') }}" />
                </div>
                <div class="form-group boxed">
                    <label class="label">Barcode</label>
                    <input type="text" class="form-control" name="barcode" value="{{ old('barcode') }}" />
                </div>
                <div class="form-group boxed">
                    <label class="label">Purchase Price</label>
                    <input type="text" class="form-control" name="purchase_price" value="{{ old('purchase_price') }}" />
                </div>
                <div class="form-group boxed">
                    <label class="label">Sale Price</label>
                    <input type="text" class="form-control" name="sale_price" value="{{ old('sale_price') }}" />
                </div>
                <div class="form-group boxed">
                    <label class="label">Vendor</label>
                    <select class="form-control custom-select" name="acc_vendor_id">
                        <option value="0">Select Vendor</option>
                        @foreach (DB::table('vendor')->where('vendor_status', '=', 1)->get() as $vendor)
                        <option @if(old('acc_vendor_id')==$vendor->vendor_id) selected @endif value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group basic">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection



