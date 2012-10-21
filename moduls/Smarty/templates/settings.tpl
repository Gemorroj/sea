{* шапка *}
{include file='header.tpl'}

{* бредкрамбсы *}
{include file='sys/breadcrumbs.tpl'}


{* сортировка *}
<div class="row">{$language.sort}:
    {if $sort == 'name' || $sort == ''}
        <strong>[{$language.name}]</strong>
    {else}
        <a href="{$smarty.const.DIRECTORY}{$id}?sort=name">{$language.name}</a>
    {/if}
    {if $sort == 'date'}
        <strong>[{$language.date}]</strong>
    {else}
        <a href="{$smarty.const.DIRECTORY}{$id}?sort=date">{$language.date}</a>
    {/if}
    {if $sort == 'size'}
        <strong>[{$language.size}]</strong>
    {else}
        <a href="{$smarty.const.DIRECTORY}{$id}?sort=size">{$language.size}</a>
    {/if}
    {if $sort == 'size'}
        <strong>[{$language.popularity}]</strong>
    {else}
        <a href="{$smarty.const.DIRECTORY}{$id}?sort=load">{$language.popularity}</a>
    {/if}
    {if $setup.eval_change}
        {if $sort == 'eval'}
            <strong>[{$language.rating}]</strong>
        {else}
            <a href="{$smarty.const.DIRECTORY}{$id}?sort=eval">{$language.rating}</a>
        {/if}
    {/if}
</div>


{* файлов на странице *}
{if $setup.onpage_change}
    <div class="row">{$language.files_on_page}:
        {for $foo=5 to 35 step=5}
            {if $onpage == $foo}
                <strong>{$foo}</strong>
            {else}
                [<a href="{$smarty.const.DIRECTORY}{$id}?onpage={$foo}">{$foo}</a>]
            {/if}
        {/for}
    </div>
{/if}


{* превью *}
{if $setup.preview_change}
    <div class="row">{$language.preview}:
    {if $prew}
        <strong>[On]</strong>[<a href="{$smarty.const.DIRECTORY}{$id}?prew=0">Off</a>]
    {else}
        [<a href="{$smarty.const.DIRECTORY}{$id}?prew=1">On</a>]<strong>[Off]</strong>
    {/if}
    </div>
{/if}


{* количество символов в библиотеке *}
{if $setup.lib_change}
    <form action="{$smarty.const.DIRECTORY}settings/{$id}" method="post">
        <div class="row">{$language.lib}:<br/>
            <input size="5" class="enter" type="text" name="lib" value="{$lib}"/>
            <input class="buttom" type="submit" value="{$language.go}"/>
        </div>
    </form>
{/if}


{* язык *}
<form action="{$smarty.const.DIRECTORY}settings/{$id}" method="post">
    <div class="row">{$language.language}:<br/>
        <select class="enter" name="langpack">
            {foreach $langpacks as $lang}
                <option value="{$lang}" {if $lang == $langpack}selected="selected"{/if}>{$lang}</option>
            {/foreach}
        </select>

        <input class="buttom" type="submit" value="{$language.go}"/>
    </div>
</form>





{* стиль *}
{if $setup.style_change}
    <form action="{$smarty.const.DIRECTORY}settings/{$id}" method="post">
        <div class="row">{$language.style}:<br/>
            <select class="enter" name="style">

                {foreach $styles as $loop_style}
                    <option value="{$smarty.server.HTTP_HOST}{$smarty.const.DIRECTORY}{$loop_style}" {if $style|pathinfo:$smarty.const.PATHINFO_FILENAME == $loop_style|pathinfo:$smarty.const.PATHINFO_FILENAME}selected="selected"{/if}>{$loop_style|pathinfo:$smarty.const.PATHINFO_FILENAME}</option>
                {/foreach}

            </select>
            <input class="buttom" type="submit" value="{$language.go}"/>
        </div>
    </form>
{/if}


{* сервисное использование *}
{if $setup.service_change}
    <form action="{$smarty.const.DIRECTORY}settings/{$id}" method="post">
        <div class="row">{$language.service}:<br/>
            <input class="enter" type="text" value="http://{$smarty.server.HTTP_HOST}{$smarty.const.DIRECTORY}?url=somebody.com{if $setup.style_change}&amp;style={$smarty.server.HTTP_HOST}{$smarty.const.DIRECTORY}style/{$setup.css}.css{/if}"/>
        </div>
    </form>
{/if}


{* расширенное сервисное использование *}
{if $setup.service_change_advanced}
    <div class="row"><a href="{$smarty.const.DIRECTORY}service.php">{$language.advanced_service}</a><br/></div>
{/if}



{* нижнее меню *}
<div class="iblock">
    - <a href="{$smarty.const.DIRECTORY}{$id}">{$language.back}</a><br/>
    - <a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a><br/>
    - <a href="{$setup.site_url}">{$language.home}</a><br/>
</div>


{* футер *}
{include file='footer.tpl'}