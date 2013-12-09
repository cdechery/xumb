$.ajaxSetup({
	contentType: 'application/x-www-form-urlencoded; '+site_charset,
	beforeSend: function(jqXHR) {
		jqXHR.overrideMimeType('application/x-www-form-urlencoded; charset='+site_charset);
	}
});

jQuery.extend({
    handleError: function( s, xhr, status, e ) {
        // If a local callback was specified, fire it
        if ( s.error )
            s.error( xhr, status, e );
        // If we have some XML response text (e.g. from an AJAX call) then log it in the console
        else if(xhr.responseText)
           console.log(xhr.responseText);
    }
})

function countOcurrences(str, value){
   var regExp = new RegExp(value, "gi");
   return str.match(regExp) ? str.match(regExp).length : 0;  
}

function go_home() {
	location.href=site_root;
}

function general_error() {
	new Messi( lang['dist_general_error'], {title: 'Oops...', titleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
}

function load_infowindow_content(infowindow, marker_id){
		$.ajax({
		url: site_root +'map/marker_infowindow/' + marker_id,
		success: function(data){
			infowindow.setContent(data);
		}
	});
}

function newmarker_infowindow_content(lat, long, infowindow) {
		$.ajax({
		url: site_root +'map/newmarker_infowindow/' + lat +'/' +long,
		success: function(data){
		infowindow.setContent(data);
			processInLineLabels();
		}
	});
}

$(function() {
	$('#update_marker').submit(function(e) {
	e.preventDefault();
	$.post($("#update_marker").attr("action"), $("#update_marker").serialize(), function(data) {
		var json = $.parseJSON( data );
		if( json.status=="success" ) {
			new Messi( json.msg );
		} else {
			new Messi( json.msg, {title: 'Ops..', titleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
		}
		}).fail( general_error() );
		return false;
	});
});

$(function() {
	$('#user_insert').submit(function(e) {
	e.preventDefault();
	$.post($("#user_insert").attr("action"), $("#user_insert").serialize(), function(data) {
		var json = $.parseJSON( data );
		if( json.status=="OK" ) {
			new Messi(lang['dist_newuser_ok2'], {title: lang['success'], titleClass: 'success', modal: true, buttons: [{id: 0, label: 'OK', val: 'S'}], callback: function(val) { go_home(); } });
		} else {
			new Messi( json.msg, {title: 'Ops...', titleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
		}
		}).fail( general_error() );
		return false;
	});
});

$(function() {
	$('#user_update').submit(function(e) {
	e.preventDefault();
	$.post($("#user_update").attr("action"), $("#user_update").serialize(), function(data) {
		var json = $.parseJSON( data );
		if( json.status=="OK" ) {
			new Messi(json.msg, {title: lang['success'], titleClass: 'success', modal: true });
		} else {
			new Messi( json.msg, {title: 'Ops...', titleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
		}
		}).fail( general_error() );
		return false;
	});
});

$(function() {
	$('#upload_avatar').submit(function(e) {
		e.preventDefault();
		$.ajaxFileUpload({
			url 		   : site_root +'image/upload_avatar/',
			secureuri      : false,
			fileElementId  :'userfile',
			contentType    : 'application/json; charset=utf-8',
			dataType	   : 'json',
			data        : {
				'thumbs'           : $('#thumbs').val()
			},
			success  : function (data) {
				if( data.status != 'error') {
					$('#user_avatar').attr('src',data.img_src);
					new Messi(data.msg, {title: lang['success'], titleClass: 'success', modal: true });
				} else {
					new Messi(data.msg, {title: lang['error'], tttleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
				}
			},
			error : function (data, status, e) {
				new Messi( lang['dist_error_upload'], {title: 'Oops...', titleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
			}
		});
		return false;
	});
});

var mrkImagesCount = 0;

$(function() {
    $('#upload_marker_image').submit(function(e) {
        e.preventDefault();

        if( max_images_marker!=0 && mrkImagesCount>=max_images_marker ) {
            new Messi(lang['dist_imgupload_max'], {title: lang['error'], tttleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
            return false;
        } 

        $.ajaxFileUpload({
            url : site_root +'image/upload_marker_image/',
            secureuri :false,
            fileElementId :'userfile',
            contentType : 'application/json; charset=utf-8',
            dataType        : 'json',
            data : {
	            'title' : $('#title').val(),
	            'thumbs' : $('#thumbs').val(),
	            'marker_id' : $('#marker_id').val()
            },
            success : function (data) {
                if( data.status != 'error') {
                    if( mrkImagesCount === 0 ) {
                        $('#images').html('');
                    }

	                var imageData = $.get( site_root +'image/get_image/'+data.file_id );
	                imageData.success(function(data) {
                        $('#images').append(data);
                        mrkImagesCount++;
	                });
	                $('#title').val('');
                } else {
                    new Messi(data.msg, {title: lang['error'], tttleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
                }
            },
            error : function (data, status, e) {
				new Messi( lang['dist_error_upload'], {title: 'Oops...', titleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
			}
        });
        return false;
    });
});

function delete_image( link ) {
	$.ajax({
		url         : site_root + 'image/delete_image/' + link.data('file_id'),
		contentType    : 'charset=utf-8',
		dataType : 'json',
		success     : function (data) {
			var images = $('#images');
			if (data.status === "success") {
				link.parent('div').fadeOut('fast', function() {
					$(this).remove();
					if (images.find('div').length === 0) {
						images.html('<p>Sem imagens.</p>');
					}
				});
				mrkImagesCount--;
			} else {
				new Messi(data.msg, {title: lang['error'], tttleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
			}
		},
		error : function (data, status, e) {
			new Messi( lang['dist_imgdel_nok'], {title: 'Oops...', titleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
		}
	});
}

$(function() {
	$(document).on('click', '.delete_file_link', function(e) {
	e.preventDefault();
	var link = $(this);
	new Messi(lang['dist_imgdel_confirm'], {modal: true, buttons: [{id: 0, label: 'Sim', val: 'S'}, {id: 1, label: 'Não', val: 'N'}], callback: function(val) { if(val=='S') delete_image(link); }});

	return false;
	}); // delete
});

function delete_marker_map( link ) {
	$.ajax({
		url         : site_root + 'map/delete_marker/' + link.data('marker_id'),
		contentType    : 'charset=utf-8',
		dataType : 'json',
		success : function (data) {
			if (data.status === "success") {
				var markerVar = eval('marker_'+link.data('marker_id'));
				markerVar.setMap(null);
			} else {
				new Messi(data.msg, {title: lang['error'], tttleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
			}
		}, error : function (data, status, e) {
			new Messi( lang['dist_error_mrkdel'], {title: 'Oops...', titleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
		}
	});
}

$(function() {
	$(document).on('click', '.delete_marker_link', function(e) {
	e.preventDefault();
	var link = $(this);
	new Messi(lang['dist_mrkdel_confirm'], {modal: true, buttons: [{id: 0, label: 'Sim', val: 'S'}, {id: 1, label: 'Não', val: 'N'}], callback: function(val) { if(val=='S') delete_marker_map(link); }});
	return false;
	}); // delete
});

function delete_marker( btn ) {
	$.ajax({
		url         : site_root + 'map/delete_marker/' + btn.data('marker_id'),
		contentType    : 'charset=utf-8',
		dataType : 'json',
		success : function (data) {
			if (data.status === "success") {
				new Messi( lang['dist_mrkdel_ok'], {title: lang['success'], titleClass: 'success', modal: true, buttons: [{id: 0, label: 'OK', val: 'S'}], callback: function(val) { go_home(); } });
			} else {
				new Messi(data.msg, {title: lang['error'], tttleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
			}
		},
		error : function (data, status, e) {
			new Messi( lang['dist_error_mrkdel'], {title: 'Oops...', titleClass: 'anim error', buttons: [{id: 0, label: 'Fechar', val: 'X'}]});
		}
	});
}

$(function() {
	$(document).on('click', '.delete_marker_btn', function(e) {
	e.preventDefault();
	var btn = $(this);
	new Messi( lang['dist_mrkdel_confirm'], {modal: true, buttons: [{id: 0, label: 'Sim', val: 'S'}, {id: 1, label: 'Não', val: 'N'}], callback: function(val) { if(val=='S') delete_marker(btn); }});
	return false;
	}); // delete
});

function refresh_marker_images( marker_id ) {
	$.get(site_root + 'image/list_marker_images/'+marker_id)
		.success(function (data) {
			mrkImagesCount = countOcurrences( data, 'img' );
			$('#images').html(data);
	});
}

function clearInlineLabels(form) {
	$('input[title]').each(function() {
		if($(this).val() === $(this).attr('title')) {
			$(this).val('');
		}
	});	
}

function processInLineLabels() {
	$('input[title]').each(function() {
		if($(this).val() === '') {
			$(this).val($(this).attr('title'));
		}
		
		$(this).focus(function() {
			if($(this).val() == $(this).attr('title')) {
				$(this).val('').addClass('focused');
			}
		});
		$(this).blur(function() {
			if($(this).val() === '') {
				$(this).val($(this).attr('title')).removeClass('focused');
			}
		});
	});
}