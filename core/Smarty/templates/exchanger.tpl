{extends file='sys/layout.tpl'}


{* добавить файл *}
{block content}
    {if $smarty.post}
        <div class="row">
            <span class="yes">{$language.file_successfully_added}</span>

            {if !$setup.exchanger_hidden}
                <br/><a href="{$smarty.const.DIRECTORY}view/{$insertId}">http://{$smarty.server.HTTP_HOST}{$smarty.const.DIRECTORY}view/{$insertId}</a>
            {/if}
        </div>
    {else}
        <form action="{$smarty.const.DIRECTORY}exchanger/{$id}" method="post" enctype="multipart/form-data">
            <div class="row">
                <label>
                    {$language.save}
                    <select class="buttom" name="topath">
                        {foreach $dirs as $k => $v}
                            <option value="{$k}">{$v}</option>
                        {/foreach}
                    </select><br/>
                </label>
                <label>
                    {$language.file} ({$setup.exchanger_name} / {$setup.exchanger_extensions} / {$upload_max_filesize})<br/>
                    <input type="file" name="file" class="enter" required="required"/><br/>
                </label>
                <label>
                    {$language.screenshot} (jpeg,gif,png)<br/>
                    <input type="file" name="screen" class="enter" accept="image/*"/><br/>
                </label>
                <label>
                    {$language.description}<br/>
                    <textarea class="enter" cols="24" rows="2" name="about"></textarea><br/>
                </label>
                <input class="buttom" type="submit" value="{$language.go}"/>
            </div>
        </form>
    {/if}
{/block}


{block footer}
    <ul class="iblock">
        <li><a href="{$smarty.const.DIRECTORY}{$id}">{$language.back}</a></li>
        <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}