{ia_add_media files='css: _IA_URL_modules/news/templates/front/css/style'}

{if !empty($entry)}
    <div class="media ia-item news-entry">
        <p class="text-fade-50">{lang key='posted_on'} {$entry.date_added|date_format:$core.config.date_format} {lang key='by'} {$entry.fullname}</p>

        {if !empty($entry.pictures)}
            <div class="ia-item__image">{ia_image file=$entry.pictures[0] type='large' title=$entry.title}</div>
        {/if}

        <div class="ia-item__content">{$entry.body}</div>

        <hr>
        <!-- AddThis Button BEGIN -->
        <div class="addthis_toolbox addthis_default_style">
            <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
            <a class="addthis_button_tweet"></a>
            <a class="addthis_button_pinterest_pinit"></a>
            <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
            <a class="addthis_counter addthis_pill_style"></a>
        </div>
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-5170da8b1f667e6d"></script>
        <!-- AddThis Button END -->
    </div>
{else}
    {if $news}
        <div class="ia-items newsreel">
            {foreach $news as $entry}
                <div class="media ia-item">
                    {if !empty($entry.pictures)}
                        <a href="{$smarty.const.IA_URL}news/{$entry.id}-{$entry.alias}" class="pull-left">{ia_image file=$entry.pictures[0] width=150 title=$entry.title class='media-object'}</a>
                    {/if}
                    <div class="media-body">
                        <h4 class="media-heading">
                            <a href="{$smarty.const.IA_URL}news/{$entry.id}-{$entry.alias}">{$entry.title|escape}</a>
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

        {navigation aTotal=$pagination.total aTemplate=$pagination.url aItemsPerPage=$pagination.limit aNumPageItems=5}
    {else}
        <div class="alert alert-info">{lang key='no_news'}</div>
    {/if}
{/if}