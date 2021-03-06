/*

	jQuery Tags Input Plugin 1.2
	
	Copyright (c) 2010 XOXCO, Inc
	
	Documentation for this plugin lives here:
	http://xoxco.com/clickable/jquery-tags-input
	
	Licensed under the MIT license:
	http://www.opensource.org/licenses/mit-license.php

	ben@xoxco.com

*/

(function($) {

	var delimiter = new Array();
	var taghref = false;
	var clickHandler = function() {};
	
	jQuery.fn.addTag = function(value,options) {
		
			var options = jQuery.extend({focus:false},options);
			this.each(function() { 
				id = $(this).attr('id');
	
				var tagslist = $(this).val().split(delimiter[id]), taglink;
				if (tagslist[0] == '') { 
					tagslist = new Array();
				}
				value = jQuery.trim(value);
				if (value !='') { 
					if (taghref !== false)
						taglink = taghref.replace("{tag}", value); 
					else 
						taglink = "#";
					$('<span class="tag"><a href="'+taglink+'">'+value+'</a>&nbsp;&nbsp;<a href="#" class="delete-tag" title="Etiket Sil">x</a></span>')
						.insertBefore('#'+id+'_addTag')
						.contents(".delete-tag")
						.click(clickHandler);
					tagslist.push(value);
				
					$('#'+id+'_tag').val('');
					if (options.focus) {
						$('#'+id+'_tag').focus();
					} else {		
						$('#'+id+'_tag').blur();
					}
				}
				jQuery.fn.tagsInput.updateTagsField(this,tagslist);
		
			});		
			
			return false;
		};
		
	jQuery.fn.removeTag = function(value) { 
			
			this.each(function() { 
				id = $(this).attr('id');
	
				var old = $(this).val().split(delimiter[id]);
	
				
				$('#'+id+'_tagsinput .tag').remove();
				str = '';
				for (i=0; i< old.length; i++) { 
					if (escape(old[i])!=value) { 
						str = str + delimiter[id] +old[i];
					}
				}
				
				jQuery.fn.tagsInput.importTags(this,str);
			});
					
			return false;
	
		};
	
	
	jQuery.fn.tagsInput = function(options) { 
	
		var defaults = {
			defaultText:'add a tag',
			width:'260px', 
			hide:true, 
			delimiter:',',
			autocomplete: {
				selectFirst:false
			}, 
			taglink:false,
			onChange: function (sender) { }
		};
		var settings = jQuery.extend(defaults, options);
		taghref = settings.taglink;
	
		this.each(function() { 
			if (settings.hide) { 
				$(this).hide();				
			}
				
			id = $(this).attr('id')
			
			data = jQuery.extend({
				pid:id,
				real_input: '#'+id,
				holder: '#'+id+'_tagsinput',
				input_wrapper: '#'+id+'_addTag',
				fake_input: '#'+id+'_tag'
			},settings);
	
			delimiter[id] = data.delimiter;
			clickHandler = function(){
				tag = jQuery(this).siblings('a').text();
				settings.onChange(jQuery(data.real_input).val());
				jQuery('#'+id).removeTag(escape(tag));
				return false;
			};
	
			$('<div id="'+id+'_tagsinput" class="tagsinput text_input"><div id="'+id+'_addTag"><input id="'+id+'_tag" value="" default="'+settings.defaultText+'" /></div><div class="tags_clear"></div></div>').insertAfter(this);
	
			$(data.holder).css('width',settings.width);
	
		
			if ($(data.real_input).val()!='') { 
				jQuery.fn.tagsInput.importTags($(data.real_input),$(data.real_input).val());
			} else {
				$(data.fake_input).val($(data.fake_input).attr('default'));
				$(data.fake_input).css('color','#666666');				
			}
		
	
			$(data.holder).bind('click',data,function(event) {
				$(event.data.fake_input).focus();
			});
		
			// if user types a comma, create a new tag
			$(data.fake_input).bind('keypress',data,function(event) { 
				if (event.which==event.data.delimiter.charCodeAt(0) || event.which==13) { 
					$(event.data.real_input).addTag($(event.data.fake_input).val(),{focus:true});
					settings.onChange($(data.real_input).val());
					return false;
				}
				if (event.which == 8 && $(this).val() == "") {
					var tag = $(event.data.holder + ' .tag:last').text();
					tag = tag.substr(0, tag.length - 3);
					$(event.data.real_input).removeTag(escape(tag));
					$(this).focus();
					settings.onChange($(data.real_input).val());
					return false;
				}
			});
					
			
			$(data.fake_input).bind('focus',data,function(event) {
				if ($(event.data.fake_input).val()==$(event.data.fake_input).attr('default')) { 
					$(event.data.fake_input).val('');
				}
				$(event.data.fake_input).css('color','#000000');		
			});
					
			if (settings.autocomplete_url != undefined) { 
				$(data.fake_input).autocomplete(settings.autocomplete_url,settings.autocomplete).bind('result',data,function(event,data,formatted) { 
					if (data) {
						d = data + "";	
						$(event.data.real_input).addTag(d,{focus:true});
						settings.onChange($(data.real_input).val());
					}
				});;
				
		
				$(data.fake_input).bind('blur',data,function(event) { 
					if ($(event.data.fake_input).val() != $(event.data.fake_input).attr('default')) {
						$(event.data.real_input).addTag($(event.data.fake_input).val(),{focus:false});	
						settings.onChange($(data.real_input).val());
					}

					$(event.data.fake_input).val($(event.data.fake_input).attr('default'));
					$(event.data.fake_input).css('color','#666666');
					return false;
				});
	
		
			} else {
	
					// if a user tabs out of the field, create a new tag
					// this is only available if autocomplete is not used.
					$(data.fake_input).bind('blur',data,function(event) { 
						var d = $(this).attr('default');
						if ($(event.data.fake_input).val()!='' && $(event.data.fake_input).val()!=d) { 
							event.preventDefault();
							$(event.data.real_input).addTag($(event.data.fake_input).val(),{focus:true});
							settings.onChange($(data.real_input).val());
						} else {
							$(event.data.fake_input).val($(event.data.fake_input).attr('default'));
							$(event.data.fake_input).css('color','#666666');
						}
						return false;
					});
			
			}
			
			$(data.fake_input).blur();
		});
			
		return this;
	
	};
	
	
	jQuery.fn.tagsInput.updateTagsField = function(obj,tagslist) { 
		
			id = $(obj).attr('id');
			$(obj).val(tagslist.join(delimiter[id]));
		};
	
	jQuery.fn.tagsInput.importTags = function(obj,val) {
			
			$(obj).val('');
			id = $(obj).attr('id');
			var tags = val.split(delimiter[id]);
			for (i=0; i<tags.length; i++) { 
				$(obj).addTag(tags[i],{focus:false});
			}
		};
			
})(jQuery);
