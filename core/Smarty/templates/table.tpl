{extends file='sys/layout.tpl'}


{* стол заказов *}
{block content}
    {if $smarty.post}
        <div class="row">
            {if $sended}
                <span class="yes">{$language.message_sent_successfully}</span>
            {else}
                <span class="no">{$language.message_not_sent}</span>
            {/if}
        </div>
    {else}
        <form method="post" action="{$smarty.const.DIRECTORY}table/{Http_Request::get('id')}">
            <div class="row">
                <label>
                    {$language.inform_administration}<br/>
                    <textarea class="enter" name="text" rows="2" cols="24" required="required"></textarea><br/>
                </label>
                <label>
                    {$language.how_do_you_contact}<br/>
                    <input class="enter" type="email" name="back" maxlength="500" required="required" placeholder="mail@example.com"/><br/>
                </label>

                {if $setup.comments_captcha}
                    <label>
                        <img onclick="this.src=this.src+'&amp;'" alt="" src="{$smarty.const.DIRECTORY}kcaptcha?{session_name()}={session_id()}" /><br/>
                        {$language.code}: <input class="enter" type="number" min="0" max="9999" required="required" name="keystring" size="5" maxlength="4"/><br/>
                    </label>
                {/if}

                <input class="buttom" type="submit" name="send" value="{$language.go}"/>
            </div>
        </form>
    {/if}
{/block}


{block footer}
    <ul class="iblock">
        <li><a href="{$smarty.const.DIRECTORY}{Http_Request::get('id')}">{$language.back}</a></li>
        <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}