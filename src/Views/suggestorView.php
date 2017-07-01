<?php
function showSuggestorForm( $arr )
{
    $userId = 1;
    $artId = 1;
    $text = 'दिल';
    $lineNum = 1;

    if (empty($_REQUEST)) {
        ?>
        <style>
            tbody {
                display: table-row-group;
                vertical-align: middle;
                border-color: inherit;
            }

            tr {
                display: table-row;
                vertical-align: inherit;
                border-color: inherit;
            }

        </style>
        <script>
            <?php
            $uId = Utils::getUserId();
            $domain = Utils::getRootDomain();

            echo 'var uId = '.$uId.';';
            echo 'var domain = '.$domain.';';
            ?>
        </script>
        <form method="post" action="/Suggestor.php">
            <input type="hidden" name="artId" value="<?php echo $arr['artId']; ?>" readonly>
            <input type="hidden" name="uId" value="<?php echo $uId; ?>" readonly>
            <table>
                <tbody>
                <tr>
                    <td>words:</td>
                    <td><input type="text" name="current_text" id="current_text" value="<?php echo $text?>"></td>
                </tr>
                <tr>
                    <td>userid:</td>
                    <td><input type="text" name="userId" id="userId" value="<?php echo $userId?>"></td>
                </tr>
                <tr>
                    <td>artid:</td>
                    <td><input type="text" name="artId" id="artId" value="<?php echo $artId?>"></td>
                </tr>
                <tr>
                    <td>line#:</td>
                    <td><input type="text" name="lineNum" id="lineNum" value="<?php echo $lineNum ?>"></td>
                </tr>
                <tr>
                    <td>
                        <input type="submit">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php
        die();
    }
}

function getButtonsFromStrongWords( $strongWordArr ){
    
}
