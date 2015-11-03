<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
	{preventCsrf}

	<div class="wrap-list">
		<div class="wrap-group">
			<div class="wrap-group-heading">
				<h4>{lang key='options'}</h4>
			</div>



			<div class="row">
				<label class="col col-lg-2 control-label" for="input-title">{lang key='title'}</label>
				<div class="col col-lg-4">
					<input type="text" name="title" value="{$item.title|escape:'html'}" id="input-title">
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-alias">{lang key='title_alias'}</label>
				<div class="col col-lg-4">
					<input type="text" name="alias" id="input-alias" value="{if isset($item.alias)}{$item.alias}{/if}">
					<p class="help-block text-break-word" id="title_box" style="display: none;">{lang key='page_url_will_be'}: <span id="title_url" class="text-danger">{$smarty.const.IA_URL}</span></p>
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="body">{lang key='body'}</label>
				<div class="col col-lg-8">
					{ia_wysiwyg name='body' value=$item.body}
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-image">{lang key='image'}</label>
				<div class="col col-lg-4">
					{if isset($item.image) && $item.image}
						<div class="input-group thumbnail thumbnail-single with-actions">
							<a href="{printImage imgfile=$item.image fullimage=true url=true}" rel="ia_lightbox">
								{printImage imgfile=$item.image}
							</a>

							<div class="caption">
								<a class="btn btn-small btn-danger" href="javascript:void(0);" title="{lang key='delete'}" onclick="return intelli.admin.removeFile('{$item.image}', this, 'news', 'image', '{$item.id}')"><i class=" i-remove-sign"></i></a>
							</div>
						</div>
					{/if}

					{ia_html_file name='image' id='input-image'}
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-date">{lang key='date'}</label>
				<div class="col col-lg-4">
					<div class="input-group">
						<input type="text" class="js-datepicker" name="date" id="input-date" data-date-show-time="true" data-date-format="yyyy-mm-dd H:i" value="{$item.date}">
						<span class="input-group-addon js-datepicker-toggle"><i class="i-calendar"></i></span>
					</div>
				</div>
			</div>
		</div>
		{capture name='systems' append='fieldset_before'}
			<div class="row">
				<label class="col col-lg-2 control-label" for="input-language">{lang key='language'}</label>
				<div class="col col-lg-4">
					<select name="lang" id="input-language"{if count($core.languages) == 1} disabled{/if}>
						{foreach $core.languages as $code => $language}
							<option value="{$code}"{if $item.lang == $code} selected{/if}>{$language.title}</option>
						{/foreach}
					</select>
				</div>
			</div>
		{/capture}

		{include file='fields-system.tpl' datetime=true}

	</div>


</form>
{ia_add_media files='js:_IA_URL_plugins/news/js/admin/index'}
