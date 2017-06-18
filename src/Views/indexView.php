<?php
function showInputForm(){
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
<form method="post" action="/index.php">
    <table>
        <tbody>
        <tr>
            <td>content:</td>
            <td><textarea type="text" id="content" name="content" rows="5" cols="50"></textarea></td>
        </tr>
        <tr>
            <td>tags:</td>
            <td><input type="text" name="tags"></td>
        </tr>
        <tr>
            <td>authors:</td>
            <td><input type="text" name="authors"></td>
        </tr>
        <tr>
            <td>movie_name:</td>
            <td><input type="text" name="movie_name"></td>
        </tr>
        <tr>
            <td>book_name:</td>
            <td><input type="text" name="book_name"></td>
        </tr>
        <tr><td>
                <input type="submit">
            </td></tr>
        </tbody>
    </table>
</form>
<?php
}