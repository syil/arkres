/* this prevents dom flickering, needs to be outside of dom.ready event: */
document.documentElement.className += 'js_active';
/*end dom flickering =) */
var kullaniciCozunurluk = screen.width + "x" + screen.height;

jQuery.noConflict();

jQuery(document).ready(function(){

// -------------------------------------------------------------------------------------------
// START EDITING HERE
// -------------------------------------------------------------------------------------------
	
	//jQuery('.preloading').kriesi_image_preloader({delay:100});
	jQuery('.the_gallery').galleryDisplay();
	jQuery('.gallery_entry').kriesi_tooltip({applyTooltip: '.item_small' });
	jQuery('.gallery_entry').kriesi_tooltip({className: 'text_tooltip tooltip', applyTooltip: '.item_big', tooltipContent:'.gallery_excerpt'});
	

	// activates the lightbox page
	my_lightbox("a[rel^='prettyPhoto'], a[rel^='lightbox']",true);
	
	k_menu(); // controls the dropdown menu

	k_smoothscroll(); //smooth scrolling

	jQuery('input:text').kriesi_empty_input();	// comment form improvement
	jQuery('#logout').click(function() {		// logout process
		jQuery('#logoutform').submit();
	});
	
	jQuery('#selected_res a').click(function(){	// show resolutions list
		jQuery('.res_selection').slideToggle(250);
		smooth_scroll(jQuery('#selected_res').offset().top);
		return false;
	});
	
	jQuery('.change_res').click(function(){		// change resolution
		var new_res = jQuery(this).find(".res").text();
		changeResolution(new_res);
		
		jQuery('.res_selection').slideUp(250);
		smooth_scroll(jQuery("#top").offset().top);
		
		return false;
	});
	
	jQuery('.post-ratings .star').click(function(){
		var $this = jQuery(this),
			$parent = $this.parent(),
			yildiz = $this.index() + 1,
			g_id = $parent.attr("id").substring($parent.attr("id").indexOf("-") + 1);
		
		jQuery.post("ajax/yildiz-ver.php", { 
			"g" : g_id,
			"y" : yildiz
		}, function(data){
			if (data != "-1")
				$parent.replaceWith(data);
		});
	});
	
	pixelperfect();
	
	changeResolution(kullaniciCozunurluk);
// -------------------------------------------------------------------------------------------
// END EDITING HERE
// -------------------------------------------------------------------------------------------		
});


// -------------------------------------------------------------------------------------------
// Tooltip Plugin
// -------------------------------------------------------------------------------------------
(function($)
{
	$.fn.kriesi_tooltip = function(options) 
	{
		var defaults = 
		{
			className: 'tooltip',
			applyTooltip:'.item',
			tooltipContent:'img:last',
			offset: {left:20, top:20 },
			opacity: 1
		};
		
		var options = $.extend(defaults, options);
	
		return this.each(function()
		{
			var container = $(this),
				item = container.find(options.applyTooltip),
				viewport = $(window),
				body = $('body'),
				content = container.find(options.tooltipContent).clone(),
				tooltip = $("<div class='"+options.className+"'></div>").appendTo(body),
				border_top = viewport.scrollTop(),
				border_right = viewport.width(),
				left_pos, top_pos, pos,
				tooltipPadding = 
				{
					x: parseInt(tooltip.css('paddingLeft')) + parseInt(tooltip.css('paddingRight')) + 2,
					y: parseInt(tooltip.css('paddingTop')) + parseInt(tooltip.css('paddingBottom')) + 2
				};
				
				content.prependTo(tooltip);
				
				
						
			item.mouseover(function(e)
			{
				border_top = viewport.scrollTop();
				border_right = viewport.width();
				pos = get_cursor_position(e);
				tooltip.css({left:pos.left, top:pos.top, opacity:options.opacity, display:"none", visibility:"visible"}).stop().fadeIn(400);
			})
			.mousemove(function(e)
			{
					
				pos = get_cursor_position(e);
				tooltip.css({left:pos.left, top:pos.top});

			})
			.mouseout(function()
			{
				tooltip.stop().fadeOut();				  
			});
			
			function get_cursor_position(e)
			{
				if(border_right - (options.offset.left * 2) >= tooltip.width() + tooltipPadding.x + e.pageX){
					left_pos = e.pageX+ options.offset.left;
					} else{
					left_pos = border_right-tooltip.width()-tooltipPadding.x-(options.offset.left);
					}

				if(border_top + (options.offset.top *2)>= e.pageY - tooltip.height() - tooltipPadding.y){
					top_pos = border_top + options.offset.top;
					} else{
					top_pos = e.pageY-tooltip.height()-tooltipPadding.y-options.offset.top;
					}
				var mypos = {top: top_pos, left: left_pos};
					
				return mypos;
			}
			
		});
	}
})(jQuery);



