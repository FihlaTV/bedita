
<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){
	$("#listObjToAssoc a").click(function(){
		var idObjAssoc = $(this).siblings("input[@name='idObjAssoc']").val();
		var relation = $(this).siblings("input[@name='relAssoc']").val();
		uploadItemById(idObjAssoc, relation);		
	});
});
{/literal}
//-->
</script>

<ul id="listObjToAssoc">
{foreach from=$objectsToAssoc.items item="objToAss"}
	<li>
		<a id="test" href="javascript:void(0)" title="{t}add{/t}">{$objToAss.title}</a> ({t}{$objToAss.relation}{/t})
		<input type="hidden" name="relAssoc" value="{$objToAss.relation}"/>
		<input type="hidden" name="idObjAssoc" value="{$objToAss.id}"/>
	</li>
{/foreach}
</ul>