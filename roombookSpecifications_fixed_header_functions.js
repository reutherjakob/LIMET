//https://stackoverflow.com/questions/32216719/datatables-fixedheader-with-scrollx

        /* Fixed Header CSS  
        table.dataTable.fixedHeader-floating {
            display: none !important;  /*Hide the fixedHeader since we dont need it*//* 
        }
        .dataTables_scrollHeadInner{
            margin-left: 0px;
            width: 100% !important;
            position: sticky !important;
            display: block;
            overflow: hidden;
            /*margin-right: 30px;*//*
            background: white;
            z-index: 1;
        }
        .dataTables_scrollBody{
            padding-top: 2.5em;
        }
        div.dataTables_scrollHead table.dataTable {
            padding-right: 0;
}*/

var yPositionOfScrollBody;

function adjustDatatableInnerBodyPadding(){
    let $dtScrollHeadInner = $('.dataTables_scrollHeadInner');
    let outerHeightOfInnerHeader = $dtScrollHeadInner.outerHeight(true);
    //console.log('outerHeightOfInnerHeader => ' + outerHeightOfInnerHeader);
    $('.dataTables_scrollBody').css('padding-top', outerHeightOfInnerHeader); 
}

function setFixedHeaderTop(header_pos){
    //console.log("header_pos : " + header_pos);
    $('.dataTables_scrollHeadInner').css({"top": header_pos});
}

function fixDatatableHeaderTopPosition(){
    //console.log("fixHeaderTop...");

    yPositionOfScrollBody = window.scrollY + document.querySelector('.dataTables_scrollBody').getBoundingClientRect().top;
    //console.log("yPositionOfScrollBody: " + yPositionOfScrollBody);

    setFixedHeaderTop(yPositionOfScrollBody);
}

function onDataTableInitComplete(settings, json) {

    // for vertical scolling
    yPositionOfScrollBody =  window.scrollY + document.querySelector('.dataTables_scrollBody').getBoundingClientRect().top;

    // datatable padding adjustment
    adjustDatatableInnerBodyPadding();

    // data table fixed header F5 (refresh/reload) fix
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    //console.log("scrollTop => " + scrollTop);
    if(scrollTop > 1){
        let header_pos;
        if (scrollTop < yPositionOfScrollBody){
           header_pos = yPositionOfScrollBody - scrollTop;
        } else {
           header_pos = 0;
        }
        setFixedHeaderTop(header_pos);
    }

    let $dtScrollHeadInner = $('.dataTables_scrollHeadInner');
    // horizontal scrolling
    $('.dataTables_scrollBody').on('scroll', function () {

        let $dtScrollBody = $(this);

        // synchronize
        let amountOfLeftScroll = $dtScrollBody.scrollLeft();
        $dtScrollHeadInner.scrollLeft(amountOfLeftScroll);

        let scrollDiff =  $dtScrollHeadInner.scrollLeft() - amountOfLeftScroll;

        //console.log("scrollDiff: " + scrollDiff);

        if(scrollDiff < 0){
            $dtScrollHeadInner.css('left', scrollDiff);
        }else{
            //console.log("scroll back to left side");
            $dtScrollHeadInner.css('left', '');
        }

    });

    //console.log("adjusment mergin: " + yPositionScrollHeadInner);
    $(document).on('scroll', function () {
        let scroll_pos = $(this).scrollTop();
        if(scroll_pos <= 0){
            fixDatatableHeaderTopPosition();
        }else{
            let margin = yPositionOfScrollBody; // Adjust it to your needs
            let cur_pos = $('.dataTables_scrollHeadInner').position();
            let header_pos = cur_pos.top;
            if (scroll_pos < margin){
               header_pos = margin - scroll_pos;
            } else {
               header_pos = 0;
            }
            setFixedHeaderTop(header_pos);
        }
    });
}