// -------------------------------------------------------------------------------------------
// galleryDisplay
// -------------------------------------------------------------------------------------------
(function($)
{
	$.fn.galleryDisplay = function(options) 
	{
		var defaults = 
		{
			links: 'a.display',
			linkContainer: '.display_buttons',
			items:'.gallery_entry',
			transitionSpeed:1600,
			easing:'easeInOutQuart'
		};
		
		var options = $.extend(defaults, options);
	
		return this.each(function()
		{
			
			var itemContainer = $(this),
				linkContainer = $(options.linkContainer),
				links = linkContainer.find(options.links),
				items = itemContainer.find(options.items),
				animationSet = options.animationSet,
				itemSet = [];
				
			itemContainer.methods = {
			
				preloadingDone: function()
				{	
					links.bind('click',itemContainer.methods.changeStyle);
					linkContainer.slideDown();
				},
				
				changeStyle: function()
				{
					var id = this.id;
					$('.display_active').removeClass('display_active');
					this.className += ' display_active';
					
					if(!$.browser.msie || ($.browser.msie && $.browser.version < 8) || ($.browser.msie && $.browser.version >= 9))
					{
						itemContainer.animate({opacity:'0'}, function()
						{
							itemContainer.attr('id',id+"_gallery");
							if(id == 'item_large')
							{
								$('.gallery_image .item_small').css('visibility','hidden');
								$('.gallery_image .item_big').css('visibility','visible');
							}
							else
							{
								$('.gallery_image .item_small').css('visibility','visible');
								$('.gallery_image .item_big').css('visibility','hidden');
							}
						});
						
					}
					else
					{
						itemContainer.attr('id',id+"_gallery");
					}
					
										
					$.cookie('gallery_display', id+"_gallery", { expires: 7});
					itemContainer.animate({opacity:'1'});					
					return false;
				}
			
			}
			
			itemContainer.attr('id',$.cookie('gallery_display'));
			itemContainer.kriesi_image_preloader({delay:100, callback:itemContainer.methods.preloadingDone});
			
		});
	}
})(jQuery);		


// -------------------------------------------------------------------------------------------
// input field improvements
// -------------------------------------------------------------------------------------------

(function($)
{
	$.fn.kriesi_empty_input = function(options) 
	{
		return this.each(function()
		{
			var currentField = $(this);
			currentField.methods = 
			{
				startingValue:  currentField.val(),
				
				resetValue: function()
				{	
					var currentValue = currentField.val();
					if(currentField.methods.startingValue == currentValue) currentField.val('');
				},
				
				restoreValue: function()
				{	
					var currentValue = currentField.val();
					if(currentValue == '') currentField.val(currentField.methods.startingValue);
				}
			};
			
			currentField.bind('focus',currentField.methods.resetValue);
			currentField.bind('blur',currentField.methods.restoreValue);
		});
	}
})(jQuery);	


	
// -------------------------------------------------------------------------------------------
// The Image preloader
// -------------------------------------------------------------------------------------------


