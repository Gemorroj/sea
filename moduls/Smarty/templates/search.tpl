{* шапка *}
{include file='header.tpl'}

{* бредкрамбсы *}
{include file='sys/breadcrumbs.tpl'}



{* поиск *}
{if $word != ''}
    <div class="iblock">
        {$language.upon_request|replace:'%word%':$word|replace:'%all%':$allItemsInDir}
    </div>
    {include file='sys/_files.tpl'}
{/if}


{* пагинация *}
{paginationExtended page=$page pages=$pages url="{$smarty.const.DIRECTORY}search" query=['word'=>$word]}


{* поиск *}
<div class="mblock">
    <form action="{$smarty.const.DIRECTORY}search" method="get">
        <div class="row">
            <label>
                {$language.enter_the_name_of_the_file_you_are}<br/>
                <input class="enter" name="word" type="search" required="required" maxlength="255" value="{$word}"/><br/>
            </label>
            <input class="buttom" type="submit" value="{$language.go}"/>
        </div>
    </form>
</div>


{* нижнее меню *}
<ul class="iblock">
    <li><a href="{$smarty.const.DIRECTORY}settings/{$id}">{$language.settings}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
    <li><a href="{$setup.site_url}">{$language.home}</a></li>
</ul>

{* футер *}
{include file='footer.tpl'}