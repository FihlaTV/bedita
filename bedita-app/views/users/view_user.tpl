{$html->script("form", false)}
{$html->script("jquery/jquery.form", false)}
{$html->script("jquery/jquery.cmxforms", false)}
{$html->script("jquery/jquery.metadata", false)}
{*
{$html->script("jquery/jquery.validate", false)}
*}
{$html->script("jquery/jquery.changealert", false)}


<script type="text/javascript">
	$(document).ready(function(){
		openAtStart("#details");
	});
</script>


{$view->element('form_common_js')}

{$view->element('modulesmenu', ['substringSearch' => false])}

{include file="inc/menuleft.tpl" method="viewUser"}

<div class="head">
	
	<h1>
		{if !empty($userdetail)}
			{t}User{/t}	“<em style="color:#FFFFFF; line-height:2em">{$userdetail.realname|default:$userdetail.userid}</em>”
		{else}
			{t}New user{/t}
		{/if}
	</h1>

</div>

{include file="inc/menucommands.tpl" method="viewUser" fixed=true}

<div class="main">
	
	{include file="inc/form_user.tpl" method="viewUser"}

</div>

{*$view->element('menuright')*}


