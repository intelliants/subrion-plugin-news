{if !empty($listings)}
    <div class="ia-items newsreel">
        {foreach $listings as $entry}
            <div class="media ia-item">
                {if !empty($entry.pictures)}
                    <a href="{$smarty.const.IA_URL}news/{$entry.id}-{$entry.slug}.html" class="pull-left">{ia_image file=$entry.pictures[0] type='thumbnail' width=150 title=$entry.title class='media-object'}</a>
                {/if}
                <div class="media-body">
                    <h4 class="media-heading">
                        <a href="{$smarty.const.IA_URL}news/{$entry.id}-{$entry.slug}.html">{$entry.title|escape}</a>
                    </h4>
                    <p class="text-fade-50">{lang key='posted_on'} {$entry.date_added|date_format:$core.config.date_format} {lang key='by'} {$entry.fullname|escape}</p>
                    <div class="ia-item__content">
                        {if !empty($entry.summary)}
                            {$entry.summary}
                        {else}
                            {$entry.body|strip_tags|truncate:$core.config.news_max_block:'...'}
                        {/if}
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
{else}
    <div class="alert alert-info">{lang key='no_news'}</div>
{/if}
