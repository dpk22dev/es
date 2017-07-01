<?php
function showInputForm( $arr ){

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

<script src="<?php echo getJsDirPath().'/jquery.js'; ?>" ></script>
<script>
    <?php
    $uId = Utils::getUserId();
    $domain = Utils::getRootDomain( );

    echo 'var uId = '.$uId.';';
    echo 'var domain = '.$domain.';';
    ?>
</script>
<div>
    <span class="message"></span>
</div>
<form method="post" action="/index.php">
    <input type="hidden" name="artId" value="<?php echo $arr['artId']; ?>" readonly>
    <input type="hidden" name="uId" value="<?php echo $uId; ?>" readonly>
    <table>
        <tbody>
        <tr>
            <td>content:</td>
            <td><textarea type="text" id="content" name="content" rows="10" cols="40" onkeyup="getStrongWords(this);" ><?php echo $arr['content']; ?></textarea></td>
        </tr>
        <tr>
            <td>categories:</td>
            <td><input type="text" name="categories" value="<?php echo $arr['categories']; ?>"></td>
        </tr>
        <tr>
            <td>language:</td>
            <td><input type="text" name="language" value="<?php echo $arr['language']; ?>"></td>
        </tr>
        <tr>
            <td>tags:</td>
            <td><input type="text" name="tags" value="<?php echo $arr['tags']; ?>"></td>
        </tr>
        <tr>
            <td>writer:</td>
            <td><input type="text" name="writer" value="<?php echo $arr['writer']; ?>" ></td>
        </tr>
        <tr>
            <td>movie_name:</td>
            <td><input type="text" name="movie_name" value=" <?php echo $arr['movie_name']; ?>" ></td>
        </tr>
        <tr>
            <td>book_name:</td>
            <td><input type="text" name="book_name" value="<?php echo $arr['book_name']; ?>"></td>
        </tr>
        <tr>
            <td><input type="submit"></td>
            <td><input type="button" id="verifyBtn" onclick="checkIfDocExists()" value="check if doc already exists"></td>
        </tr>
        </tbody>
    </table>
</form>
<div id="strongWordDiv" class="strongWordDiv"></div>
<div id="swDocListDiv" class="swDocListDiv"></div>
<div id="swDocDiv" class="swDocDiv"></div>

<div id="docsForVerify">

</div>
<script src="<?php echo getJsDirPath().'/logic.js'; ?>" ></script>
<?php
}