

<div class="tab"><h2>{t}Properties{/t}</h2></div>

<fieldset id="properties">			
			
<table class="bordered" style="width:100%">
		
	<tr>

		<th>{t}status{/t}:</th>
		<td colspan="4">
			{if ($object.status == 'fixed')}
			{t}This object is fixed - some data is readonly{/t}
			<input type="hidden" name="data[status]" value="fixed"/>
			{else}
			{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator="&nbsp;"}
			{/if}
		</td>

	</tr>
	
	{if isset($comments)}
	<tr>
		<th>{t}comments{/t}:</th>
		<td>
			<input type="radio" name="data[comments]" value="off"{if empty($object.comments) || $object.comments=='off'} checked{/if}/>{t}No{/t} 
			<input type="radio" name="data[comments]" value="on"{if !empty($object.comments) && $object.comments=='on'} checked{/if}/>{t}Yes{/t}
		</td>
	</tr>
	{/if}
</table>
	
</fieldset>
