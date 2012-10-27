{* шапка *}
{include file='header.tpl'}

{* бредкрамбсы *}
{include file='sys/breadcrumbs.tpl'}


{* стол заказов *}
<div class="mblock">
    {if $smarty.post}
        <div class="row">
            {if $sended}
                <span class="yes">{$language.message_sent_successfully}</span>
            {else}
                <span class="no">{$language.message_not_sent}</span>
            {/if}
        </div>
    {else}
        <form method="post" action="{$smarty.const.DIRECTORY}table/{$id}">
            <div class="row">
                <label>
                    {$language.inform_administration}<br/>
                    <textarea class="enter" name="text" rows="2" cols="24" required="required"></textarea><br/>
                </label>
                <label>
                    {$language.how_do_you_contact}<br/>
                    <input class="enter" type="email" name="back" maxlength="500" required="required" placeholder="mail@example.com"/><br/>
                </label>
                <input class="buttom" type="submit" name="send" value="{$language.go}"/>
            </div>
        </form>
    {/if}
</div>

{* нижнее меню *}
<ul class="iblock">
    <li><a href="{$smarty.const.DIRECTORY}{$id}">{$language.back}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
    <li><a href="{$setup.site_url}">{$language.home}</a></li>
</ul>


{* футер *}
{include file='footer.tpl'}