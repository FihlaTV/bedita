
<meta name="BEDITA.currLang" content="{$currLang}" />
<meta name="BEDITA.currLang2" content="{$currLang2}" />
<meta name="BEDITA.webroot" content="{$session->webroot}" />
<meta name="BEdita.base"  content="{$html->url('/')}" />

<script type="text/javascript">

	// global json BEDITA config
	var BEDITA = {
		'currLang': '{$currLang}',
		'currLang2': '{$currLang2}',
		'webroot': '{$session->webroot}',
		'base': '{$html->url("/")}',
		'currentModule': {if !empty($currentModule)} {$currentModule|json_encode} {else}{ name: 'home' }{/if},
		'action': '{$view->action|default:"index"}'
	};
	
</script>
