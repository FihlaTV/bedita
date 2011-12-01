{literal}
<script type="text/javascript">
	function delElems(elem) {
		var prev = $(elem).prev("input");
		$(elem).prev("input").remove();
		$(elem).remove();
	}

	$(document).ready(function(){
		$("#system_config").prev(".tab").BEtabstoggle();
		$("#general_config").prev(".tab").BEtabstoggle();
		$('#addLocale').click(function () {
			var v = $('#localesV').attr('value');
			if($('input[value="' + v + '"]').length == 0) {
				var key = $('#localesK').attr('value');
				var value = $('#localesV').attr('value');
				var newinput = '<input type="text" name="sys[locales][' + key + ']" value="' + value + '" readonly="readonly" /><input type="button" value="-" onclick="delElems(this)" />'
				$('#localesAdded').append(newinput);
			}
		});
		$('#addTranslationLang').click(function () {
			var label = $('#translationLangs option:selected').text();
			if($('input[value="' + label + '"]').length == 0) {
				var value = $('#translationLangs').attr('value');
				var index = $('#translationLangs').attr("selectedIndex");
				var newinput = '<input type="text" rel="' + index + '" title="' + value + '" name="cfg[langOptions][' + value + ']" value="' + label + '" readonly="readonly" /><input type="button" value="-" onclick="delElems(this)" />'
				$('#translationLangsAdded').append(newinput);
			}
		});
	});
</script>
{/literal}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="viewConfig"}

{include file="inc/menucommands.tpl" method="viewConfig" fixed=true}

