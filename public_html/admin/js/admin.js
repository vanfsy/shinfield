// JavaScript Document
function delConfirm(url){
    if(window.confirm('削除したデータは元に戻りません\n削除しても宜しいでしょうか？')){
        $(location).attr('href',url);
    }
    return false;
}

function setEditData(id){
    p_comment_date = $('#comment_date_' + id).html()
    p_comment = $('#comment_' + id).html()

    $('#whatsnews_id').val(id);
    $('#comment_date').val(p_comment_date);
    $('#whatsnews_comment').val(p_comment);
}

$(function() {
    // 全選択ボタン
    $('#select-delete').on('click', function() {
        if($(".table input[type = 'checkbox']").prop('checked') == false){
            $(".table input[type = 'checkbox']").prop({'checked':true});
        } else {
            $(".table input[type = 'checkbox']").prop({'checked':false});
        }
    });
});

$(function() {
    if ($.fn.datepicker) {
	$('#comment_date').datepicker({
		format: 'yyyy/mm/dd',
		language: 'ja',
		autoclose: true,
		clearBtn: true,
//		clear: "閉じる"
	});

	$('.date_input').datepicker({
		format: 'yyyy/mm/dd',
		language: 'ja',
		autoclose: true,
		clearBtn: true,
//		clear: "閉じる"
	});
    }
}); 

