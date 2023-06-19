$(document).ready(function(){

	//add more product group
	$('.addMoreProduct').change(function() {	
		if(this.checked) {	
			$('.addMoreProductTbl').show();
		}else{
			$('input[name=sub_amount').val('00.00');
			$('.addMoreProductTbl').hide();
		}
	});

	//Product Popup open
	$(".add_item_btn").on('click', function(e) {
      $("#products_list_inv").fadeToggle();
      e.preventDefault();
    });

	 //Product Popup close
    $(document).on('click', ".cancel-inv", function(e) {
      $("#products_list_inv").fadeOut();
      e.preventDefault();
    });

    //product item click event
    $(document).on('click', ".product-item", function() {
    	var Id = $(this).attr('data-id');
    	  $.ajax({
                type: "GET",
                url: BASE_URL + "/addMoreProduct/" + Id,
                data: {},
                dataType: 'html',
                success: function(response) {
                	var obj = jQuery.parseJSON(response);
                   if(obj.status == 1){
			          $("#add_item").append(obj.loaded);
			          calculateTotalAmount(obj.product.product_amount);
			        }
                }
            });
    	return false;
    });

    //product closest item remove
    $(document).on("click",".deleteProductItem", function(){
    	 var getItemValue = $(this).closest('.item-row').find("input[name='product_add_amount']").val();
    	 var oldSubTotal = $('input[name=sub_amount').val();
		var newSubTotal = oldSubTotal - getItemValue;
		$('input[name=sub_amount').val(newSubTotal.toFixed(2));
		$('#subtotal').text(newSubTotal.toFixed(2));
		$(this).closest('.item-row').remove();

		/*$('input[name=total_amount').val(newSubTotal.toFixed(2));*/
	});

	//product total amount calculation
	/*$(document).on("blur","#product_amount",function(){
		var oldTotal = $('input[name=total_amount').val();
		if(oldTotal == ''){
			oldTotal = 0;
		}
		var newSubTotal = parseFloat(oldTotal) + parseFloat(this.value);
		$('input[name=total_amount').val(newSubTotal.toFixed(2));
	});*/
});

function calculateTotalAmount(amount){
	var oldSubTotal = $('input[name=sub_amount').val();
	var newSubTotal = parseFloat(oldSubTotal) + parseFloat(amount);
	$('input[name=sub_amount').val(newSubTotal.toFixed(2));
	$('#subtotal').text(newSubTotal.toFixed(2));

	/*$('input[name=total_amount').val(newSubTotal.toFixed(2));*/
}