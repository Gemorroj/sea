<form action="{$smarty.const.DIRECTORY}service?act=registration" method="post">
    <div class="row">
        <table>
            <tr>
                <th colspan="2">{$language.registration}</th>
            </tr>
            <tr>
                <td><label for="url">{$language.your_site}</label></td>
                <td><input class="enter" type="url" name="url" id="url" required="required" value="http://"/></td>
            </tr>
            <tr>
                <td><label for="name">{$language.name}</label></td>
                <td><input class="enter" type="text" name="name" id="name" required="required" style="width:96%;"/></td>
            </tr>
            <tr>
                <td><label for="style">{$language.style}:</label></td>
                <td><input class="enter" type="url" id="style" name="style" required="required" value="http://{$style}"/></td>
            </tr>
            <tr>
                <td><label for="mail">Email:</label></td>
                <td><input class="enter" type="email" name="mail" id="mail" required="required" style="width:96%;"/></td>
            </tr>
            <tr>
                <td><label for="pass">{$language.pass}</label></td>
                <td><input class="enter" type="password" name="pass" id="pass" style="width:96%;" required="required"/></td>
            </tr>
            <tr>
                <th><img onclick="this.src=this.src+'&amp;'" alt="" src="{$smarty.const.DIRECTORY}moduls/kcaptcha/index.php?{session_name()}={session_id()}" /></th>
                <td><input class="enter" type="number" name="keystring" maxlength="4" required="required" style="width:96%;"/></td>
            </tr>
            <tr>
                <th colspan="2"><input type="submit" value="{$language.go}" class="buttom"/></th>
            </tr>
        </table>
    </div>
</form>