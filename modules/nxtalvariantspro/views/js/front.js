/**
 * Product Variants Pro
 *
 * @author    Nxtal <support@nxtal.com>
 * @copyright Nxtal 2023
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @version   1.4.0
 * @modified  2024 - Naprawa wyświetlania obrazów po kliknięciu w miniaturę
 *
 */

var nxtalvariantspro = {
	
	init: function() {
		var $this = this;
		
		// Sprawdź czy już zainicjalizowane
		if ($('body').data('nxtalvariantspro-initialized')) {
			return;
		}
		
		$this.setElement();
		
		// Oznacz jako zainicjalizowany
		$('body').data('nxtalvariantspro-initialized', true);
		
		$('body').on('click', '.nxtal-variant .xs-toggle-view', function () {
			let el = $(this).parent().find('ul');
			if ('nowrap' == el.css('white-space')) {
				$(this).text(textMinimalView);
				el.css('white-space', 'unset');
			} else {
				$(this).text(textFullView);
				el.removeAttr('style');
			}
		});
		
		$(window).resize(function(){
			var el = $('body').find('.nxtal-variant-attributes');
		
			if (el.width() >= el.find('ul').width()) {
				el.next('.xs-toggle-view').addClass('hide');
			} else {
				el.next('.xs-toggle-view').removeClass('hide');
			}
		});
		
		$(window).trigger('resize');
		
		$(document).ajaxComplete(function(event, xhr) {
			
			setTimeout(
				function() {
					$this.setElement();
				},
				800
			);
		});
		
		// Zmiana nazwy koloru przy najechaniu (bez zmiany obrazu)
		$('body').on('mouseenter', '.nxtal-variant .nxtal-variant-attributes li', function() {
			var $hoveredItem = $(this);
			var parent = $hoveredItem.parents('.nxtal-variant');
			
			// Nie zmieniaj nic jeśli element jest aktywny (został kliknięty)
			if ($hoveredItem.hasClass('active')) {
				return;
			}
			
			// Zmień tylko label (nazwę koloru), nie zmieniaj obrazu
			var labelValue = parent.find('.nxtal-variant-label .variant-group-value');
			if ($hoveredItem.data('label')) {
				labelValue.text($hoveredItem.data('label'));
			}
		});
		
		$('body').on('mouseleave', '.nxtal-variant .nxtal-variant-attributes li', function() {
			var $leavingItem = $(this);
			var parent = $leavingItem.parents('.nxtal-variant');
			
			// Nie zmieniaj nic jeśli element jest aktywny (został kliknięty)
			if ($leavingItem.hasClass('active')) {
				return;
			}
			
			// Przywróć label z aktywnego elementu
			var labelValue = parent.find('.nxtal-variant-label .variant-group-value');
			var $activeItem = parent.find('.nxtal-variant-attributes li.active');
			
			if ($activeItem.length > 0 && $activeItem.data('label')) {
				labelValue.text($activeItem.data('label'));
			}
		});
		
		$('body').on('click', '.nxtal-variant .nxtal-variant-attributes li', function(e) {
			var parent = $(this).parents('.nxtal-variant');
			var $clickedItem = $(this);
			
			// Update active class
			parent.find('.nxtal-variant-attributes li').removeClass('active');
			$clickedItem.addClass('active');
			
			// Update label
			var labelText = $clickedItem.data('label');
			var $labelElement = parent.find('.nxtal-variant-label .variant-group-value');
			
			if (labelText && $labelElement.length > 0) {
				$labelElement.text(labelText);
			}
			
			// Update image permanently if variant has image enabled
			if (parent.data('image') == '1' && $clickedItem.data('cover')) {
				var coverImage = $('.product-cover img, #bigpic, .js-qv-product-cover');
				var newImageSrc = $clickedItem.data('cover');
				
				// Update main cover image
				coverImage.attr('src', newImageSrc);
				
				// Update picture sources if they exist
				coverImage.closest('picture').find('source').each(function() {
					var $source = $(this);
					var srcset = $source.attr('srcset');
					if (srcset) {
						// Try to update srcset with new image path
						var newSrcset = srcset.replace(/\/[^\/]+\.(jpg|jpeg|png|webp|avif)/i, function(match) {
							return newImageSrc.substring(newImageSrc.lastIndexOf('/'));
						});
						if (newSrcset !== srcset) {
							$source.attr('srcset', newSrcset);
						}
					}
				});
				
				// Update Swiper if it exists (for carousel layout)
				var $swiperContainer = $('#product-images-large');
				if ($swiperContainer.length && typeof Swiper !== 'undefined') {
					try {
						var swiperInstance = $swiperContainer[0].swiper;
						if (swiperInstance) {
							// Extract image ID from URL (usually format: /path/to/image-123.jpg)
							var imageMatch = newImageSrc.match(/-(\d+)\.(jpg|jpeg|png|webp|avif)$/i);
							var imageId = imageMatch ? imageMatch[1] : null;
							
							// Find the slide with matching image and switch to it
							$swiperContainer.find('.swiper-slide').each(function(index) {
								var $slide = $(this);
								var slideImgSrc = $slide.find('img').attr('src');
								
								if (slideImgSrc) {
									// Try to match by image ID first
									if (imageId) {
										var slideImageMatch = slideImgSrc.match(/-(\d+)\.(jpg|jpeg|png|webp|avif)$/i);
										if (slideImageMatch && slideImageMatch[1] === imageId) {
											swiperInstance.slideTo(index);
											return false; // break
										}
									}
									
									// Fallback: match by image name
									var imageName = newImageSrc.split('/').pop().split('.')[0];
									var slideImageName = slideImgSrc.split('/').pop().split('.')[0];
									if (slideImageName === imageName || slideImgSrc.indexOf(imageName) !== -1) {
										swiperInstance.slideTo(index);
										return false; // break
									}
								}
							});
						}
					} catch(e) {
						// Swiper not available or not initialized yet - silently fail
					}
				}
			}
		});
	},
	
	setElement: function() {
		$('body').find('.nxtal-variant-box .nxtal-variant').each(function(){
			var $variant = $(this);
			var $activeItem = $variant.find('.nxtal-variant-attributes li.active');
			
			// Sprawdź czy znaleziono aktywny element
			if ($activeItem.length === 0) {
				$activeItem = $variant.find('.nxtal-variant-attributes li').first();
			}
			
			// Update label
			var labelText = $activeItem.data('label');
			var $labelElement = $variant.find('.nxtal-variant-label .variant-group-value');
			
			if (labelText && $labelElement.length > 0) {
				$labelElement.text(labelText);
			}
			
			// Update main product cover image if variant has image enabled
			if ($variant.data('image') == '1') {
				// Update main product cover image if active variant has cover image
				if ($activeItem.data('cover')) {
					var coverImage = $('.product-cover img, #bigpic, .js-qv-product-cover');
					var newImageSrc = $activeItem.data('cover');
					
					// Update main cover image
					coverImage.attr('src', newImageSrc);
					
					// Update picture sources if they exist
					coverImage.closest('picture').find('source').each(function() {
						var $source = $(this);
						var srcset = $source.attr('srcset');
						if (srcset) {
							// Try to update srcset with new image path
							var newSrcset = srcset.replace(/\/[^\/]+\.(jpg|jpeg|png|webp|avif)/i, function(match) {
								return newImageSrc.substring(newImageSrc.lastIndexOf('/'));
							});
							if (newSrcset !== srcset) {
								$source.attr('srcset', newSrcset);
							}
						}
					});
					
					// Update Swiper if it exists (for carousel layout)
					var $swiperContainer = $('#product-images-large');
					if ($swiperContainer.length && typeof Swiper !== 'undefined') {
						try {
							var swiperInstance = $swiperContainer[0].swiper;
							if (swiperInstance) {
								// Extract image ID from URL (usually format: /path/to/image-123.jpg)
								var imageMatch = newImageSrc.match(/-(\d+)\.(jpg|jpeg|png|webp|avif)$/i);
								var imageId = imageMatch ? imageMatch[1] : null;
								
								// Find the slide with matching image and switch to it
								$swiperContainer.find('.swiper-slide').each(function(index) {
									var $slide = $(this);
									var slideImgSrc = $slide.find('img').attr('src');
									
									if (slideImgSrc) {
										// Try to match by image ID first
										if (imageId) {
											var slideImageMatch = slideImgSrc.match(/-(\d+)\.(jpg|jpeg|png|webp|avif)$/i);
											if (slideImageMatch && slideImageMatch[1] === imageId) {
												swiperInstance.slideTo(index);
												return false; // break
											}
										}
										
										// Fallback: match by image name
										var imageName = newImageSrc.split('/').pop().split('.')[0];
										var slideImageName = slideImgSrc.split('/').pop().split('.')[0];
										if (slideImageName === imageName || slideImgSrc.indexOf(imageName) !== -1) {
											swiperInstance.slideTo(index);
											return false; // break
										}
									}
								});
							}
						} catch(e) {
							// Swiper not available or not initialized yet - silently fail
						}
					}
				}
			}
		});
	}
}


// Inicjalizacja modułu
$(function() {
	if (typeof nxtalvariantspro !== 'undefined') {
		nxtalvariantspro.init();
	} else {
		console.error('[NxtalVariantsPro] BŁĄD: Obiekt nxtalvariantspro nie został zdefiniowany!');
	}
});

