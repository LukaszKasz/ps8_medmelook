/**
 * Product Variants Pro
 *
 * @author    Nxtal <support@nxtal.com>
 * @copyright Nxtal 2023
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @version   1.4.0
 *
 */

var nxtalvariantspro = {
	
	init: function() {
		var obj = this;
		
		/* All check/uncheck */
		$('.check-uncheck').on('click', function(){
			var checkBoxes = $('[id^='+$(this).attr('id')+']');
			checkBoxes.prop("checked", !checkBoxes.prop("checked"));
		});
		
		$(".filter-panel .list-group").sortable();
		
		$('#nxtal_variant_product_form').on('change', '#type', function(){
			
			$('#nxtal_variant_product_form').find('[class*="type_element"]').addClass('hide');
			
			if ($(this).val() == 'custom') {
				$('#nxtal_variant_product_form').find('.type_element_custom').removeClass('hide');
			} else {
				if ($(this).val() == 'feature') {
					$('#nxtal_variant_product_form').find('.type_element_feature').removeClass('hide');
				}
				$('#nxtal_variant_product_form').find('.type_element').removeClass('hide');
			}
		});
		
		$('#nxtal_variant_product_form').find('#type').trigger('change');
	   
	    $('[name="search"]').bind('click focus', function(e){
			var parent = $(this).parents('.filter-panel');
		   parent.find('.ajax_list').show();
			e.stopPropagation();
	    });

	    $(document).on('click', function(e){
		   $('.ajax_list').hide();
	    });

	    $('.panel-footer').on('click', 'button', function(e){
		   $('.ajax_list').html('');
	    });

	    $('.ajax_list').on('click', '.list-group-item', function(){
			var parent = $(this).parents('.filter-panel');
			
			if (parent.data('multioption') != 0) {
				var element = $(this).detach();
				if(parent.find('.list-group [data-id="'+$(this).attr('data-id')+'"]').length == 0){
				   parent.find('.list-group').append(element.prop('outerHTML').replace('input-name', 'name'));
				}
			} else {
				parent.find('[name="id_'+ parent.data('type') +'"]').val($(this).attr('data-id'));
				parent.find('[name="search"]').val($(this).find('.text-label').text());
			}
			
			$('.ajax_list').hide();
	    });
		
	    $('.list-group').on('click', '.clear', function(){
		   $(this).closest('.list-group-item').remove();
	    });
		
		$('[name="search"]').on('keyup', function(){
			obj.searchElement($(this));
		});
		
		/* Product from */
		$('#variant-product-image').on('change', '#variant-image-input', function() {
			var fileInput = $(this)[0].files[0];
			if (fileInput) {
			  var formData = new FormData();
			  formData.append('image', fileInput);

			  $.ajax({
				url: nxtalvariantspro_module_link + '&action=uploadVariationImage&id_product=' + $('#form_id_product, #id_product').val(),
				type: 'POST',
				data: formData,
				dataType: "json",
				processData: false,
				contentType: false,
				xhr: function() {
					var xhr = new window.XMLHttpRequest();
					
					
					xhr.addEventListener("progress", function(e) {
					  if (e.lengthComputable) {
						var percent = (e.loaded / e.total) * 100;
						$("#variant-product-image .vi-upload").width(percent + "%");
					  }
					});
					
					return xhr;
				},
				success: function(response) {
				  
				  if (response.status) {
					  $('#variant-product-image label').css('background-image', 'url('+ response.image +'?'+ Math.random() +')');
				  } else {
					  $('#variant-product-image .display-response').html('<div class="alert alert-danger">'+ response.error +'</div>');
				  }
				 
				},
				beforeSend: function() {
					$("#variant-product-image .vi-progress").css('opacity', 1);
					$('#variant-product-image .display-response').html('');
				},
				complete: function() {
					$("#variant-product-image .vi-progress").css('opacity', 0);
				},
			  });
			}
		  });
		  
		  $('#variant-product-image .variant-image-box').on('click', '.delete-image', function() {
			
			if (confirm(variation_image_delete_confirm)) {

			  $.ajax({
				url: nxtalvariantspro_module_link + '&action=deleteVariationImage&id_product=' + $('#form_id_product, #id_product').val(),
				type: 'POST',
				dataType: "json",
				success: function(response) {
				  
				  if (response.status) {
					  $('#variant-product-image label').removeAttr('style');
				  } else {
					  $('#variant-product-image .display-response').html('<div class="alert alert-danger">'+ response.error +'</div>');
				  }
				 
				},
				beforeSend: function() {
					$("#variant-product-image .vi-progress").css('opacity', 1);
					$('#variant-product-image .display-response').html('');
				},
				complete: function() {
					$("#variant-product-image .vi-progress").css('opacity', 0);
				},
			  });
			}
		  });
		
	},
	searchElement: function(e) {
		var parent = e.parents('.filter-panel');
		var search_text = e.val();
			
		if(search_text){
			$.ajax({
			   url: nxtalvariantspro_module_link,
			   type: "POST",
			   cache: false,
			   dataType: "json",
			   data: {
				   ajax: true,
				   action: 'searchElement',
				   type: parent.data('type'),
				   search_text: search_text
			   },
			   success: function(data)
			   {				  
					var li = '';
					if (data.found) {
					   $.each(data.elements, function(i, item){
						   
							li += '<div data-id="'+item['id_'+ data.type]+'" class="list-group-item">';
							
							if (typeof item.image != 'undefined') {
								
							li += '<div class="col-lg-2"><img src="'+item.image+'" alt="'+item.name+'" /></div><div class="col-lg-10"><h4><span class="text-label">'+item.name+'</span></h4><em>'+idText+ ' #' +item['id_'+ data.type]+'</em></div>';
							
							} else {
								li += '<span class="text-label">' + item.name + ' #'+ item['id_'+ data.type] + '</span>';
							}
							
							li += '<span class="clear pull-right">x</span><input type="hidden" input-name="'+data.type+'s[]" value="'+item['id_'+ data.type]+'" /></div>';
					   });
					   
					   parent.find('.ajax_list').html(li).show();					  
					}
				}
			});
		} else if (parent.data('multioption') == 0) {
			parent.find('[name="id_'+ parent.data('type') +'"]').val('');
		}
	}
}


$(function(){
	nxtalvariantspro.init();
});

