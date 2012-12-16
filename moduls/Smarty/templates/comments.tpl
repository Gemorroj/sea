{extends file='sys/layout.tpl'}


{* комментарии *}
{block content}
    {if $allItemsInDir < 1}
        <strong>[{$language.empty}]</strong>
    {else}
        {foreach $comments as $comment}
            <div class="{cycle values="row,row2"}">

                {if $smarty.const.IS_ADMIN}
                    <a href="{$smarty.const.DIRECTORY}apanel/apanel.php?comment={$comment.id}&amp;action=del_comment_{$comments_module}" title="Удалить">[X]</a>
                {/if}

                <strong>{$comment.name}</strong> ({$comment.time|dateFormatExtended})<br/>
                <span style="font-size:9px;">{$comment.text|bbcode nofilter}</span><br/>
            </div>
        {/foreach}
    {/if}

    {* пагинация *}
    {paginationExtended page=$page pages=$pages url="{$smarty.const.DIRECTORY}{$comments_module}/{$id}"}

    <form action="{$smarty.const.DIRECTORY}{$comments_module}/{$id}" method="post">
        <div class="row">
            <label>
                {$language.your_name}:<br/>
                <input class="enter" name="name" type="text" required="required" maxlength="255" value="{(isset($smarty.cookies.sea_name)) ? $smarty.cookies.sea_name : ''}"/><br/>
            </label>
            <label>
                {$language.message}:<br/>
                <textarea class="enter" cols="40" rows="5" name="msg" maxlength="65536" required="required"></textarea><br/>
            </label>

            {if $setup.comments_captcha}
                <label>
                    <img onclick="this.src=this.src+'&amp;'" alt="" src="{$smarty.const.DIRECTORY}moduls/kcaptcha/index.php?{session_name()}={session_id()}" /><br/>
                    {$language.code}: <input class="enter" type="number" min="0" max="9999" required="required" name="keystring" size="5" maxlength="4"/><br/>
                </label>
            {/if}

            <input class="buttom" type="submit" value="{$language.go}"/>
        </div>
    </form>
{/block}


{block footer}
<ul class="iblock">
    <li><a href="{$comments_module_backlink}">{$comments_module_backname}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}settings/{$id}">{$language.settings}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
    <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
</ul>
{/block}