(function($)
{
	$.fn.kriesi_image_preloader = function(options) 
	{
		var defaults = 
		{
			repeatedCheck: 500,
			fadeInSpeed: 1000,
			delay:600,
			callback: ''
		};
		
		var options = $.extend(defaults, options);
		
		return this.each(function()
		{	
			var imageContainer = jQuery(this),
				images = imageContainer.find('img').not('.no_preload').css({opacity:0, visibility:'hidden'}),
				imagesToLoad = images.length;				
				
				imageContainer.operations =
				{	
					preload: function()
					{	
						var stopPreloading = true;
												
						images.each(function(i, event)
						{	
							var image = $(this);							
							
							if(event.complete == true)
							{	
								if($.browser.opera) imagesToLoad --;
								imageContainer.operations.showImage(image);
							}
							else
							{	
								if($.browser.opera) imagesToLoad --;
								image.bind('error load',{currentImage: image}, imageContainer.operations.showImage);
							}
							
						});
						
						return this;
					},
					
					showImage: function(image)
					{	
						if(!$.browser.opera) imagesToLoad --;
						if(image.data.currentImage != undefined) { image = image.data.currentImage;}
													
						if (options.delay <= 0) image.css('visibility','visible').animate({opacity:1}, options.fadeInSpeed);
											 
						if(imagesToLoad == 0)
						{
							if(options.delay > 0)
							{
								images.each(function(i, event)
								{	
									var image = $(this);
									setTimeout(function()
									{	
										image.css('visibility','visible').animate({opacity:1}, options.fadeInSpeed, function()
										{
											$(this).parent().removeClass('preloading');
										});
									},
									options.delay*(i+1));
								});
								
								if(options.callback != '')
								{
									setTimeout(options.callback, options.delay*images.length);
								}
							}
							else if(options.callback != '')
							{
								(options.callback)();
							}
							
						}
						
					}

				};
				
				imageContainer.operations.preload();
		});
		
	}
})(jQuery);



// -------------------------------------------------------------------------------------------
// The BLOCK && FADE Slider
// -------------------------------------------------------------------------------------------

