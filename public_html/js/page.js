// JavaScript Document
// ソート変更
function chgSort(val,formId) {
  $("#sort").val(val);
  $("#mode").val('sort');
  $("#" + formId).submit();
}

$("#btnWithdrawal").click(function(){
    if(window.confirm('会員を退会された場合には、現在保存されている購入履歴や、お届け先などの情報は、全て削除されますがよろしいでしょうか？')){
        $(location).attr('href','/mypage/withdrawal/enter');
    }
    return false;
})

$(".message_del").click(function(){
    if(window.confirm('メッセージを削除します。\n削除したメッセージは元に戻りません\n削除してもよろしいでしょうか？')){
        url = $(this).attr("href");
        $(location).attr('href',url);
    }
    return false;
})

$(".concern").click(function(){
    url = $(this).attr("href");
		$.ajax({
			type : "GET",
			url : url,
			dataType:"html",
			success: function(request) {
          alert(request);
			}
		});
    return false;
})

$(function() {
    // 商品登録完了モーダル
    var check = $("#checkModal").attr('data-isSuccess');
    if (check == 1) {
        $('[data-remodal-id=item_complete]').remodal().open();
    }

    // 全選択ボタン
    $('#select-delete').on('click', function() {
        if($(".table04 input[type = 'checkbox']").prop('checked') == false){
            $(".table04 input[type = 'checkbox']").prop({'checked':true});
        } else {
            $(".table04 input[type = 'checkbox']").prop({'checked':false});
        }
    });

    $('#item_upload_file').change(function() {
          //値が変更されたときの処理
        var file = $(this).prop('files')[0];
        $('#file-type').text(file['type']);
        $('#file-size').text(file['size']);
        $('#file-regist').text('登録済');
    });
    $('.image_del').on('click', function(){
        location.href = $(this).data('url');
    });
});
