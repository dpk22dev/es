globalLastLine = '';
globalStrongWordsArr = [];
globalStrongWordConfig = {
    strongWordDiv : "#strongWordDiv",
    strongWordDocListDiv : "#swDocListDiv",
    strongWordDocDiv : "#swDocDiv"
};

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
                getListStrongWords( postData );
            }
            //e.preventDefault();
//            return false;
        }
    });
}

function  makeStrongWordsDataGlobal( arr ){
    if( typeof arr =='object' && arr.length > 0 )
        globalStrongWordsArr = arr;
    else
        globalStrongWordsArr = [];
}

function getListStrongWords( postData ){
    // if its in local storage get from there
    if (typeof(Storage) !== "undefined") {
        var arr = fetchStrongWordsDataFromLocalStorage( postData );
    }

    if( arr.length > 0 ) {
        makeStrongWordsDataGlobal(arr);
        showListOfKeywords(arr);
    } else {
        // else get from ajax and store in local storage
        //@todo remember to expire storage to include new suggestion and window
        var jqxhr = $.post("/Suggestor.php", postData, function () { })
            .done(function (data) {
                var arr = [];
                if (typeof data == 'string') {
                    arr = JSON.parse(data);
                } else if (data instanceof Array ) {
                    arr = data;
                } else {
                    console.log('suggestor returned: ' + typeof data);
                    data = "";
                    arr = [];
                }
                insertStrongWordsDataIntoLocalStorage( postData, data );
                makeStrongWordsDataGlobal( arr );
                showListOfKeywords( arr );
                //console.log(arr);

            })
            .fail(function () {
                return [];
            })
    }
}

function insertStrongWordsDataIntoLocalStorage( postData, data ) {
    var key = postData.current_text;
    if( typeof data == 'object' )
        var val = JSON.stringify( data );

    if( typeof val=='string' && val.length > 0 )
        localStorage.setItem( key, val );

}

function fetchStrongWordsDataFromLocalStorage( postData ) {
    var key = postData.current_text;
    var value = localStorage.getItem( key );
    if( value != null && typeof value =='object' && value.length  > 0 )
        value = JSON.parse( value );
    else
        value = [];

    return value;
}

function showListOfKeywords( arr ){
    var strongWordDiv = $( globalStrongWordConfig.strongWordDiv );

    $(strongWordDiv).empty();
    if( arr.length < 1 ) {
        console.log('no keywords found');
        var p = $('<p/>')
            .addClass('noKeywords')
            .text('no keywords found')
            .appendTo( strongWordDiv );

        return;
    }

    var strongWordDivUl = $('<ul/>');

    $.each(arr, function(i)
    {
        var li = $('<li/>')
            .addClass('strongWord')
            .attr('data-index', i)
            .appendTo( strongWordDivUl );
        var text = arr[i].word + ' (' + arr[i].cnt+ ' )';
        var aaa = $('<a/>')
            .addClass('strongWordLink')
            .text(text)
            .appendTo(li);
    });

    $(strongWordDiv).append( strongWordDivUl );

}

/*
$('#content').one('onkeyup', function ( e ) {
    getStrongWords( $this );
} );*/

$( document ).on( 'click', '#strongWordDiv .strongWord', function ( e ) {
    var inx = $(this).attr('data-index');
    if( globalStrongWordsArr.length > 0 ){
        var data = globalStrongWordsArr[inx];
        showDocsListForStrongWord( data );
    }

});

$( document ).on( 'click', '#swDocListDiv .strongWordDoc', function ( e ) {
    var inx = $(this).attr('data-docIndex');
    fetchAndShowDoc( inx );
});

function showDoc( data ) {
    var docDiv = $( globalStrongWordConfig.strongWordDocDiv );

    if (typeof data == 'string') {
        txtObj = JSON.parse(data);
    } else if (data instanceof Object ) {
        txtObj = data;
    }

    $(docDiv).empty();
    var pEle = $('<p/>')
        .addClass('doc')
        .text(txtObj.article);
    $(docDiv).append( pEle );
}

function fetchAndShowDoc( inx ) {

    var jqxhr = $.post("/Article.php", { docId : inx }, function () { })
        .done(function (data) {
            console.log( data );
            showDoc( data );
        })
        .fail(function () {
            console.log('failed to retrieve document at this moment');
        })
}

function showDocsListForStrongWord( data ) {
    var swDocs = data.info;
    if( swDocs.length > 0 ){

        var strongWordDocListDiv = $( globalStrongWordConfig.strongWordDocListDiv );
        strongWordDocListDiv.empty();

        var strongWordDocListUl = $('<ul/>');
        $.each(swDocs, function (i) {
            var docObj = swDocs[i];
            var li = $('<li/>')
                .addClass('strongWordDoc')
                .attr('data-docIndex', docObj.docId )
                .appendTo( strongWordDocListUl );
            var text = docObj.str;
            var aaa = $('<a/>')
                .addClass('strongWordDocLink')
                .text( text )
                .appendTo(li);
        });

        $(strongWordDocListDiv).append( strongWordDocListUl );
    }

}