(function($)
{
	$.fn.kriesi_block_slider= function(options) 
	{
		var defaults = 
		{
			slides: '>div',				// wich element inside the container should serve as slide
			animationSpeed: 900,		// animation duration
			autorotation: true,			// autorotation true or false?
			autorotationSpeed:3,		// duration between autorotation switch in Seconds
			appendControlls: '',		// element to apply controlls to
			slideControlls: 'none',		// controlls, yes or no?
			blockSize: {height: 'full', width:'full'},
			betweenBlockDelay:15,
			display: 'topleft',
			switchMovement: false,
			showText: true,	
			transition: 'slide',		//slide or fade	
			backgroundOpacity:0.8,		// opacity for background
			transitionOrder: new Array('diagonaltop', 'diagonalbottom','topleft', 'bottomright', 'random'),
			onChange: function(current, next) {
				
			}
		};
		
		var options = $.extend(defaults, options);
		
		return this.each(function()
		{
			var slideWrapper 	= $(this),								//wrapper element
				slides			= slideWrapper.find(options.slides),	//single slide container
				slideImages		= slides.find('>a>img'),				//slide image within container
				slideCount 	= slides.length,							//number of slides
				slideWidth = slides.width(),							//width of slidecontainer
				slideHeight= slides.height(),							//height of slidecontainer
				blockNumber = 0,										//how many blocks do we need
				currentSlideNumber = 0,									//which slide is currently shown
				reverseSwitch = false,									//var to set the starting point of the transition
				currentTransition = 0,									//var to set which transition to display when rotating with 'all'
				current_class = 'active_item',							//currently active controller item
				controlls = '',											//string that will contain controll items to append
				skipSwitch = true,										//var to check if performing transition is allowed
				interval ='',
				blockOrder = new Array();										
			
			if (options.blockSize.height == 'full') options.blockSize.height = slideHeight;
			if (options.blockSize.width == 'full') options.blockSize.width = slideWidth;
			
			if(options.showText)
			slides.find('.feature_excerpt').css({display:'block', 'opacity':options.backgroundOpacity});
				
			slideWrapper.methods = {
			
				init: function()
				{	
					var posX = 0,
						posY = 0,
						generateBlocks = true,
						bgOffset = '',
						blockSelectionJQ ='',
						blockSelection ='';
						
						
					while(generateBlocks)
					{
						blockNumber ++;
						bgOffset = "-"+posX +"px -"+posY+"px";
						
						$('<div class="kBlock"></div>').appendTo(slideWrapper).css({	
								zIndex:20, 
								position:'absolute',
								display:'none',
								left:posX,
								top:posY,
								height:options.blockSize.height,
								width:options.blockSize.width,
								backgroundPosition:bgOffset
							});
				
						
						posX += options.blockSize.width;
						
						if(posX >= slideWidth)
						{
							posX = 0;
							posY += options.blockSize.height;
						}
						
						if(posY >= slideHeight)
						{	
							//end adding Blocks
							generateBlocks = false;
						}
					}
					
					//setup directions
					blockSelection = slideWrapper.find('.kBlock');
					blockOrder['topleft'] = blockSelection;
					blockOrder['bottomright'] = $(blockSelection.get().reverse());
					blockOrder['diagonaltop'] = slideWrapper.methods.kcubit(blockSelection);
					blockOrder['diagonalbottom'] = slideWrapper.methods.kcubit(blockOrder['bottomright']);
					blockOrder['random'] = slideWrapper.methods.fyrandomize(blockSelection);
					
					
					//save image in case of flash replacements
					slides.each(function()
					{
						$.data(this, "data", { img: $(this).find('img').attr('src')});
					});
			
					if(slideCount <= 1)
						{
							slideWrapper.kriesi_image_preloader({delay:200});
						}
						else
						{
							slideWrapper.kriesi_image_preloader({callback:slideWrapper.methods.preloadingDone, delay:200});
							slideWrapper.methods.appendControlls();
						}	
				},
				
				appendControlls: function()
				{
					if (options.slideControlls == 'items')
					{
						controlls = $('<div></div>').addClass('slidecontrolls').css({position:'absolute'}).appendTo(options.appendControlls);
						
						slides.each(function(i)
						{	
							var controller = $('<a href="#" class="ie6fix '+current_class+'"></a>').appendTo(controlls);
							controller.bind('click', {currentSlideNumber: i}, slideWrapper.methods.switchSlide);
							current_class = "";
						});	
					}
				},
				
				preloadingDone: function()
				{	
					skipSwitch = false;
					
					slides.css({'backgroundColor':'transparent','backgroundImage':'none'});
					
					if(options.autorotation && !$.browser.opera) 
					{
					slideWrapper.methods.autorotate();
					slideImages.bind("click", function(){ clearInterval(interval); });
					}
				},
				
				autorotate: function()
				{	
					interval = setInterval(function()
					{ 	
						currentSlideNumber ++;
						if(currentSlideNumber == slideCount) currentSlideNumber = 0;
						
						slideWrapper.methods.switchSlide();
					},
					(parseInt(options.autorotationSpeed) * 1000) + (options.betweenBlockDelay * blockNumber) + options.animationSpeed);
				},
				
				switchSlide: function(passed)
				{
					var noAction = false;
						
					if(passed != undefined && !skipSwitch)
					{	
						if(currentSlideNumber != passed.data.currentSlideNumber)
						{	
							currentSlideNumber = passed.data.currentSlideNumber;
						}
						else
						{
							noAction = true;
						}
					}
						
					if(passed != undefined) clearInterval(interval);
					
					if(!skipSwitch && noAction == false)
					{	
						skipSwitch = true;
						var currentSlide = slides.filter(':visible'),
							nextSlide = slides.filter(':eq('+currentSlideNumber+')'),
							nextURL = $.data(nextSlide[0], "data").img,	
							nextImageBG = 'url('+nextURL+')';	
							if(options.appendControlls)
							{	
								controlls.find('.active_item').removeClass('active_item');
								controlls.find('a:eq('+currentSlideNumber+')').addClass('active_item');									
							}

						blockSelectionJQ = blockOrder[options.display];
						
						//workarround to make more than one flash movies with the same classname possible
						slides.find('>a>img').css({opacity:1,visibility:'visible'});
							
						//switchmovement
						if(options.switchMovement && (options.display == "topleft" || options.display == "diagonaltop"))
						{
								if(reverseSwitch == false)
								{	
									blockSelectionJQ = blockOrder[options.display];
									reverseSwitch = true;							
								}
								else
								{	
									if(options.display == "topleft") blockSelectionJQ = blockOrder['bottomright'];
									if(options.display == "diagonaltop") blockSelectionJQ = blockOrder['diagonalbottom'];
									reverseSwitch = false;							
								}
						}	
						
						if(options.display == 'random')
						{
							blockSelectionJQ = slideWrapper.methods.fyrandomize(blockSelection);
						}

						if(options.display == 'all')
						{
							blockSelectionJQ = blockOrder[options.transitionOrder[currentTransition]];
							currentTransition ++;
							if(currentTransition >=  options.transitionOrder.length) currentTransition = 0;
						}
						

						//fire transition
						blockSelectionJQ.css({backgroundImage: nextImageBG}).each(function(i)
						{	
							
							var currentBlock = $(this);
							setTimeout(function()
							{	
								var transitionObject = new Array();
								if(options.transition == 'slide')
								{
									transitionObject['css'] = {height:1, width:1, display:'block',opacity:0};
									transitionObject['anim'] = {height:options.blockSize.height,width:options.blockSize.width,opacity:1};
								}
								else
								{
									transitionObject['css'] = {display:'block',opacity:0};
									transitionObject['anim'] = {opacity:1};
								}
							
								currentBlock
								.css(transitionObject['css'])
								.animate(transitionObject['anim'],options.animationSpeed, function()
								{ 
									if(i+1 == blockNumber)
									{	
										slideWrapper.methods.changeImage(currentSlide, nextSlide);
									}
								});
							}, i*options.betweenBlockDelay);
						});
						
					} // end if(!skipSwitch && noAction == false)
					
					return false;
				},
				
				changeImage: function(currentSlide, nextSlide)
				{	
					currentSlide.css({zIndex:0, display:'none'});
					nextSlide.css({zIndex:3, display:'block'});
					blockSelectionJQ.fadeOut(options.animationSpeed*1/3, function(){ skipSwitch = false; });
					options.onChange(currentSlide, nextSlide);
				},
				
				// array sorting
				fyrandomize: function(object) 
				{	
					var length = object.length,
						objectSorted = $(object);
						
					if ( length == 0 ) return false;
					
					while ( --length ) 
					{
						var newObject = Math.floor( Math.random() * ( length + 1 ) ),
							temp1 = objectSorted[length],
							temp2 = objectSorted[newObject];
						objectSorted[length] = temp2;
						objectSorted[newObject] = temp1;
					}
					return objectSorted;
				},
				
				kcubit: function(object)
				{
					var length = object.length, 
						objectSorted = $(object),	
						currentIndex = 0;		//index of the object that should get the object in "i" applied
						rows = Math.ceil(slideHeight / options.blockSize.height),
						columns = Math.ceil(slideWidth / options.blockSize.width),
						oneColumn = blockNumber/columns,
						oneRow = blockNumber/rows,
						modX = 0,
						modY = 0,
						rowend = 0,
						endreached = false,
						onlyOne = false; 
					
					if ( length == 0 ) return false;
					for (i = 0; i<length; i++ ) 
					{
						objectSorted[i] = object[currentIndex];
						
						if((currentIndex % oneRow == 0 && blockNumber - i > oneRow)|| (modY + 1) % oneColumn == 0)
						{						
							currentIndex -= (((oneRow - 1) * modY) - 1); modY = 0; modX ++; onlyOne = false;
							
							if (rowend > 0)
							{
								modY = rowend; currentIndex += (oneRow -1) * modY;
							}
						}
						else
						{
							currentIndex += oneRow -1; modY ++;
						}
						
						if((modX % (oneRow-1) == 0 && modX != 0 && rowend == 0) || (endreached == true && onlyOne == false) )
						{	
							modX = 0.1; rowend ++; endreached = true; onlyOne = true;
						}	
					}
					
				return objectSorted;						
				}
			}	
			
			slideWrapper.methods.init();	
		});
	}
})(jQuery);


