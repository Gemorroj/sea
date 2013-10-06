<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta name="viewport" content="width=device-width"/>
        <link rel="alternate" type="application/rss+xml" href="http://{$smarty.server.HTTP_HOST}{$smarty.const.DIRECTORY}rss.php"/>
        <link rel="stylesheet" type="text/css" href="http://{$style}"/>
        <title>{strip}
            {*
            {if IS_INDEX|defined}
                {if $smarty.const.IS_INDEX}
                    {$setup.zag}
                {else}
                    {' / '|implode:Breadcrumbs::getBreadcrumbs()}
                {/if}
            {else}
                {assign var='directory' value=Breadcrumbs::getBreadcrumbs()}
                {assign var='file' value=$directory|array_pop}
                {$file}{if $directory} | {' / '|implode:$directory}{/if}
            {/if} | {$smarty.server.HTTP_HOST}
            *}
            {$setup.zag} - {if Breadcrumbs::getBreadcrumbs()}{' / '|implode:Breadcrumbs::getBreadcrumbs()} - {/if}{Seo::getTitle()|default:'sea downloads'}
        {/strip}</title>
        <meta name="keywords" content="{Seo::getKeywords()|default:'sea downloads'}"/>
        <meta name="description" content="{Seo::getDescription()|default:'sea downloads'}"/>
        {block javascripts}{/block}
        {block styles}{/block}
    </head>
    <body>
        <div>
            {block breadcrumbs}
                {include file='./breadcrumbs.tpl'}
            {/block}

            {block reklama}
                {include file='./reklama.tpl'}
            {/block}

            {block header}{/block}

            <div class="mblock">{block content}{/block}</div>

            {block footer}{/block}

            {block banner}
                {include file='./banner.tpl'}
            {/block}

            {if $setup.online}
                Online: <strong>{$online}</strong><br/>
            {/if}
            {$pageTime|round:4}
        </div>
    </body>
</html>