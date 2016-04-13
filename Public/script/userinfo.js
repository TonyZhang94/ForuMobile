$(document).ready(function(){
	$(".revise-sex").click(function(){
		var info = {
			sex:'sex'
		};
		
		$.ajax({
			type:"POST",
			url:"/fuwebapp/index.php/Home/Person/infoSexRevise",
			data:info,
			success:function(res){
				if (res['result'] != 0)	{
					var sex = $(".sex-none").val();

					if (sex != 0) {
						$(".revise-sex .fr").html('男'+'<img class="forward-arrow"  src="/fuwebapp/public/img/icon/forwardarrow.jpg" alt="">');
						$(".revise-sex .sex-none").attr("value","0")
					}
					else {
						$(".revise-sex .fr").html("女"+'<img class="forward-arrow"  src="/fuwebapp/public/img/icon/forwardarrow.jpg" alt="">');
						$(".revise-sex .sex-none").attr("value","1")
					}
				}
				else {
					// 更改性别失败
				}
			}
		})
	});
})

function getInfo(field){
	info = {
		field:field
	};

	$.ajax({
		type:"POST",
		url:"/fuwebapp/index.php/Home/Person/getUserInfo",
		data:info,
		success:function(info){
			if (info['result'] !=0) {
				document.getElementById("revise-"+field).value=info[field];
			}
			else {
				// 获取用户信息失败
			}
		}
	})
}