function k_smoothscroll()
{
	jQuery('a[href*=#]:not([href*=download])').click(function() {
		
	   var newHash=this.hash;
	   
	   if(newHash != '' && newHash != '#' )
	   {
		   var target=jQuery(this.hash).offset().top,
			   oldLocation=window.location.href.replace(window.location.hash, ''),
			   newLocation=this;
			
			
		   // make sure it's the same location      
		   if(oldLocation+newHash==newLocation)
		   {
		      // animate to target and set the hash to the window.location after the animation
		      smooth_scroll(target);
		
		      // cancel default click action
		      return false;
		   }
		
		}
	
	});
}

function smooth_scroll(target)
{
	var duration=800,
		easing='easeOutQuint';
	
	jQuery('html:not(:animated),body:not(:animated)').animate({ scrollTop: target }, duration, easing, function() {

		// add new hash to the browser location
		// window.location.href=newLocation;
	});
}

function k_menu()
{
	// k_menu controlls the dropdown menus and improves them with javascript
	
	jQuery("#nav a").removeAttr('title');
	jQuery(" #nav ul ").css({display: "none"}); // Opera Fix

	
	//smooth drop downs
	jQuery("#nav li").each(function()
	{	
		
		var $sublist = jQuery(this).find('ul:first');
		
		jQuery(this).hover(function()
		{	
			$sublist.stop().css({overflow:"hidden", height:"auto", display:"none"}).slideDown(400, function()
			{
				jQuery(this).css({overflow:"visible", height:"auto"});
			});	
		},
		function()
		{	
			$sublist.stop().slideUp(400, function()
			{	
				jQuery(this).css({overflow:"hidden", display:"none"});
			});
		});	
	});
}



