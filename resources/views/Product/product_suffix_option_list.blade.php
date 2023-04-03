@if(!empty($products))
@foreach($products as $key => $Product)
<div class="product-suffix-sec">
    <label class="combo-product-name">{{$Product['product_name']}}</label>
    @foreach($Product['childProducts'] as $key => $ProductSuffix)
    <div class="row">
        <div class="col-md-12 products-suffix-details append-product-sku-html">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="product_name">{{ __('languages.Product.option_code') }}</label>
                    <input type="text" class="form-control required" id="product_suffix" name="combo_product_suffix[]" value="{{$ProductSuffix['product_suffix']}}" placeholder="{{ __('languages.Product.option_code') }}" readonly>
                    <small class="text-danger">{{ $errors->first('product_name') }}</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="product_name">{{ __('languages.Product.option_name') }}</label>
                    <input type="text" class="form-control required" id="product_suffix_name" name="combo_product_suffix_name[]" value="{{$ProductSuffix['product_suffix_name']}}" placeholder="{{ __('languages.Product.option_name') }}" readonly>
                    <small class="text-danger">{{ $errors->first('product_name') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>
    @endforeach
</div>
@endforeach
@endif