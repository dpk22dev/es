globalLastLine = '';

function getStrongWords(textarea) {
    $(textarea).keypress(function(e) {
        if( e.keyCode == 13 || e.which == 13) {
            var arrLines = textarea.value.substr(0, textarea.selectionStart).split("\n");
            var lineNo = arrLines.length;
            var lastLine = arrLines[lineNo-1];
            var postData = { current_text : lastLine, userId : 1, artId : 1, lineNum : lineNo };
            console.log( lastLine );
            if( typeof lastLine != 'undefined' && lastLine.length > 0 && globalLastLine != lastLine){
                globalLastLine = lastLine;
                var strongWordsArr = getListStrongWords( postData );
            }
            //e.preventDefault();
//            return false;
        }
    });
}

function getListStrongWords( postData ){
    var jqxhr = $.post( "/Suggestor.php", postData,function() {})
        .done(function( data ) {
            if( typeof data == 'string' ){
                arr = JSON.parse( data );
                console.log( arr );
                return arr;
            }
        })
        .fail(function() {
            return [];
        })
}

/*
$('#content').one('onkeyup', function ( e ) {
    getStrongWords( $this );
} );*/