//equalHeights by james padolsey
jQuery.fn.equalHeights = function() {
    return this.height(Math.max.apply(null,
        this.map(function() {
           return jQuery(this).height()
        }).get()
    ));
};




function my_lightbox($elements, autolink)
{	

	var theme_selected = 'facebook';
	
	if(autolink)
	{
		jQuery('a[href$=jpg], a[href$=png], a[href$=gif], a[href$=jpeg], a[href$=".mov"] , a[href$=".swf"] , a[href*="vimeo.com"] , a[href*="youtube.com"]').contents("img").parent().each(function()
		{
			if(!jQuery(this).attr('rel') != undefined && !jQuery(this).attr('rel') != '' && !jQuery(this).hasClass('noLightbox') && jQuery(this).parents('.gallery_inner').length == 0)
			{
				jQuery(this).attr('rel','lightbox');
			}
		});
	} 
	
	
	jQuery($elements).fancybox({
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'fade'
		//'titleFormat'	: function(title, currentArray, currentIndex, currentOpts) {
		//						return '<span id="fancybox-title-over">Resim ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; '+ title : '') + '</span>';
	});
	
	//jQuery($elements).prettyPhoto({
	//		"theme": theme_selected /* light_rounded / dark_rounded / light_square / dark_square 																});
	
	jQuery($elements).each(function()
	{	
		var $image = jQuery(this).contents("img");
		$newclass = 'lightbox_image';
		
		if(jQuery(this).attr('href').match(/(jpg|gif|jpeg|png|tif)/)) 
			$newclass = 'lightbox_image';
		if(jQuery(this).attr('href').match(/(#)/))
			$newclass = 'download_image';
			
		if ($image.length > 0)
		{	
			if(jQuery.browser.msie &&  jQuery.browser.version < 7) jQuery(this).addClass('ie6_lightbox');
			
			var $bg = jQuery("<span class='"+$newclass+" '></span>").appendTo(jQuery(this));
			
			jQuery(this).bind('mouseenter', function()
			{
				var $height = $image.height(),
					$width = $image.width(),
					$pos =  $image.position(),
					$paddingX = parseInt($image.css('paddingTop')) + parseInt($image.css('paddingBottom')),
					$paddingY = parseInt($image.css('paddingLeft')) + parseInt($image.css('paddingRight'));					
				
				jQuery(this).removeClass('preloading');
				$bg.css({height:$height + $paddingY, width:$width + $paddingX, top:$pos.top, left:$pos.left});
			});
		}
	});	
	
	jQuery($elements).contents("img").hover(function()
	{
		jQuery(this).stop().animate({opacity:0.4},400);
	},
	function()
	{
		jQuery(this).stop().animate({opacity:1},400);
	});
}


(function($)
{
	$.fn.kriesi_ajax_form = function(options) 
	{
		var defaults = 
		{
			sendPath: 'send.php',
			responseContainer: '.ajax_response',
			onComplete : function(response) { return true; },
			onSubmit : function() { }
		};
		
		var options = $.extend(defaults, options);
		
		return this.each(function()
		{
			var form = $(this),
				send = 
				{
					formElements: form.find('textarea, select, :text, :password, :checked, input[type=hidden]'),
					validationError:false,
					button : form.find('input:submit'),
					datastring : ''
				};
			
			send.button.bind('click', checkElements);
			
			function send_ajax_form()
			{
				options.onSubmit();
				
				var message = $(options.responseContainer);
				send.button.fadeOut(300);	
				message.slideUp(400);
									
				$.ajax({
					type: "POST",
					url: options.sendPath,
					data:send.datastring,
					success: function(response)
					{	
						if (options.onComplete(response)) {
							message.html(response).slideDown(400);
							send.button.fadeIn(300);
						}
					}
				});
				
			}
			
			function checkElements()
			{	
				// reset validation var and send data
				send.formElements = form.find('textarea, select, :text, :password, :checked, input[type=hidden]');
				send.validationError = false;
				send.datastring = 'ajax=true&' + send.formElements.serialize();
				
				send.formElements.each(function(i)
				{
					var currentElement = $(this),
						surroundingElement = currentElement.parent(),
						value = currentElement.val(),
						name = currentElement.attr('name'),
					 	classes = currentElement.attr('class'),
					 	nomatch = true;
					 	
					 	//send.datastring  += "&" + name + "=" + value;
					 	
					 	if(classes.match(/is_empty/))
						{
							if(value == '')
							{
								surroundingElement.removeClass("valid").addClass("error");
								send.validationError = true;
							}
							else
							{
								surroundingElement.removeClass("error").addClass("valid");
							}
							nomatch = false;
						}
						
						if(classes.match(/is_email/))
						{
							if(!value.match(/^\w[\w|\.|\-]+@\w[\w|\.|\-]+\.[a-zA-Z]{2,4}$/))
							{
								surroundingElement.removeClass("valid").addClass("error");
								send.validationError = true;
							}
							else
							{
								surroundingElement.removeClass("error").addClass("valid");
							}	
							nomatch = false;
						}
						
						if(nomatch && value != '')
						{
							surroundingElement.removeClass("error").addClass("valid");
						}
				});
				
				if(send.validationError == false)
				{
					send_ajax_form();
				}
				return false;
			}
		});
	}
})(jQuery);

/*
 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
*/

// t: current time, b: begInnIng value, c: change In value, d: duration
jQuery.easing['jswing'] = jQuery.easing['swing'];

jQuery.extend( jQuery.easing,
{
	def: 'easeOutQuad',
	swing: function (x, t, b, c, d) {
		//alert(jQuery.easing.default);
		return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
	},
	easeInQuad: function (x, t, b, c, d) {
		return c*(t/=d)*t + b;
	},
	easeOutQuad: function (x, t, b, c, d) {
		return -c *(t/=d)*(t-2) + b;
	},
	easeInOutQuad: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t + b;
		return -c/2 * ((--t)*(t-2) - 1) + b;
	},
	easeInCubic: function (x, t, b, c, d) {
		return c*(t/=d)*t*t + b;
	},
	easeOutCubic: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t + 1) + b;
	},
	easeInOutCubic: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t + b;
		return c/2*((t-=2)*t*t + 2) + b;
	},
	easeInQuart: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t + b;
	},
	easeOutQuart: function (x, t, b, c, d) {
		return -c * ((t=t/d-1)*t*t*t - 1) + b;
	},
	easeInOutQuart: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
		return -c/2 * ((t-=2)*t*t*t - 2) + b;
	},
	easeInQuint: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t*t + b;
	},
	easeOutQuint: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t*t*t + 1) + b;
	},
	easeInOutQuint: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
		return c/2*((t-=2)*t*t*t*t + 2) + b;
	},
	easeInSine: function (x, t, b, c, d) {
		return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
	},
	easeOutSine: function (x, t, b, c, d) {
		return c * Math.sin(t/d * (Math.PI/2)) + b;
	},
	easeInOutSine: function (x, t, b, c, d) {
		return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
	},
	easeInExpo: function (x, t, b, c, d) {
		return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
	},
	easeOutExpo: function (x, t, b, c, d) {
		return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
	},
	easeInOutExpo: function (x, t, b, c, d) {
		if (t==0) return b;
		if (t==d) return b+c;
		if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
		return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	},
	easeInCirc: function (x, t, b, c, d) {
		return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
	},
	easeOutCirc: function (x, t, b, c, d) {
		return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
	},
	easeInOutCirc: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
		return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
	},
	easeInElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
	},
	easeOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
	},
	easeInOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
	},
	easeInBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*(t/=d)*t*((s+1)*t - s) + b;
	},
	easeOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
	},
	easeInOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158; 
		if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
		return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
	},
	easeInBounce: function (x, t, b, c, d) {
		return c - jQuery.easing.easeOutBounce (x, d-t, 0, c, d) + b;
	},
	easeOutBounce: function (x, t, b, c, d) {
		if ((t/=d) < (1/2.75)) {
			return c*(7.5625*t*t) + b;
		} else if (t < (2/2.75)) {
			return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
		} else if (t < (2.5/2.75)) {
			return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
		} else {
			return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
		}
	},
	easeInOutBounce: function (x, t, b, c, d) {
		if (t < d/2) return jQuery.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
		return jQuery.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
	}
});


