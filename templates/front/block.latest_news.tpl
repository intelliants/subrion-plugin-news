{if !empty($news_latest)}
    <div class="ia-items latest-news">
        {foreach $news_latest as $entry}
            <div class="media ia-item ia-item--border-bottom">
                {if !empty($entry.pictures)}
                    <a class="pull-left" href="{$smarty.const.IA_URL}news/{$entry.id}-{$entry.slug}">
                        {ia_image file=$entry.pictures[0] width=50 type='thumbnail' title=$entry.title class='media-object'}
                    </a>
                {/if}
                <div class="media-body">
                    <h5 class="media-heading"><a href="{$smarty.const.IA_URL}news/{$entry.id}-{$entry.slug}">{$entry.title|escape}</a></h5>
                    {if !empty($entry.summary)}
                        <p>{$entry.summary}</p>
                    {else}
                        <p>{$entry.body|strip_tags|truncate:$core.config.news_max_block:'...'}</p>
                    {/if}
                </div>
            </div>
        {/foreach}
    </div>
{/if}