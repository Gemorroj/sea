{extends file='sys/layout.tpl'}



{* поиск *}
{block header}
    {if $word != ''}
        <div class="iblock">
            {$language.upon_request|replace:'%word%':$word|replace:'%all%':$paginatorConf.items}
        </div>
    {/if}
{/block}

{block content}
    {if $word != ''}
        {include file='sys/_files.tpl'}

        {* пагинация *}
        {paginationExtended page=$paginatorConf.page pages=$paginatorConf.pages url="{$smarty.const.DIRECTORY}search" query=['word'=>$word]}
    {/if}

    {* поиск *}
    <form action="{$smarty.const.DIRECTORY}search" method="get">
        <div class="row">
            <label>
                {$language.enter_the_name_of_the_file_you_are}<br/>
                <input class="enter" name="word" type="search" required="required" maxlength="255" value="{$word}"/><br/>
            </label>
            <input class="buttom" type="submit" value="{$language.go}"/>
        </div>
    </form>
{/block}


{block footer}
    <ul class="iblock">
        <li><a href="{$smarty.const.DIRECTORY}settings/{$id}">{$language.settings}</a></li>
        <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}