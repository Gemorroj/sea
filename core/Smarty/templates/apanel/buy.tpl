{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>Реклама</h3>

<form action="apanel.php?action=buy" method="post">
    <div class="ui-body ui-body-c">
        <div data-role="fieldcontain">
            <label for="buy">XHTML код отображаемый сверху:</label>
            <textarea cols="32" rows="5" name="buy" id="buy">{$setup.buy}</textarea>
        </div>

        <div data-role="fieldcontain">
            <label for="countbuy">Количество отображаемых строк:</label>
            <input id="countbuy" name="countbuy" type="number" value="{$setup.countbuy}" />
        </div>

        <div data-role="fieldcontain">
            <label for="randbuy">В случайном порядке</label>
            <input id="randbuy" name="randbuy" type="checkbox" {if $setup.randbuy}checked="checked"{/if}/>
        </div>
    </div>

    <p></p>

    <div class="ui-body ui-body-c">
        <div data-role="fieldcontain">
            <label for="banner">XHTML код отображаемый снизу:</label>
            <textarea id="banner" cols="32" rows="5" name="banner">{$setup.banner}</textarea>
        </div>

        <div data-role="fieldcontain">
            <label for="countbanner">Количество отображаемых строк:</label>
            <input id="countbanner" name="countbanner" type="number" value="{$setup.countbanner}" />
        </div>

        <div data-role="fieldcontain">
            <label for="randbanner">В случайном порядке</label>
            <input id="randbanner" name="randbanner" type="checkbox" {if $setup.randbanner}checked="checked"{/if}/>
        </div>
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}