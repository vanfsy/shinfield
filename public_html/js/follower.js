// JavaScript Document
function notlogin(){
    alert('フォロワーの追加はログインしてから出来ます');
}

function add_follow(member_id){
    $.ajax({
        type: 'GET',
        url: '/mypage/follower_add/' + member_id,
        dataType: 'text',
        success: function(response){
            alert('フォロワーに追加しました');
            return response;
        }
    });
}

