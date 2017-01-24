
var j = jQuery.noConflict();

j(document).ready(function () {

    //First load actions
    j('#graft1_label').css('font-weight', 'bold');
    var data = { 'action': 'getRestData', 'graftTypeId': 1 };
    j.post(ajaxurl, data, function(response) {
        j('#dataContainer').html(response);
        j('#calqueLoading, #ring').css('display', 'none');
    });

    init_labels = function(id) {
        j("#type_selector_form label").css('font-weight', 'normal');
        j('#graft' + id + '_label').css('font-weight', 'bold');
    }

    j('#graft1').on('click', function() {
        j('#calqueLoading, #ring').css('display', 'block');
        init_labels(j(this).data('id'));
        var data = { 'action': 'getRestData', 'graftTypeId': 1 };
        j.post(ajaxurl, data, function(response) {
            j('#dataContainer').html(response);
            j('#calqueLoading, #ring').css('display', 'none');
        });
    });

    j('#graft2').on('click', function() {
        j('#calqueLoading, #ring').css('display', 'block');
        init_labels(j(this).data('id'));
        var data = { 'action': 'getRestData', 'graftTypeId': 2 };
        j.post(ajaxurl, data, function(response) {
            j('#dataContainer').html(response);
            j('#calqueLoading, #ring').css('display', 'none');
        });
    });

    j('#graft3').on('click', function() {
        j('#calqueLoading, #ring').css('display', 'block');
        init_labels(j(this).data('id'));
        var data = { 'action': 'getRestData', 'graftTypeId': 3 };
        j.post(ajaxurl, data, function(response) {
            j('#dataContainer').html(response);
            j('#calqueLoading, #ring').css('display', 'none');
        });
    });

    j('#graft4').on('click', function() {
        j('#calqueLoading, #ring').css('display', 'block');
        init_labels(j(this).data('id'));
        var data = { 'action': 'getRestData', 'graftTypeId': 4 };
        j.post(ajaxurl, data, function(response) {
            j('#dataContainer').html(response);
            j('#calqueLoading, #ring').css('display', 'none');
        });
    });

    //************************
    //Popovers display events
    //************************
    j('#D0').on('mouseenter', function() {
        j(this).attr( 'fill', '#febc6c' ).css( 'cursor', 'pointer' );
    }).on('mouseleave', function() {
        j(this).attr( 'fill', '#F7A541' );
    }).webuiPopover({
        constrains: 'horizontal',
        trigger:'hover',
        multi: false,
        closeable:true,
        placement:'top',
        width:250,
        animation:'pop',
        style:'inverse'
    });

    j('#R0').on('mouseenter', function() {
        j(this).attr( 'fill', '#febc6c' ).css( 'cursor', 'pointer' );
    }).on('mouseleave', function() {
        j(this).attr( 'fill', '#F7A541' );
    }).webuiPopover({
        constrains: 'horizontal',
        trigger:'hover',
        multi: false,
        closeable:true,
        placement:'top',
        width:250,
        animation:'pop',
        style:'inverse'
    });

    j('#a1').on('mouseenter', function() {
        j(this).attr( 'fill', '#0d8588' ).css( 'cursor', 'pointer' );
    }).on('mouseleave', function() {
        j(this).attr( 'fill', '#055759' );
    }).webuiPopover({
        constrains: 'horizontal',
        trigger:'hover',
        multi: false,
        closeable:true,
        placement:'top',
        width:250,
        animation:'pop',
        style:'inverse'
    });

    j('#a2').on('mouseenter', function() {
        j(this).attr( 'fill', '#0d8588' ).css( 'cursor', 'pointer' );
    }).on('mouseleave', function() {
        j(this).attr( 'fill', '#055759' );
    }).webuiPopover({
        constrains: 'horizontal',
        trigger:'hover',
        multi: false,
        closeable:true,
        placement:'top',
        width:250,
        animation:'pop',
        style:'inverse'
    });

    j('#c1').on('mouseenter', function() {
        j(this).attr( 'fill', '#0d8588' ).css( 'cursor', 'pointer' );
    }).on('mouseleave', function() {
        j(this).attr( 'fill', '#055759' );
    }).webuiPopover({
        constrains: 'horizontal',
        trigger:'hover',
        multi: false,
        closeable:true,
        placement:'top',
        width:250,
        animation:'pop',
        style:'inverse'
    });

    j('#c2').on('mouseenter', function() {
        j(this).attr( 'fill', '#0d8588' ).css( 'cursor', 'pointer' );
    }).on('mouseleave', function() {
        j(this).attr( 'fill', '#055759' );
    }).webuiPopover({
        constrains: 'horizontal',
        trigger:'hover',
        multi: false,
        closeable:true,
        placement:'top',
        width:250,
        animation:'pop',
        style:'inverse'
    });

    j('#s1').on('mouseenter', function() {
        j(this).attr( 'fill', '#56d7e8' ).css( 'cursor', 'pointer' );
    }).on('mouseleave', function() {
        j(this).attr( 'fill', '#45BCCC' );
    }).webuiPopover({
        constrains: 'horizontal',
        trigger:'hover',
        multi: false,
        closeable:true,
        placement:'bottom',
        width:250,
        animation:'pop',
        style:'inverse'
    });

    j('#s2').on('mouseenter', function() {
        j(this).attr( 'fill', '#56d7e8' ).css( 'cursor', 'pointer' );
    }).on('mouseleave', function() {
        j(this).attr( 'fill', '#45BCCC' );
    }).webuiPopover({
        constrains: 'horizontal',
        trigger:'hover',
        multi: false,
        closeable:true,
        placement:'bottom',
        width:250,
        animation:'pop',
        style:'inverse'
    });

    j('#s3').on('mouseenter', function() {
        j(this).attr( 'fill', '#56d7e8' ).css( 'cursor', 'pointer' );
    }).on('mouseleave', function() {
        j(this).attr( 'fill', '#45BCCC' );
    }).webuiPopover({
        constrains: 'horizontal',
        trigger:'hover',
        multi: false,
        closeable:true,
        placement:'bottom',
        width:250,
        animation:'pop',
        style:'inverse'
    });
});