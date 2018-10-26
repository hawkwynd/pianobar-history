/**
 * @author Scott Fleming
 * @email scott.fleming@gmail.com
 * Date: 10/26/18
 *
 *
 */
$(document).ready(function() {

    var info = false;

    $('#deviceTable').dataTable({
        "lengthMenu": [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "All"]],
        pageLength: 10,
        order: [[7, "desc"]],
        ajax: "mongod.php?table=pianobar",
        dataSrc: 'data',
        columns : [
            {data: "masterId",
                "render" : function(data){

                    if(data){
                        info = false;
                        return '<a href="#" class="modalLink" id='+data+' >Info</a>';
                    } else{
                        info = true;
                        return '';

                    }
                }
            },
            {data : "artist",
                "render" : function(data){
                    if(info) {
                        return '<a href="#" class="artistLink" id="' + data + '">' + data + '</a>';
                    }else{
                        return data;
                    }
                }
            },
            {className: "title" ,  data : "title"},
            {className : "album" , data : "album"},
            {data : "genre"},
            {data : "year"},
            {className: "label", data : "label"},
            {data: "loveDate",
                "render": function (data) {
                    var date = new Date(data);
                    var month = date.getMonth() + 1;
                    return (month.length > 1 ? month : month) + "/" + date.getDate() + "/" + date.getFullYear()+ "&nbsp;" +(date.getHours() < 10 ? ("0"+date.getHours()) : date.getHours())+ ":"+(date.getMinutes() < 10 ? ("0"+date.getMinutes()) : date.getMinutes()) ;
                }},
            {data: "coverImg",
                "render" : function(data) {
                    if(data){
                        return '<div class="zoom"><img src=' + data + '></div>';
                    }else{
                        return '';
                    }

                }
            }

        ]
    });

    // Get the modal
    var modal = document.getElementById('myModal');
    // Get the <span> element that closes the modal
    var fclose = document.getElementsByClassName("close")[0];
    var lclose = document.getElementsByClassName("close")[1];

    // When the user clicks on <span> (x), close the modal
    fclose.onclick = function() {
        modal.style.display = "none";
    }
    // when user clicks the bottom close span, close the modal
    lclose.onclick = function() {
        modal.style.display = "none";
    }
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // ------------------------------------------
    // Get just the artist excerpt from Wikipedia
    // ------------------------------------------

    $(document).on('click', '.artistLink', function() {

        var data = encodeURIComponent($(this).attr('id'));
        var url  = 'getArtist.php?artist=' + data;

        $.ajax({
            type: 'GET',
            url : url,
            success: function( response ){

                var content = $.parseJSON( response );

                $.each(content.query.pages, function(idx, v){
                    output = v.extract;
                });


                console.log( $(output).text() );

                modal.style.display = "block";

                $('.content-container').html(  output );


            },
            error: function( response ){
                alert("fail");
            }
        });

    });


    // The magic happens here to create a modal and populate it from
    // the json data returned by getMaster.php
    $(document).on('click', '.modalLink', function () {

        $('.content-container').empty(); // flush the toilet!

        var data        = $(this).attr('id');
        var album       = $(this).closest("tr").find(".album").text();
        var songTitle   = $(this).closest("tr").find(".title").text();
        var label       = $(this).closest("tr").find(".label").text();
        var url         = 'getMaster.php?id='+data;

        modal.style.display = "block";

        $.ajax({
            type: 'GET',
            url: url,
            success: function (output) {

                var content = $.parseJSON(output);
                $('.content-container').append('<h3>'+content.artist.name+'</h3>');
                $('.content-container').append('<div class="songTitle">Title: ' + songTitle + '</div>');

                // Get The album
                if(album) $('.content-container').append('<div class="albumTitle">Album: ' + album + '</div>');
                // get Release Year
                if(content.year_released) $('.content-container').append('<div class="albumTitle">Released: ' + content.year_released + '</div>');

                if(label) $('.content-container').append('<div class="albumTitle">Label: ' +label+ '</div>'  );

                // Styles
                if(content.styles) $('.content-container').append('<div class="wiki slant">Style: '+content.styles+'</div>');
                // Genres
                if(content.genres) $('.content-container').append('<div class="wiki slant">Genre: '+content.genres+'</div>');


                // display band extract
                if(content.wiki.extract){
                    $('.content-container').append('<div class=wiki>'+content.wiki.extract+'</div>');
                }

                // Band members list (if available)
                if(content.artist.members.length > 0){
                    var members = '<h3>Members</h3>';
                    $.each(content.artist.members, function( k, v){
                        members += '<div class=memberName excerpt>'+ v.member_name + '</div>';
                        members += '<div class=excerpt>' + v.member_content + '</div>';
                    });
                    $('.content-container').append(members);
                }

                // List YouTube video links
                if(content.artist.videos.length > 0){
                    var videos='<h3>YouTube Videos</h3>';
                    $.each(content.artist.videos, function(k,v){
                        videos += '<div class="vTitle"><a href="'+ v.uri +'" target="_blank">' + v.title + '</a></div>';
                        //videos += '<div class="vLink">' + v.uri + '</div>';
                    });

                    $('.content-container').append(videos);
                }



                console.log(content);
            },
            error: function(output){
                alert("fail");
            }
        });
    });


});