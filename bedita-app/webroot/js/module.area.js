/*
File con la logica utilizzata nel modulo aree.

@author		giangi@qwerg.com
*/

/* ******************************
treeView Aree
****************************** */
	// Crea o refresh albero
	function designTree() {
		$("#tree").Treeview({ 
			control: "#treecontrol" ,
			speed: 'fast',
			collapsed:false
		});
		
		// Inserisce l'URL per la modifica dell'area o della sezione
		var URLFrmArea 		= "/" ;
		var URLFrmSection 	= "/" ;
		
		try {
			URLFrmArea 		= $("input[@name='URLFrmArea']").eq(0).attr('value') ;
			URLFrmSection 	= $("input[@name='URLFrmSezione']").eq(0).attr('value') ;
		} catch(e) {}
		
		$("li/span[@class='SectionItem']", "#tree").each(function(i){
			// Preleva l'ID della sezione
			var id = $("input[@name='id']", this.parentNode).eq(0).attr('value') ;
			
			// Crea il tag per il form
			$(this).html('<a href="'+URLFrmSection+id+'">'+$(this).html()+'</a>') ;
		});
		
		$("li/span[@class='AreaItem']", "#tree").each(function(i){
			var id = $("input[@name='id']", this.parentNode).eq(0).attr('value') ;
			
			$(this).html('<a href="'+URLFrmArea+id+'">'+$(this).html()+'</a>') ;
		});

	}
	
	
	// Resetta l'albero per ridisegnarlo
	function resetTree() {
		$('div', "#tree").remove();
		$('span', "#tree").removeAttr('style');
		$('ul', "#tree").removeAttr('style');

		$("li[@class='expandable lastExpandable'], li[@class='expandable']", "#tree").attr('class', 'closed');
		$("li[@class='collapsable lastCollapsable'], li[@class='last'], , li[@class='closed last']", "#tree").removeAttr('class');
				
		$('li/ul', "#tree").each(function(i){
			if(!($('li', this).size())) {
				$(this).remove() ;
			}
		});
	}

	// Aggiunge i comandi albero, nascosti
	function addCommand() {
		$("li/span[@class='SectionItem']", "#tree").before('<input type="button" name="up" value=" up " class="initCmdTree">&nbsp;<input type="button" name="down" value=" down " class="initCmdTree">&nbsp;&nbsp;');
	}
	
	// Rende visibili i comandi accessibili e disabilita gli altri
	function refreshCommand() {
		$("li", "#tree").each(function(i){
			var tmp = new Array() ; 
			
			if($(this).siblings(this).size() == 0) {
				// figlio unico
				tmp["style"] = "block" ;
				tmp["class"] = "disabledCmdTree" ;
				$('input', this).attr(tmp) ;
				return ;
				
			} 
			tmp["style"] = "block" ;
			tmp["class"] = "enabledCmdTree" ;
			$("input", this).attr(tmp) ;
			
			if($(this).prev(this).size() == 0) {
				// e' il primo
				tmp["class"] = "disabledCmdTree" ;
				$("input[@name='up']", this).attr(tmp) ;
			} else if($(this).next(this).size() == 0) {
				// e' l'ultimo
				tmp["class"] = "disabledCmdTree" ;
				$("input[@name='down']", this).attr(tmp) ;
			}			

		});
				
	}

	// Assegna i comandi ai button
	function refreshOnClick() {
		$("input[@name='up'][@class='enabledCmdTree']", "#tree").click(function(i) { 			
			$(this.parentNode).prev().before(this.parentNode);

			$("input[@name='up'][@class='enabledCmdTree']", "#tree").unbind('click') ;
			$("input[@name='down'][@class='enabledCmdTree']", "#tree").unbind('click') ;
			
			resetTree() ;
			designTree() ;
			refreshCommand() ;
			refreshOnClick();
			
			// Indica l'avvenuto cambiamento dei dati
			try { $().alertSignal() ; } catch(e) {}
		} );		

		$("input[@name='down'][@class='enabledCmdTree']", "#tree").click(function(i) { 
			$(this.parentNode).next().after(this.parentNode);
					
			$("input[@name='up'][@class='enabledCmdTree']", "#tree").unbind('click') ;
			$("input[@name='down'][@class='enabledCmdTree']", "#tree").unbind('click') ;

			resetTree() ;
			designTree() ;
			refreshCommand() ;
			refreshOnClick();
			
			// Indica l'avvenuto cambiamento dei dati
			try { $().alertSignal() ; } catch(e) {}
		} );		
	}
/*
	// evento per il submit, registra lo stato del tree
	function submitTree(tree, contest) {
		tree["children"] = {} ;
		var index = 0 ;
		
		$("li:only-child", contest).each(function(i){
			tree["children"][index] = {} ;
			
			tree["children"][index]["id"] = $("input[@name='id']", this).eq(0).val() ;
			submitTree(tree["children"][index], this) ;
			index++ ;
		});
	}	
*/
	// evento per il submit, registra lo stato del tree
	function submitTree(contest) {
		tree = new Array() ;

		$("li", contest).each(function(i){
			tree[i] = {} ;
			
			tree[i]["id"] = $("input[@name='id']", this).eq(0).val() ;
			try {
				tree[i]["parent"] = $( "../../input[@name=id]", this).eq(0).val() ;
			} catch(e) {
				tree[i]["parent"] = null ;
			}
		});
		
		tree.toString = function() {
			var str = "" ;
			for(var i=0; i < this.length ; i++) {
				str += "id="+this[i].id + " parent=" + this[i].parent ;
				if((i+1) < this.length) str += ";" ;
			}
			return str ;
		}
		
		return tree ;
	}	
	