/**
 * Common JavaScript functions
 * 
 * (some to be moved to beditaUI.js)
 *  
 */


/*
 * General functions
 */

// Date - force two date pickers to work as a date range 
function customRange(input)
{
    return {minDate: (input.id == 'end' ? $('#start').datepicker( "getDate" ) : null), 
        	maxDate: (input.id == 'start' ? $('#end').datepicker( "getDate" )  : null)}; 
}




/*
 * Extend JQuery
 */
 
jQuery.fn.extend({
	check: function() {
		return this.each(function() { this.checked = true; });
	},
	uncheck: function() {
		return this.each(function() { this.checked = false; });
	},
	toggleCheck: function() {
		return this.each(function() { this.checked = !this.checked ; });
	},
	submitConfirm: function(params) {
		$(this).bind("click", function() {
			if(!confirm(params.message)) {
				return false ;
			}
			if (params.formId) {
				$("#" + params.formId).attr("action", params.action);
				$("#" + params.formId).submit(); 
			} else {
				$(this).parents("form").attr("action", params.action);
				$(this).parents("form").submit();
			}
		});
	},
	/*
	*	build the tree
	*	@params: params is a JSON object with values:
	*				- @id_control: div's id that contains "close all" and "expand all" anchors
	*				- @url: target base url of area/section
	*				- @urlVoid: if it's true insert javascript:void(0) in every node
	*				- @inputTypt: input type to add to the item's tree (i.e. checkbox, radio)
	*/
	designTree: function(params) {
		controlElem = (params.id_control)? "#" + params.id_control : false ;
		$(this).Treeview({
			control: controlElem,
			speed: 'fast',
			collapsed:false
		});
		
		$("li span", this).each(function(i){
			// get ID section 
			var id = $("input[@name='id']", this.parentNode).eq(0).attr('value') ;
			if (params.url) {
				// add anchor
				$(this).html('<a href="'+params.url+"id:"+id+'">'+$(this).html()+'</a>') ;
			}
			if (params.urlVoid) {
				$(this).html('<a href="javascript:void(0)">'+$(this).html()+'</a>') ;
			}
			if (params.inputType) {
				// add input
				$(this).before('<input type="' + params.inputType  + '" name="data[destination][]" id="s_'+id+'" value="'+id+'"/>&nbsp;');
				$(this).html('<label class="section" for="s_'+id+'">'+$(this).html()+"<\/label>") ;
				// if there isn't any radio button checked it checks the first
				if (params.inputType == "radio") { 
					if ($("input[name*='destination']:checked").length == 0) {
						$("input[name*='destination']:first").attr("checked", "checked"); 
					}
				}
			}
		});
	},


	/*
	*	reorder items
	*/
	reorderListItem: function ()
	{
		$(this).find(".itemBox").each(function (priority)
		{
			$(this).find ("input[@name*='[priority]']").val (priority+1)	// update priority
				.hide().fadeIn(100).fadeOut(100).fadeIn('fast');			// effects
		});
	}

});

