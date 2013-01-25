{* настройки сервиса *}

<form action="{$smarty.const.DIRECTORY}service?act=save" method="post">
    <div class="row">
        <table>
            <tr>
                <th>N</th>
                <th>{$language.name}</th>
                <th>{$language.link}</th>
            </tr>

            {if $setup.service_head}
                <tr><th colspan="3">{$language.head}</th></tr>

                {foreach $head as $key => $val}
                    <tr>
                        <td>{$key}</td>
                        <td><input class="enter" type="text" name="head[name][]" value="{$val.name}"/></td>
                        <td><input class="enter" type="url" name="head[value][]" value="{if $val.value}http://{/if}{$val.value}"/></td>
                    </tr>
                {/foreach}
            {/if}

            {if $setup.service_foot}
                <tr><th colspan="3">{$language.foot}</th></tr>

                {foreach $foot as $key => $val}
                    <tr>
                        <td>{$key}</td>
                        <td><input class="enter" type="text" name="foot[name][]" value="{$val.name}"/></td>
                        <td><input class="enter" type="url" name="foot[value][]" value="{if $val.value}http://{/if}{$val.value}"/></td>
                    </tr>
                {/foreach}
            {/if}

            <tr>
                <th colspan="3">URL</th>
            </tr>
            <tr>
                <td>&#187;</td>
                <td><input class="enter" type="text" name="name" required="required" value="{$smarty.session.name}"/></td>
                <td><input class="enter" type="text" name="url" required="required" value="http://{$smarty.session.url}"/></td>
            </tr>
            <tr>
                <td>Email</td>
                <td colspan="2"><input class="enter" required="required" type="email" name="mail" value="{$smarty.session.mail}" style="width:98%;"/></td>
            </tr>
            <tr>
                <td>{$language.style}</td>
                <td colspan="2"><input class="enter" required="required" type="url" name="style" value="http://{$style}" style="width:98%;"/></td>
            </tr>
            <tr>
                <th colspan="3"><input type="submit" value="{$language.go}" class="buttom"/></th>
            </tr>
        </table>
    </div>
</form>


<form action="" onsubmit="return false;">
    <div class="row">
        <label>
            {$language.service}<br/>
            <input class="enter" type="text" value="http://{$smarty.server.HTTP_HOST}{$smarty.const.DIRECTORY}?user={$smarty.session.id}"/>
        </label>
    </div>
</form>
