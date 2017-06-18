<?php
function showSuggestorForm()
{
    $userId = 1;
    $artId = 1;
    $text = 'दौलत';
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
        <form method="post" action="/Suggestor.php">
            <table>
                <tbody>
                <tr>
                    <td>words:</td>
                    <td><input type="text" name="current_text" id="current_text" value="<?php echo $text?>"></td>
                    <td>userid:</td>
                    <td><input type="text" name="userId" id="userId" value="<?php echo $userId?>"></td>
                    <td>artid:</td>
                    <td><input type="text" name="artId" id="artId" value="<?php echo $artId?>"></td>
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
