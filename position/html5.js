
window.onload = function(){
	$("#createImg").click();
}


/*更新图片的点击事件*/
$("#createImg").click(function(event) {

	var url = 'php.php?'+$('form').serialize();
	$("#phpPos").attr('src', url);//更新php图片。

	var points = [];
	$.each($(".point-group"), function(index, val) {
		var point = {};
		point.Name = $(this).find('input').eq(0).val();
		point.L = parseFloat($(this).find('input').eq(1).val());
		point.B = parseFloat($(this).find('input').eq(2).val());
		points.push(point);
	});

	draw(points);//更新html5图片

	drawMap(points);//更新百度地图图片
});
/*修改坐标点数量*/
$("#createInput").click(function(event) {
	var html='',item='',num='',oldnum,addLen,i;
	num = $.trim($('input[name=num]').val());
	if(num==''){
		return;
	}
	num = parseInt(num);
	oldnum = $(".point-group").length;
	if(oldnum>num){
		$(".point-group:gt("+(num-1)+")").remove();
	}else{
		addLen = num - oldnum;
		for (i = 0; i < addLen; i++) {
			item = '<div class="form-group point-group"><label >坐标点'+(oldnum+i+1)+'：</label> <input name="points['+(oldnum+i)+'][Name]" class="form-control"  placeholder="请输入点名"> <input name="points['+(oldnum+i)+'][L]" class="form-control" placeholder="请输入经度-180至180-180至180"> <input name="points['+(oldnum+i)+'][B]" class="form-control"  placeholder="请输入纬度-90至90"> </div>';
			html+=item;
		}
	}

	$(".form-group:last").before(html);
});

var map;
function drawMap(points){
	if(!map){
		 map = new BMap.Map("mapDiv"); //初始化地图
		 map.enableScrollWheelZoom();//滚轮放大缩小。
		}
	map.centerAndZoom(new BMap.Point(103.388611,35.563611), 5);//设置中心点和显示级别。中国。
	addMarker(points);
}

/*地图上添加坐标点*/   
function addMarker(points){
	map.clearOverlays();
	var point,marker,bpoints=[];
	// 创建标注对象并添加到地图   
	for(var i = 0,pointsLen = points.length;i <pointsLen;i++){

		point = new BMap.Point(points[i].L,points[i].B);
		bpoints.push(point);	
		marker = new BMap.Marker(point);   
		map.addOverlay(marker); 
		//给标注点添加点击事件。使用立即执行函数和闭包
		(function() {
			var thePoint = points[i];
			marker.addEventListener("click",function(){
				showInfo(this,thePoint);
			});
		})();
	}
	map.setViewport(bpoints);
}

/*标注点的信息窗口*/  
function showInfo(thisMaker,point){
	var sContent =point.Name;
	var infoWindow = new BMap.InfoWindow(sContent);// 创建信息窗口对象
   	thisMaker.openInfoWindow(infoWindow);//图片加载完毕重绘infowindow
   }
