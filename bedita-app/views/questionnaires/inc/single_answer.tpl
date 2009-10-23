<tr>
	<td style="vertical-align:top; text-align:right; padding-right:10px; padding-left:0px;">
		<input class="iteration" tabindex="5000" type="text" 
		style="text-align:center; margin-bottom:5px; margin-right:10px; width:30px; background-color:transparent; font-size:24px" name="data[QuestionAnswer][{$i}][priority]" value="{$it}" />
		<br />
		<input type="button" class="add" 	title="{t}add{/t}" 		value="+" />
		<input type="button" class="remove" title="{t}delete{/t}" 	value="-" />
		<input type="button" class="undo" 	title="{t}undo{/t}" 	value="u" style="display:none;"  />
	</td>
	<td style="padding-left:15px;">
		<textarea id="a{$answer.id|default:$it}" name="data[QuestionAnswer][{$i}][description]" class="mcea">{$answer.description|default:''}</textarea>
		
		<p class="correct" style="text-align:right; margin:2px 0px 2px 0px">
		
		<!--
		<input type="button" class="toggleMCE" rel="a{$answer.id|default:$it}" style="font-size:10px !important" value="html" />
		-->
		
		<input type="checkbox" name="data[QuestionAnswer][{$i}][correct]" value="1" {if @$answer.correct == 1} checked="checked"{/if}> &nbsp;{t}correct answer{/t}
		{bedev}
			&nbsp;&nbsp;
			<input type="button" value="rinforzo">
		{/bedev}
		</p>
	</td>
</tr>