/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

/**
 * Create a cookie with the given name and value and other optional parameters.
 *
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Set the value of a cookie.
 * @example $.cookie('the_cookie', 'the_value', { expires: 7, path: '/', domain: 'jquery.com', secure: true });
 * @desc Create a cookie with all available options.
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Create a session cookie.
 * @example $.cookie('the_cookie', null);
 * @desc Delete a cookie by passing null as value. Keep in mind that you have to use the same path and domain
 *       used when the cookie was set.
 *
 * @param String name The name of the cookie.
 * @param String value The value of the cookie.
 * @param Object options An object literal containing key/value pairs to provide optional cookie attributes.
 * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
 *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
 *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
 *                             when the the browser exits.
 * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
 * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
 * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
 *                        require a secure protocol (like HTTPS).
 * @type undefined
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Get the value of a cookie with the given name.
 *
 * @example $.cookie('the_cookie');
 * @desc Get the value of a cookie.
 *
 * @param String name The name of the cookie.
 * @return The value of the cookie.
 * @type String
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

function pixelperfect()
{
	if (jQuery.browser.opera)
	{
		jQuery('input').css({"border-radius":0});
	}

}

function changeResolution(new_res) {
	var selected_res = jQuery("#selected_res span");
	
	jQuery('.download_link').each(function(){
		jQuery(this).attr("href", jQuery(this).attr("href").replace(selected_res.text(), new_res));
	});
	
	selected_res.text(new_res);
	jQuery.cookie(cozunurluk_cerezi, new_res, { expires: 7 });
	
}