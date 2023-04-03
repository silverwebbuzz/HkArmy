<tr class="item-row">
        <td>{{ $product['product_name'] }}
            <input type="hidden" class="form-control item" placeholder="Item" type="text" name="items[]" value="{{ $product['id'] }}">
        </td>
        <td>
            {{ $product['product_sku'] }}
        </td>
        <td width="15%">
            <div class="delete-btn">
                <span class="currency_wrapper"></span>
                <input type="hidden" class="form-control item" placeholder="Item" type="text" name="product_add_amount" value="{{ $product['product_amount'] }}">
                <span class="total">{{ $product['product_amount'] }}</span>
               <a href="javascript:void(0);" class="deleteProductItem"><i class="bx bx-trash-alt"></i> </a>
            </div>
        </td>
    </tr>