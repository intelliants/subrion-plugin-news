{if isset($newsline) && $newsline}
	<div class="row-fluid">
		{foreach $newsline as $entry}
			{assign var='num_columns' value=((isset($core.config.newsline_row_count)) ? $core.config.newsline_row_count : 3)}
			{assign class_names ['span12', 'span6', 'span4', 'span3']}

			<div class="{$class_names[$num_columns - 1]}">
				<div class="ia-items latest-news">
					<div class="media ia-item ia-item-bordered-bottom">
						<div class="media-body">
							{if $entry.image && $core.config.newsline_display_img}
								<a href="{$smarty.const.IA_URL}news/{$entry.id}-{$entry.alias}" class="media-object pull-right">{printImage imgfile=$entry.image title=$entry.title width='100'}</a>
							{/if}
							<h4 class="media-heading">
								<a href="{$smarty.const.IA_URL}news/{$entry.id}-{$entry.alias}">{$entry.title}</a>
							</h4>
							<p class="ia-item-date">{$entry.date|date_format:$core.config.date_format} {lang key='by'} {$entry.fullname}</p>
							<p class="ia-item-body">{$entry.body|strip_tags|truncate:$core.config.newsline_body_max:'...'} <a href="{$smarty.const.IA_URL}news/{$entry.id}-{$entry.alias}.html">{lang key='continue_reading'}</a></p>
						</div>
					</div>
				</div>
			</div>

			{if $entry@iteration % $num_columns == 0}
				</div>
				<hr>
				<div class="row-fluid">
			{/if}
		{/foreach}
	</div>
{/if}