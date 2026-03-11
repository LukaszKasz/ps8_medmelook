/*
* 2012-2022 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Google Analytycs 4.0 Pro 1.6.x and 1.7.x Module © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek <info@prestadev.pl>
* @copyright 2012-2022 Patryk Marek @ PrestaDev.pl
* @license   Do not edit, modify or copy this file, if you wish to customize it, contact us at info@prestadev.pl.
* @link      http://prestadev.pl
* @package   PD Google Analytycs 4.0 Pro 1.6.x and 1.7.x Module
* @version   1.0.2
* @date      01-05-2021
*/

$(document).ready(function() {

	let pdgoogleanalytycs4pro_quantity_wanted = 1,
		PdDelayFunction = (function () {
	    var timer = 0;
	    return function (callback, ms) {
	        clearTimeout(timer);
	        timer = setTimeout(callback, ms);
	    };
	})();

	// supercheckout send carrier and payment default selections events
	if ($("body#module-supercheckout-supercheckout").length > 0) {

		PdDelayFunction(function () {

			let id_carrier = parseInt($('input.delivery_option_radio:checked').val());
			if (typeof(id_carrier) !== 'undefined'){
				$.ajax({
					type: "POST",
					url: pdgoogleanalytycs4pro_ajax_link,
					data: {'action': 'addDeliveryInfo', 'id_carrier': id_carrier, 'secure_key': pdgoogleanalytycs4pro_secure_key, ajax: true},
					dataType: "json",
					success: function(data) {
						if (data) {
							$('#hook-display-before-carrier').append(data);
						}
					}
				});
			}

			let payment_module = $('input[name=payment_method]:checked').data('module-name');
			if (typeof(payment_module) !== 'undefined'){
				$.ajax({
					type: "POST",
					url: pdgoogleanalytycs4pro_ajax_link,
					data: {'action': 'addPaymentInfo', 'payment_module': payment_module, 'secure_key': pdgoogleanalytycs4pro_secure_key, ajax: true},
					dataType: "json",
					success: function(data) {
						if (data) {
							$('#hook-display-before-carrier').append(data);
						}
					}
				});
			}

		}, 1500);
	}

	// supercheckout
	$("body#module-supercheckout-supercheckout").on("click", "input.delivery_option_radio:checked", function() {
		let id_carrier =  parseInt(this.value);
		$.ajax({
			type: "POST",
			url: pdgoogleanalytycs4pro_ajax_link,
			data: {'action': 'addDeliveryInfo', 'id_carrier': id_carrier, 'secure_key': pdgoogleanalytycs4pro_secure_key, ajax: true},
			dataType: "json",
			success: function(data) {
				if (data) {
					$('#hook-display-before-carrier').append(data);
				}
			}
		});
	});

	// supercheckout
	$("body#module-supercheckout-supercheckout").on("click", "input[name=payment_method]:checked", function() {
		let payment_module = $(this).data('module-name');
		$.ajax({
			type: "POST",
			url: pdgoogleanalytycs4pro_ajax_link,
			data: {'action': 'addPaymentInfo', 'payment_module': payment_module, 'secure_key': pdgoogleanalytycs4pro_secure_key, ajax: true},
			dataType: "json",
			success: function(data) {
				if (data) {
					$('#hook-display-before-carrier').append(data);
				}
			}
		});
	});


	// thecheckout send carrier and payment default selections events
	if ($("body#module-thecheckout-order").length > 0) {

		PdDelayFunction(function () {

			let id_carrier = parseInt($('input[type=radio][name^=delivery_option]:checked').val());

			if (typeof(id_carrier) !== 'undefined'){
				$.ajax({
					type: "POST",
					url: pdgoogleanalytycs4pro_ajax_link,
					data: {'action': 'addDeliveryInfo', 'id_carrier': id_carrier, 'secure_key': pdgoogleanalytycs4pro_secure_key, ajax: true},
					dataType: "json",
					success: function(data) {
						if (data) {
							$('#hook-display-before-carrier').append(data);
						}
					}
				});
			}

			let payment_module = $('input[name=payment-option]:checked').data('module-name');

			if (typeof(payment_module) !== 'undefined'){
				$.ajax({
					type: "POST",
					url: pdgoogleanalytycs4pro_ajax_link,
					data: {'action': 'addPaymentInfo', 'payment_module': payment_module, 'secure_key': pdgoogleanalytycs4pro_secure_key, ajax: true},
					dataType: "json",
					success: function(data) {
						if (data) {
							$('#hook-display-before-carrier').append(data);
						}
					}
				});
			}

		}, 1000);
	}

	$("body#module-thecheckout-order").on("click", "input[type=radio][name^=delivery_option]:checked", function() {
		let id_carrier =  parseInt(this.value);
		$.ajax({
			type: "POST",
			url: pdgoogleanalytycs4pro_ajax_link,
			data: {'action': 'addDeliveryInfo', 'id_carrier': id_carrier, 'secure_key': pdgoogleanalytycs4pro_secure_key, ajax: true},
			dataType: "json",
			success: function(data) {
				if (data) {
					$('#hook-display-before-carrier').append(data);
				}
			}
		});
	});

	$("body#module-thecheckout-order").on("click", "input[name=payment-option]:checked", function() {
		let payment_module = $(this).data('module-name');
		$.ajax({
			type: "POST",
			url: pdgoogleanalytycs4pro_ajax_link,
			data: {'action': 'addPaymentInfo', 'payment_module': payment_module, 'secure_key': pdgoogleanalytycs4pro_secure_key, ajax: true},
			dataType: "json",
			success: function(data) {
				if (data) {
					$('#hook-display-before-carrier').append(data);
				}
			}
		});
	});

	$("body#checkout").on("click", '.delivery-options input[type="radio"]:checked', function() {
		let id_carrier =  parseInt(this.value);
		$.ajax({
			type: "POST",
			url: pdgoogleanalytycs4pro_ajax_link,
			data: {'action': 'addDeliveryInfo', 'id_carrier': id_carrier, 'secure_key': pdgoogleanalytycs4pro_secure_key, ajax: true},
			dataType: "json",
			success: function(data) {
				if (data) {
					$('#hook-display-before-carrier').append(data);
				}
			}
		});
	});

	$("body#checkout").on("click", 'input[name="payment-option"]:checked', function() {
		let payment_module = $(this).data('module-name');
		$.ajax({
			type: "POST",
			url: pdgoogleanalytycs4pro_ajax_link,
			data: {'action': 'addPaymentInfo', 'payment_module': payment_module, 'secure_key': pdgoogleanalytycs4pro_secure_key, ajax: true},
			dataType: "json",
			success: function(data) {
				if (data) {
					$('#hook-display-before-carrier').append(data);
				}
			}
		});
	});


	// opc steasycheckout
	$("body.module-steasycheckout-default").on("click", 'input[type=radio][name^=delivery_option]:checked', function() {
		let id_carrier =  parseInt(this.value);
		$.ajax({
			type: "POST",
			url: pdgoogleanalytycs4pro_ajax_link,
			data: {'action': 'addDeliveryInfo', 'id_carrier': id_carrier, 'secure_key': pdgoogleanalytycs4pro_secure_key, ajax: true},
			dataType: "json",
			success: function(data) {
				if (data) {
					$('#hook-display-before-carrier').append(data);
				}
			}
		});
	});

	// opc steasycheckout
	$("body.module-steasycheckout-default").on("click", 'input[name="payment-option"]:checked', function() {
		let payment_module = $(this).data('module-name');
		$.ajax({
			type: "POST",
			url: pdgoogleanalytycs4pro_ajax_link,
			data: {'action': 'addPaymentInfo', 'payment_module': payment_module, 'secure_key': pdgoogleanalytycs4pro_secure_key, ajax: true},
			dataType: "json",
			success: function(data) {
				if (data) {
					$('#hook-display-before-carrier').append(data);
				}
			}
		});
	});



	$("#payment-confirmation").on("click", "button.btn-primary", function() {
		$.ajax({
			type: "POST",
			url: pdgoogleanalytycs4pro_ajax_link,
			data: {'action': 'confirmOrder', 'secure_key':  pdgoogleanalytycs4pro_secure_key, ajax: true},
			dataType: "json",
			success: function(data) {
				if (data) {

					if ($('#hook-display-before-carrier').length) {
						$('#hook-display-before-carrier').append(data);
					} else if ($('#payment-confirmation').length) {
						$('#payment-confirmation').append(data);
					}

				}
			}
		});
	});

	if (typeof(prestashop) !== 'undefined') {

		prestashop.on('theCheckoutConfirmOrder', function(params) {
			if (typeof(params) !== 'undefined') {

				$.ajax({
					type: "POST",
					url: pdgoogleanalytycs4pro_ajax_link,
					data: {'action': 'confirmOrder', 'secure_key':  pdgoogleanalytycs4pro_secure_key, ajax: true},
					dataType: "json",
					success: function(data) {
						if (data) {
							$('#hook-display-before-carrier').append(data);
						}
					}
				});
			}
		});

		//prestashop.on('changedCheckoutStep', function(params) {
			// if (typeof(params.event.currentTarget.id) !== 'undefined') {
			// 	let step = params.event.currentTarget.id,
			// 		step_call_elm = params.event.target.id;

			// 	if (step == 'checkout-payment-step' && step_call_elm != 'conditions_to_approve[terms-and-conditions]') {

			// 		let payment_module = $('input[name="payment-option"]:checked').data('module-name');

			// 		$.ajax({
			// 			type: "POST",
			// 			url: pdgoogleanalytycs4pro_ajax_link,
			// 			data: {'action': 'addPaymentInfo', 'payment_module': payment_module, 'secure_key': pdgoogleanalytycs4pro_secure_key, ajax: true},
			// 			dataType: "json",
			// 			success: function(data) {
			// 				if (data) {
			// 					$('#hook-display-before-carrier').append(data);
			// 				}
			// 			}
			// 		});
			// 	}
			// }
		//});

		if (pd_google_analitycs_controller != 'cart') {
			prestashop.on('updateProduct', function(params) {
				if (typeof(params) !== 'undefined') {
					pdgoogleanalytycs4pro_quantity_wanted = $(params.event.currentTarget).val();
				}
			});
		}

		if (pd_google_analitycs_controller != 'cart') {
			prestashop.on('updateCart', function(params) {
				if (typeof(params) !== 'undefined' && typeof(prestashop.cart) !== 'undefined') {

					let iso_code = prestashop.currency.iso_code,
						product_id = params.reason.idProduct,
						product_id_product_attribute = params.reason.idProductAttribute;

					if (typeof(product_id) !== 'undefined' && typeof(product_id_product_attribute) !== 'undefined' && typeof(pdgoogleanalytycs4pro_quantity_wanted) !== 'undefined') {

						$.ajax({
							type: "POST",
							url: pdgoogleanalytycs4pro_ajax_link,
							data: {'action': 'updateCart', 'product_id': product_id, 'product_id_product_attribute' : product_id_product_attribute, 'secure_key':  pdgoogleanalytycs4pro_secure_key, ajax : true},
							dataType: "json",
							success: function(data) {
								console.log('Fired up event GA4: add_to_cart');
								gtag('event', 'add_to_cart', {
									currency: iso_code,
									items: [{
										item_id: data.content_ids,
										item_name: data.content_name,
										coupon: '',
										discount: data.content_discount,
										index: '1',
										item_list_name: pd_google_analitycs_controller,
										item_list_id: pd_google_analitycs_controller,
										affiliation: data.http_referer,
										item_brand: data.content_manufacturer,
										item_category: data.content_category,
										item_category2: (data.content_category2.length) ? data.content_category2 : '',
		        						item_category3: (data.content_category3.length) ? data.content_category3 : '',
		        						item_category4: (data.content_category4.length) ? data.content_category4 : '',
		        						item_category5: (data.content_category5.length) ? data.content_category5 : '',
										item_variant: data.content_variant,
										price: data.content_value,
										currency: iso_code,
										quantity: pdgoogleanalytycs4pro_quantity_wanted
									}],
									value: data.content_value
								});
							}
						});
					}
				}
			});
		}


		// qty up > thecheckout module opc
		$('body').on("click", "a.cart-line-product-quantity-up", function(e) {

			let qty_input = $(this).parent().find('input.cart-line-product-quantity'),
				updat_url = qty_input.attr('data-update-url'),
				url_params = PdParseQuery(updat_url),
				iso_code = prestashop.currency.iso_code;

			if (typeof(url_params.id_product) !== 'undefined' && typeof(url_params.id_product_attribute) !== 'undefined') {
				var product_id_product = url_params.id_product;
				var product_id_product_attribute = url_params.id_product_attribute;
			}

			if (typeof(product_id_product) !== 'undefined' && typeof(product_id_product_attribute) !== 'undefined') {
				$.ajax({
					type: "POST",
					url: pdgoogleanalytycs4pro_ajax_link,
					data: {'action': 'updateCart', 'product_id': product_id_product, 'product_id_product_attribute' : product_id_product_attribute, 'secure_key':  pdgoogleanalytycs4pro_secure_key, ajax : true},
					dataType: "json",
					success: function(data) {
						console.log('Fired up event GA4: add_to_cart');
						gtag('event', 'add_to_cart', {
							currency: iso_code,
							items: [{
								item_id: data.content_ids,
								item_name: data.content_name,
								coupon: '',
								discount: data.content_discount,
								index: '1',
								item_list_name: pd_google_analitycs_controller,
								item_list_id: pd_google_analitycs_controller,
								affiliation: data.http_referer,
								item_brand: data.content_manufacturer,
								item_category: data.content_category,
								item_category2: (data.content_category2.length) ? data.content_category2 : '',
        						item_category3: (data.content_category3.length) ? data.content_category3 : '',
        						item_category4: (data.content_category4.length) ? data.content_category4 : '',
        						item_category5: (data.content_category5.length) ? data.content_category5 : '',
								item_variant: data.content_variant,
								price: data.content_value,
								currency: iso_code,
								quantity: 1
							}],
							value: data.content_value
						});
					}
				});
			}
		});

		// qty up > thecheckout module opc user entered value
		$('body').on("keyup", "input.cart-line-product-quantity", function(e) {

 			PdDelayFunction(function () {

				let qty_input = $('input.cart-line-product-quantity'),
					qty_input_val = parseInt($('input.cart-line-product-quantity').val()),
					updat_url = qty_input.attr('data-update-url'),
					url_params = PdParseQuery(updat_url),
					iso_code = prestashop.currency.iso_code;

				if (typeof(url_params.id_product) !== 'undefined' && typeof(url_params.id_product_attribute) !== 'undefined') {
					var product_id_product = url_params.id_product;
					var product_id_product_attribute = url_params.id_product_attribute;
				}

				if (typeof(product_id_product) !== 'undefined' && typeof(product_id_product_attribute) !== 'undefined' && qty_input_val > 0) {
					$.ajax({
						type: "POST",
						url: pdgoogleanalytycs4pro_ajax_link,
						data: {'action': 'updateCart', 'product_id': product_id_product, 'product_id_product_attribute' : product_id_product_attribute, 'secure_key':  pdgoogleanalytycs4pro_secure_key, ajax : true},
						dataType: "json",
						success: function(data) {
							console.log('Fired up event GA4: add_to_cart');
							gtag('event', 'add_to_cart', {
								currency: iso_code,
								items: [{
									item_id: data.content_ids,
									item_name: data.content_name,
									coupon: '',
									discount: data.content_discount,
									index: '1',
									item_list_name: pd_google_analitycs_controller,
									item_list_id: pd_google_analitycs_controller,
									affiliation: data.http_referer,
									item_brand: data.content_manufacturer,
									item_category: data.content_category,
									item_category2: (data.content_category2.length) ? data.content_category2 : '',
	        						item_category3: (data.content_category3.length) ? data.content_category3 : '',
	        						item_category4: (data.content_category4.length) ? data.content_category4 : '',
	        						item_category5: (data.content_category5.length) ? data.content_category5 : '',
									item_variant: data.content_variant,
									price: data.content_value,
									currency: iso_code,
									quantity: qty_input_val
								}],
								value: data.content_value
							});
						}
					});
				}
			}, 800);
		});

		// down qty > thecheckout module opc
		$('body').on("click", "a.cart-line-product-quantity-down", function(e) {

			let qty_input = $(this).parent().find('input.cart-line-product-quantity'),
				updat_url = qty_input.attr('data-update-url'),
				url_params = PdParseQuery(updat_url),
				iso_code = prestashop.currency.iso_code;

			if (typeof(url_params.id_product) !== 'undefined' && typeof(url_params.id_product_attribute) !== 'undefined') {
				var product_id_product = url_params.id_product;
				var product_id_product_attribute = url_params.id_product_attribute;
			}

			if (typeof(product_id_product) !== 'undefined' && typeof(product_id_product_attribute) !== 'undefined') {
				$.ajax({
					type: "POST",
					url: pdgoogleanalytycs4pro_ajax_link,
					data: {'action': 'updateCart', 'product_id': product_id_product, 'product_id_product_attribute' : product_id_product_attribute, 'secure_key':  pdgoogleanalytycs4pro_secure_key, ajax : true},
					dataType: "json",
					success: function(data) {
						console.log('Fired up event GA4: remove_from_cart');
						gtag('event', 'remove_from_cart', {
							currency: iso_code,
							items: [{
								item_id: data.content_ids,
								item_name: data.content_name,
								coupon: '',
								discount: data.content_discount,
								index: '1',
								item_list_name: pd_google_analitycs_controller,
								item_list_id: pd_google_analitycs_controller,
								affiliation: data.http_referer,
								item_brand: data.content_manufacturer,
								item_category: data.content_category,
								item_category2: (data.content_category2.length) ? data.content_category2 : '',
        						item_category3: (data.content_category3.length) ? data.content_category3 : '',
        						item_category4: (data.content_category4.length) ? data.content_category4 : '',
        						item_category5: (data.content_category5.length) ? data.content_category5 : '',
								item_variant: data.content_variant,
								price: data.content_value,
								currency: iso_code,
								quantity: 1
							}],
							value: data.content_value
						});
					}
				});
			}
		});



		// qty up > stadard theme ps 17
		$('body').on("click", "button.bootstrap-touchspin-up", function(e) {

			let qty_input = $(this).parent().parent().find('input.js-cart-line-product-quantity'),
				updat_url = qty_input.attr('data-update-url'),
				url_params = PdParseQuery(updat_url),
				iso_code = prestashop.currency.iso_code;


			if (typeof(url_params.id_product) !== 'undefined' && typeof(url_params.id_product_attribute) !== 'undefined') {
				var product_id_product = url_params.id_product;
				var product_id_product_attribute = url_params.id_product_attribute;
			}

			if (typeof(product_id_product) !== 'undefined' && typeof(product_id_product_attribute) !== 'undefined') {
				$.ajax({
					type: "POST",
					url: pdgoogleanalytycs4pro_ajax_link,
					data: {'action': 'updateCart', 'product_id': product_id_product, 'product_id_product_attribute' : product_id_product_attribute, 'secure_key':  pdgoogleanalytycs4pro_secure_key, ajax : true},
					dataType: "json",
					success: function(data) {
						console.log('Fired up event GA4: add_to_cart');
						gtag('event', 'add_to_cart', {
							currency: iso_code,
							items: [{
								item_id: data.content_ids,
								item_name: data.content_name,
								coupon: '',
								discount: data.content_discount,
								index: '1',
								item_list_name: pd_google_analitycs_controller,
								item_list_id: pd_google_analitycs_controller,
								affiliation: data.http_referer,
								item_brand: data.content_manufacturer,
								item_category: data.content_category,
								item_category2: (data.content_category2.length) ? data.content_category2 : '',
        						item_category3: (data.content_category3.length) ? data.content_category3 : '',
        						item_category4: (data.content_category4.length) ? data.content_category4 : '',
        						item_category5: (data.content_category5.length) ? data.content_category5 : '',
								item_variant: data.content_variant,
								price: data.content_value,
								currency: iso_code,
								quantity: 1
							}],
							value: data.content_value
						});
					}
				});
			}
		});

		// qty down > stadard theme ps 17
		$('body').on("click", "button.bootstrap-touchspin-down", function(e) {

			let qty_input = $(this).parent().parent().find('input.js-cart-line-product-quantity'),
				updat_url = qty_input.attr('data-update-url'),
				url_params = PdParseQuery(updat_url),
				iso_code = prestashop.currency.iso_code;

			if (typeof(url_params.id_product) !== 'undefined' && typeof(url_params.id_product_attribute) !== 'undefined') {
				var product_id_product = url_params.id_product;
				var product_id_product_attribute = url_params.id_product_attribute;
			}

			if (typeof(product_id_product) !== 'undefined' && typeof(product_id_product_attribute) !== 'undefined') {
				$.ajax({
					type: "POST",
					url: pdgoogleanalytycs4pro_ajax_link,
					data: {'action': 'productClick', 'product_id': product_id_product, 'product_id_product_attribute' : product_id_product_attribute, 'secure_key':  pdgoogleanalytycs4pro_secure_key, ajax : true},
					dataType: "json",
					success: function(data) {
						console.log('Fired up event GA4: remove_from_cart');
						gtag('event', 'remove_from_cart', {
							currency: iso_code,
							items: [{
								item_id: data.content_ids,
								item_name: data.content_name,
								coupon: '',
								discount: data.content_discount,
								index: '1',
								item_list_name: pd_google_analitycs_controller,
								item_list_id: pd_google_analitycs_controller,
								affiliation: data.http_referer,
								item_brand: data.content_manufacturer,
								item_category: data.content_category,
								item_category2: (data.content_category2.length) ? data.content_category2 : '',
        						item_category3: (data.content_category3.length) ? data.content_category3 : '',
        						item_category4: (data.content_category4.length) ? data.content_category4 : '',
        						item_category5: (data.content_category5.length) ? data.content_category5 : '',
								item_variant: data.content_variant,
								price: data.content_value,
								currency: iso_code,
								quantity: 1
							}],
							value: data.content_value
						});
					}
				});
			}
		});


		$('body').on("click", ".js-product-miniature a.product_img_link", function(e) {
			let product_container = $(this).parents('article'),
				product_url = $(this).attr('href').trim(),
				product_id = product_container.attr('data-id-product'),
				product_id_product_attribute = product_container.attr('data-id-product-attribute');
				iso_code = prestashop.currency.iso_code;

			if (typeof(product_id) !== 'undefined' && typeof(product_id_product_attribute) !== 'undefined' && typeof(product_url) !== 'undefined') {
				e.preventDefault();
				setTimeout(function () {
					document.location = product_url;
				}, 700);

				$.ajax({
					type: "POST",
					url: pdgoogleanalytycs4pro_ajax_link,
					data: {'action': 'productClick', 'product_id': product_id, 'product_id_product_attribute' : product_id_product_attribute, 'secure_key':  pdgoogleanalytycs4pro_secure_key, ajax : true},
					dataType: "json",
					success: function(data) {
						console.log('Fired up event GA4: select_item');
						gtag('event', 'select_item', {
							currency: iso_code,
							items: [{
								item_id: data.content_ids,
								item_name: data.content_name,
								coupon: '',
								discount: data.content_discount,
								index: '1',
								item_list_name: pd_google_analitycs_controller,
								item_list_id: pd_google_analitycs_controller,
								affiliation: data.http_referer,
								item_brand: data.content_manufacturer,
								item_category: data.content_category,
								item_category2: (data.content_category2.length) ? data.content_category2 : '',
        						item_category3: (data.content_category3.length) ? data.content_category3 : '',
        						item_category4: (data.content_category4.length) ? data.content_category4 : '',
        						item_category5: (data.content_category5.length) ? data.content_category5 : '',
								item_variant: data.content_variant,
								price: data.content_value,
								currency: iso_code,
								quantity: 1
							}],
							item_list_name: pd_google_analitycs_controller,
							item_list_id: pd_google_analitycs_controller
						});
					}
				});
			}
		});

		// standard theme ps 1.7 select item
		$('body').on("click", ".js-product-miniature a.thumbnail", function(e) {


			let product_container = $(this).parents('article'),
				product_url = $(this).attr('href').trim(),
				product_id = product_container.attr('data-id-product'),
				product_id_product_attribute = product_container.attr('data-id-product-attribute');
				iso_code = prestashop.currency.iso_code;

			if (typeof(product_id) !== 'undefined' && typeof(product_id_product_attribute) !== 'undefined' && typeof(product_url) !== 'undefined') {
				e.preventDefault();

				setTimeout(function () {
					document.location = product_url;
				}, 700);

				$.ajax({
					type: "POST",
					url: pdgoogleanalytycs4pro_ajax_link,
					data: {'action': 'productClick', 'product_id': product_id, 'product_id_product_attribute' : product_id_product_attribute, 'secure_key':  pdgoogleanalytycs4pro_secure_key, ajax : true},
					dataType: "json",
					success: function(data) {
						console.log('Fired up event GA4: select_item');
						gtag('event', 'select_item', {
							currency: iso_code,
							items: [{
								item_id: data.content_ids,
								item_name: data.content_name,
								coupon: '',
								discount: data.content_discount,
								index: '1',
								item_list_name: pd_google_analitycs_controller,
								item_list_id: pd_google_analitycs_controller,
								affiliation: data.http_referer,
								item_brand: data.content_manufacturer,
								item_category: data.content_category,
								item_category2: (data.content_category2.length) ? data.content_category2 : '',
        						item_category3: (data.content_category3.length) ? data.content_category3 : '',
        						item_category4: (data.content_category4.length) ? data.content_category4 : '',
        						item_category5: (data.content_category5.length) ? data.content_category5 : '',
								item_variant: data.content_variant,
								price: data.content_value,
								currency: iso_code,
								quantity: 1
							}],
							item_list_name: pd_google_analitycs_controller,
							item_list_id: pd_google_analitycs_controller
						});
					}
				});
			}
		});



		// Transformer theme add to cart butons on product lists
		$('body').on("click", ".ajax_add_to_cart_button", function(e) {

			let article = $(this).parents('article.js-product-miniature'),
				product_qty = parseInt(article.find('input[name=pro_quantity]').val()),
				product_id = parseInt(article.attr('data-id-product')),
				product_id_product_attribute = parseInt(article.attr('data-id-product-attribute')),
				iso_code = prestashop.currency.iso_code;


			if (typeof(product_id) !== 'undefined' && typeof(product_id_product_attribute) !== 'undefined') {
				$.ajax({
					type: "POST",
					url: pdgoogleanalytycs4pro_ajax_link,
					data: {'action': 'updateCart', 'product_id': product_id, 'product_id_product_attribute' : product_id_product_attribute, 'secure_key':  pdgoogleanalytycs4pro_secure_key, ajax : true},
					dataType: "json",
					success: function(data) {
						console.log('Fired up event GA4: add_to_cart');
						gtag('event', 'add_to_cart', {
							currency: iso_code,
							items: [{
								item_id: data.content_ids,
								item_name: data.content_name,
								coupon: '',
								discount: data.content_discount,
								index: '1',
								item_list_name: pd_google_analitycs_controller,
								item_list_id: pd_google_analitycs_controller,
								affiliation: data.http_referer,
								item_brand: data.content_manufacturer,
								item_category: data.content_category,
								item_category2: (data.content_category2.length) ? data.content_category2 : '',
		        				item_category3: (data.content_category3.length) ? data.content_category3 : '',
		        				item_category4: (data.content_category4.length) ? data.content_category4 : '',
		        				item_category5: (data.content_category5.length) ? data.content_category5 : '',
								item_variant: data.content_variant,
								price: data.content_value,
								currency: iso_code,
								quantity: product_qty ? product_qty : pdgoogleanalytycs4pro_quantity_wanted
							}],
							value: data.content_value
						});
					}
				});
			}
		});


		prestashop.on('updateProduct', function(params) {


			if (typeof(params) !== 'undefined' && params.eventType == 'updatedProductCombination') {
				let iso_code = prestashop.currency.iso_code,
					product_id = parseInt(document.getElementsByName('id_product')[0].value),
					groups = [],
					select_groups = document.getElementsByClassName('form-control-select'),
					input_color_group = document.getElementsByClassName('input-color'),
					input_radio_group = document.querySelector('.input-radio:checked');

				if (typeof(select_groups) != 'undefined' && select_groups != null) {
					for (select_count = 0; select_count < select_groups.length; select_count++) {
					groups.push(select_groups[select_count].value);
					}
				}

				if (typeof(input_color_group) != 'undefined' && input_color_group != null) {
					for (color_count = 0; color_count < input_color_group.length; color_count++) {
					if (input_color_group[color_count].checked) {
						groups.push(input_color_group[color_count].value);
					}
					}
				}

				if (typeof(input_radio_group) != 'undefined' && input_radio_group != null) {
					for (radio_count = 0; radio_count < input_radio_group.length; radio_count++) {
					if (input_radio_group[radio_count].checked) {
						groups.push(input_radio_group[radio_count].value);
					}
					}
				}

				if (typeof groups !== 'undefined' && groups.length > 0 && typeof product_id !== 'undefined') {

					$.ajax({
						type: "POST",
						url: pdgoogleanalytycs4pro_ajax_link,
						data: {'action': 'updateProduct', 'product_id': product_id, 'attributes_groups': groups,'secure_key':  pdgoogleanalytycs4pro_secure_key, ajax : true},
						dataType: "json",
						success: function(data) {
							console.log('Fired up event GA4: view_item on combination change');
							PdSetEventFireUpDelay(250);
							gtag('event', 'view_item', {
							items: [{
								item_id: data.content_ids,
								item_name: data.content_name,
								coupon: "",
								discount: data.content_discount,
								affiliation: data.http_referer,
								item_list_name: pd_google_analitycs_controller,
								item_list_id: pd_google_analitycs_controller,
								item_brand: data.content_manufacturer,
								item_category: data.content_category,
								item_category2: (data.content_category2.length) ? data.content_category2 : '',
        						item_category3: (data.content_category3.length) ? data.content_category3 : '',
        						item_category4: (data.content_category4.length) ? data.content_category4 : '',
        						item_category5: (data.content_category5.length) ? data.content_category5 : '',
								item_variant: data.content_variant,
								price: data.content_value,
								currency: iso_code,
								quantity: 1
							}],
							value: data.content_value
							});
						}
					});
				}
			}
		});
	}


	function PdSetEventFireUpDelay(ms) {
		var cur_d = new Date();
		var cur_ticks = cur_d.getTime();
		var ms_passed = 0;
		while(ms_passed < ms) {
			var d = new Date();
			var ticks = d.getTime();
			ms_passed = ticks - cur_ticks;
		}
	}

	function PdParseQuery(str) {

		if(typeof str != "string" || str.length == 0) return {};
		var s = str.split("&");
		var s_length = s.length;
		var bit, query = {}, first, second;
		for(var i = 0; i < s_length; i++) {
			bit = s[i].split("=");
			first = decodeURIComponent(bit[0]);
			if(first.length == 0) continue;
			second = decodeURIComponent(bit[1]);
			if(typeof query[first] == "undefined") query[first] = second;
			else if(query[first] instanceof Array) query[first].push(second);
			else query[first] = [query[first], second];
		}
		return query;
	}
});