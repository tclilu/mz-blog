// 退出登录
function logout() {
    //询问框
    layer.confirm('确定要退出吗？', {btn: ['确定','取消'],icon: 3}, function(){
        $.get('logout',function (response) {
            if (response.code == 2004){
                layer.msg(response.msg,{icon: 1});
                setTimeout(function () {
                    window.location.href='login';
                },1000);
            } else {
                layer.msg(response.msg,{icon: 2});
            }
        },'json');
    }, function(){
        layer.msg('操作取消', {
            time: 1000, //1s后自动关闭
        });
    });
}