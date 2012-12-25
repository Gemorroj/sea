{extends file='sys/layout.tpl'}



{* email *}
{block content}
<form action="{$smarty.const.DIRECTORY}email/{$id}" method="post">
    <div class="row">
        <label>
            Email:<br/>
            <input type="email" name="email" class="enter" required="required" value="{(isset($smarty.cookies.sea_email)) ? $smarty.cookies.sea_email : ''}"/><br/>
        </label>
        <input type="submit" class="buttom" value="{$language.go}"/>
    </div>
</form>
{/block}


{block footer}
<ul class="iblock">
    <li><a href="{$smarty.const.DIRECTORY}view/{$id}">{$file.name}</li>
    <li><a href="{$smarty.const.DIRECTORY}settings/{$id}">{$language.settings}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
    <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
</ul>
{/block}