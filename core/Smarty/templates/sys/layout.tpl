<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="alternate" type="application/rss+xml" href="http://{$smarty.server.HTTP_HOST}{$smarty.const.DIRECTORY}rss.php"/>
        <link rel="stylesheet" type="text/css" href="http://{$style}"/>
        <title>{$setup.zag} - {$seo.title|default:'/'}</title>
        <meta name="keywords" content="{$seo.keywords|default:'sea downloads'}"/>
        <meta name="description" content="{$seo.description|default:'sea downloads'}"/>
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