{if !empty($news_latest)}
    <div class="ia-items latest-news">
        {foreach $news_latest as $entry}
            <div class="media ia-item ia-item--border-bottom">
                {if !empty($entry.pictures)}
                    <a class="pull-left" href="{$smarty.const.IA_URL}news/{$entry.id}-{$entry.alias}">
                        {ia_image file=$entry.pictures[0] width=50 title=$entry.title class='media-object'}
                    </a>
                {/if}
                <div class="media-body">
                    <h5 class="media-heading"><a href="{$smarty.const.IA_URL}news/{$entry.id}-{$entry.alias}">{$entry.title|escape}</a></h5>
                    <p>{$entry.body|strip_tags|truncate:$core.config.news_max_block:'...'}</p>
                </div>
            </div>
        {/foreach}
    </div>
{/if}