<div class="mainfull">

	<form action="{$html->url('/admin/saveConfig')}" method="post" name="configForm" id="configForm">

		<div class="tab"><h2>{t}System configuration{/t}</h2></div>

		<fieldset id="system_config">

			{if !empty($bedita_sys_err)}
				<p>{$bedita_sys_err}</p>
			{else}
			<table class="" border=0 style="margin-bottom:10px">

				<tr>
					<th style="text-transform:none">{t}BEdita url{/t}:</th>
					<td>
						<input type="text" name="sys[beditaUrl]" value="{$conf->beditaUrl}" style="width: 300px;"/>
					</td>
					{if !empty($bedita_url_err)}
					<td>
						{$bedita_url_err}
					</td>
					{/if}
				</tr>
				<tr>
					<th>{t}Media root{/t}:</th>
					<td>
						<input type="text" name="sys[mediaRoot]" value="{$conf->mediaRoot}" style="width: 300px;"/>
					</td>
					{if !empty($media_root_err)}
					<td>
						{$media_root_err}
					</td>
					{/if}
				</tr>

				<tr>
					<th>{t}Media url{/t}:</th>
					<td>
						<input type="text" name="sys[mediaUrl]" value="{$conf->mediaUrl}" style="width: 300px;"/>
					</td>
					{if !empty($media_url_err)}
					<td>
						{$media_url_err}
					</td>
					{/if}
				</tr>
				
				</table>
				
				<hr />
				
				<table>
{* <!--
				<tr>
					<th>{t}Date Pattern{/t}:</th>
					<td>
						<input type="text" name="sys[datePattern]" value="{$conf->datePattern}" style="width: 300px;"/>
					</td>
					{if !empty($date_pattern_err)}
					<td>
						{$date_pattern_err}
					</td>
					{/if}
				</tr>

				<tr>
					<th>{t}Date Time Pattern{/t}:</th>
					<td>
						<input type="text" name="sys[dateTimePattern]" value="{$conf->dateTimePattern}" style="width: 300px;"/>
					</td>
					{if !empty($date_time_pattern_err)}
					<td>
						{$date_time_pattern_err}
					</td>
					{/if}
				</tr>

				<tr>
					<th>{t}Locales{/t}:</th>
					<td>
						{t}key{/t}: <input type="text" id="localesK" />
						{t}value{/t}: <input type="text" id="localesV" />
						<input type="button" value="{t}Add{/t}" id="addLocale"/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<div id="localesAdded" style="width:180px;">
						{if !empty($conf->locales)}
						{foreach $conf->locales as $langKey => $langLabel name='lof'}
						<input type="text" title="{$langLabel}" name="sys[locales][{$langKey}]" value="{$langLabel}" readonly="readonly" /><input type="button" value="-" onclick="delElems(this)" />
						{/foreach}
						{/if}
						</div>
					</td>
				</tr>
				
 -->
*}
				<tr>
					<th style="padding-top:10px; vertical-align:top"><b>{t}Smtp Options{/t}</b>:</th>
					<td>
						<table class="simpleList">
						<tr><th>{t}port{/t}:</th><td><input type="text" name="sys[smtpOptions][port]" value="{$conf->smtpOptions.port|default:''}" /></td></tr>
						<tr><th>{t}timeout{/t}:</th><td><input type="text" name="sys[smtpOptions][timeout]" value="{$conf->smtpOptions.timeout|default:''}" /></td></tr>
						<tr><th>{t}host{/t}:</th><td><input type="text" name="sys[smtpOptions][host]" value="{$conf->smtpOptions.host|default:''}" /></td></tr>
						<tr><th>{t}username{/t}:</th><td><input type="text" name="sys[smtpOptions][username]" value="{$conf->smtpOptions.username|default:''}" /></td></tr>
						<tr><th>{t}password{/t}:</th><td><input type="password" name="sys[smtpOptions][password]" /></td></tr>
						</table>
					</td>
				</tr>
				<tr>
					<th style="padding-top:10px; vertical-align:top"><b>{t}Mail support{/t}</b>:</th>
					<td>
						<table class="simpleList">
							<tr><th>{t}from{/t}:</th><td><input type="text" name="sys[mailSupport][from]" value="{$conf->mailSupport.from|default:''}" /></td></tr>
							<tr><th>{t}to{/t}:</th><td><input type="text" name="sys[mailSupport][to]" value="{$conf->mailSupport.to|default:''}" /></td></tr>
							<tr><th>{t}subject{/t}:</th><td><input type="text" name="sys[mailSupport][subject]" value="{$conf->mailSupport.subject|default:''}" /></td></tr>
						</table>
					</td>
				</tr>

			</table>
			{/if}

		</fieldset>

		<div class="tab"><h2>{t}General configuration{/t}</h2></div>

		<fieldset id="general_config">

			{if !empty($bedita_cfg_err)}
				<p>{$bedita_cfg_err}</p>
			{else}
			<table class="" border=0 style="margin-bottom:10px">

				<tr>
					<th>{t}Project name{/t}:</th>
					<td>
						<input type="text" name="cfg[projectName]" value="{$conf->projectName}" style="width: 300px;"/>
					</td>
				</tr>
				
				<tr>
					<th>{t}User Interface default language{/t}:</th>
					<td>
						<select name="cfg[Config][language]">
							{foreach $conf->langsSystem as $langKey => $langLabel}
							<option value="{$langKey}"{if $langKey == $conf->Config.language} selected{/if}>{$langLabel}</option>
							{/foreach}
						</select>
					</td>
				</tr>

				<tr>
					<th>{t}New objects default language{/t}:</th>
					<td>
						<select name="cfg[defaultLang]">
							{foreach $conf->langOptions as $langKey => $langLabel}
							<option value="{$langKey}" {if $langKey == $conf->defaultLang}selected="selected"{/if}>{$langLabel}</option>
							{/foreach}
							{foreach $langs_iso as $langKey => $langLabel}
							<option value="{$langKey}" {if $langKey == $conf->defaultLang}selected="selected"{/if}>{$langLabel}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<th>{t}Objects available languages (default:all){/t}:</th>
					<td>
						<select id="translationLangs">
							<option></option>
							{foreach $langs_iso as $langKey => $langLabel}
							<option value="{$langKey}">{$langLabel}</option>
							{/foreach}
						</select>
						<input type="button" value="{t}Add{/t}" id="addTranslationLang"/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<div id="translationLangsAdded" style="width:200px;">
						{foreach $conf->langOptions as $langKey => $langLabel name='lof'}
						<input type="text" rel="{$smarty.foreach.lof.index}" title="{$langLabel}" name="cfg[langOptions][{$langKey}]" value="{$langLabel}" readonly="readonly" /><input type="button" value="-" onclick="delElems(this)" />
						{/foreach}
						</div>
					</td>
				</tr>
			</table>
			{/if}
			
			<hr />
			
			<table>
				<tr>
					<th colspan="4"><label>{t}Notifications setup{/t}:</label></label></th>
				</tr>
				<tr>
					<td>{t}From{/t} {t}name{/t}:</td>
					<td><input type="text" name="" value="" /></td>
					<td>{t}From{/t} {t}email{/t}</td>
					<td><input type="text" name="" value="noreply@" /></td>
				</tr>
					<tr>
					<td>{t}Subject prefix{/t}:</td>
					<td><input type="text" name="" value="[BEdita]" /></td>
				</tr>
				
				
			</table>
			
		</fieldset>

	</form>

</div>