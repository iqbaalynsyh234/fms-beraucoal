var isInitFinished=false;//是否加载完视频插件？？？
var swfwidth=0;//swf插件窗口宽度
var swfheight=0;
var PlateNum=0; //url中车牌号
var chnCount = 4;//默认通道数
var maxChnCount = 16; //最大窗口数
var loginServer = null;
var isLoadLoginServer = false;
var jsession="";
var devIdno="";// 车辆对应设备号
var vehiIdno = ""; //车牌号
var isShowResidualSeconds=false; //是否显示视频播放剩余秒数
var isSelectFilled=false;//加载完通道菜单
var closeSecond = 3*60;//自动关闭视频秒数
var stopIndex = 0;
var serverIp = "";//请求的服务器ip
var serverPort = "";//请求的服务器端口
var isSuccessed="";
var lang = new langZhCn();


function langZhCn() {
	this.loginError = "登陆失败";
	this.jsessionError = "会话号不存在";
	this.deviceNoExist = "找不到车辆信息";
	this.errorVideoDevice = "这个设备不是视频设备";
	this.vehicleNotOperate = "没有车辆或者设备操作权限";
	this.closeTip = "秒后关闭视频";
	this.realtimeVideo = "实时视频";
	this.replayVideo = "历史视频";
	this.fourWindows = "四窗口";
	this.nineWindows = "九窗口";
	this.sixteenWindows = "十六窗口";
	this.monitor = "监听";
	this.talkback = "对讲";
	this.start = "启动";
	this.stop = "停止";
	this.cannotOperate="信息不完整，无法运行！";
	this.isTalking = "已在对讲中";
	this.noMic = "没有找到麦克风";
	this.forbiddenMic = "您禁用了麦克风";
	this.talkback_openMic = "请开启FLASH插件麦克风";
	this.talkback_flashMicStep1 = "第一步，请在视频窗上右键菜单中选择设置"; 
	this.talkback_flashMicStep2 = "第二步在设置窗口中 选择 “允许”，并勾选“记住”"; 
	this.talkback_flashMicStep3 = "关闭设置，并重新启动对讲";
	this.inTheRequest = "请求中……";
	this.running = "运行中……";
	this.haveStopped = "已停止";
	this.monitoring = "监听中……";
	this.talking = "对讲中……";
}

function langEn() {
	this.loginError = "Login failed";
	this.jsessionError = "Jsession error";
	this.deviceNoExist = "Can't not find vehicles";
	this.vehicleNotOperate = "No vehicle or device operating authority";
	this.errorVideoDevice = "This device is not video device";
	this.closeTip = " seconds later close video";
	this.realtimeVideo = "Realtime Video";
	this.replayVideo = "Replay Video";
	this.fourWindows = "four windows";
	this.nineWindows = "nine windows";
	this.sixteenWindows = "sixteen windows";
	this.monitor = "monitor";
	this.talkback = "talkback";
	this.start = "start";
	this.stop = "stop";
	this.cannotOperate="Can not operated since lack of information!";
	this.isTalking = "is already talking";
	this.noMic = "Can't find the microphone!";
	this.forbiddenMic = "The microphone have been forbidden!";
	this.talkback_openMic = "Please open the microphone of the FLASH plug-in";
	this.talkback_flashMicStep1 = "1. select Settings in the video window on the right-click menu"; 
	this.talkback_flashMicStep2 = "The second step in the Settings window select 'Allow' and check the 'Remember'"; 
	this.talkback_flashMicStep3 = "Close the settings and restart the intercom";
	this.inTheRequest = "In The Request";
	this.running = "Running...";
	this.haveStopped = "Have Stopped";
	this.monitoring = "Monitoring";
	this.talking = "talking";
}

function langChange(){
	if (getUrlParameter("lang") == "en") {
		lang = new langEn();
		return false;
	}else{
		return true;
	}
}



$(document).ready(function () {
	langChange();
	$("#closeTip").hide();
	$('#current').next().text(lang.realtimeVideo);
	$('#history').next().text(lang.replayVideo);
	$('#btn1').attr("title",lang.fourWindows);
	$('#btn2').attr("title",lang.nineWindows);
	$('#btn3').attr("title",lang.sixteenWindows);
	$('#monitor').next().text(lang.monitor);
	$('#talkback').next().text(lang.talkback);
	$('#operationBtn').text(lang.start);
	$('input:radio[name="voiceType"],.labelSelection').click(function(){
		var selectType=$('input:radio[name="voiceType"]:checked').val();
		if(selectType=="monitor"){
			$('#monitorChns').attr("disabled",false);
		}else{
			$('#monitorChns').attr("disabled",true);
		}
			
	});
	$('#operationBtn').click(function(){
		var text=$('#operationBtn').text();
		var type=$('input:radio[name="voiceType"]:checked').val();
		if(text==lang.start){
			$('#status').text(lang.inTheRequest);
			if(type=="monitor"){
				startMonitor();
			}else{
				startTalkback();
			}
			//是否成功连接
			if(isSuccessed){
				if(type=="monitor"){
					$('#status').text(lang.monitoring);
				}else{
					$('#status').text(lang.talking);
				}
				$('#operationBtn').text(lang.stop);
				$('input:radio[name="voiceType"],#monitorChns').attr("disabled",true);
			}else{
				$('#operationBtn').text(lang.start);
				$('#status').text("");
				if(type=="monitor"){
					$('input:radio[name="voiceType"],#monitorChns').attr("disabled",false);
				}else{
					$('input:radio[name="voiceType"]').attr("disabled",false);
				}
			}

		}else{
			if(type=="monitor"){
				stopMonitor();
				$('#operationBtn').text(lang.start);
				$('#status').text("");
				$('input:radio[name="voiceType"],#monitorChns').attr("disabled",false);
			}else{
				stopTalkback();
				$('#operationBtn').text(lang.start);
				$('#status').text("");
				$('input:radio[name="voiceType"]').attr("disabled",false);
			}
		}
    });
//	var channel = getUrlParameter("channel");
//	var channelNum=parseInt(channel, 10);
//	if (!isNaN(channelNum)) {
//		chnCount = channelNum;
//	}
	var temp = getUrlParameter("closeMins");
	if (temp) {
		closeSecond = parseInt(temp, 10)*60;
	}
	
	//加载页面
	loadPage();
	getServer();
	//配置页面信息
	$('#btn1').click(function() {
    	doSwitchWindow(4);
    });
	$('#btn2').click(function() {
    	doSwitchWindow(9);
    });
	$('#btn3').click(function() {
    	doSwitchWindow(16);
    });
	$('#videoTitle').val(vehiIdno);
});

//填充通道菜单选项
function fillOptions(){
	for(var i=0;i<chnCount;i++){
		$('#monitorChns').append('<option value="'+i+'">CH'+(i+1)+'</option>');
	}
	isSelectFilled=true;
}

//视频插件要初始化完成后才能调用
//开始监听    startListen(jsession, devIdno, channel, ip, port)
function startMonitor() {
	isSuccessed=false;
	if(isInitFinished && isSelectFilled){
		//获取当前选择通道
		var c=$('#monitorChns option:selected').val();
		//开始监听
	    swfobject.getObjectById("cmsv6flash").startListen(jsession,devIdno, c, serverIp,serverPort);
	    isSuccessed=true;
	}else{
		alert(lang.cannotOperate);
	}
}
//停止监听
function stopMonitor() {
    swfobject.getObjectById("cmsv6flash").stopListen();
}
//视频插件要初始化完成后才能调用
//开始对讲
function startTalkback() {
	isSuccessed=false;
	if(isInitFinished && isSelectFilled){
	    //开始对讲
	    var ret = swfobject.getObjectById("cmsv6flash").startTalkback(jsession,devIdno, 0, serverIp,serverPort);
	    //返回 0成功，1表示正在对讲，2表示没有mic，3表示禁用了mic。
	    if(Number(ret)==1){
	    	$.dialog.tips(lang.isTalking, 2);
	    	isSuccessed=true;
	    	return;
	    }else if(Number(ret)==2){
	    	alert(lang.noMic);
	    	return;
	    }else if(Number(ret)==3){
	    	$.dialog({id:'talkbacktip', title: lang.talkback_openMic, content: 'url:talkbacktipSP.html', min:false, max:false, lock:true
				, autoSize:true});
	    	return;
	    }
	    isSuccessed=true;
	}else{
		alert(lang.cannotOperate);
	}
}

////启动对讲	//0成功，1表示正在对讲，2表示没有mic，3表示禁用了mic
//var ret = ttxVideo.startTalkback(device.getIdno());
//if (ret == 0) {
//	//$("input[name='listing']").attr("disabled", "disabled");
//	this.isTalking = true;
//	$("#talkbackTip").html(lang.requiring);
//	$("#btnTalkback").html(lang.stop);
//} else if (ret == 1) {
//	//一般都不会到此接口
//} else if (ret == 2) {
//	alert(lang.talkback_noMic);
//} else if (ret == 3) {			
//	$.dialog({id:'talkbacktip', title: lang.talkback_openMic, content: 'url:LocationManagement/talkbacktip.html', min:false, max:false, lock:true
//			, autoSize:true});
//} else {
//	//一般都不会到此接口
//	//除非视频插件没有初始化完成
//}

//停止对讲
function stopTalkback() {
    swfobject.getObjectById("cmsv6flash").stopTalkback();
}

function loadPage(){
	setPanelWidth();
	//载入视频插件
	initPlayer();
	//获取车牌号
	vehiIdno=decodeURIComponent(getUrlParameter("PlateNum"));
	if(!vehiIdno ) {//为空
		alert(lang.deviceNoExist);
		return;
	}
	
	login();
}
//如果传入了用户名和密码，则继续加载信息
function login() {
	
	var account = getUrlParameter("account");
	var password = getUrlParameter("password");
	var param = [];
    param.push({name: 'account', value: account});
    param.push({name: 'password', value: password});
    
	doAjaxSubmit('StandardApiAction_login.action', param, function(json, action, success) {
		if(success) {
			//获取到会话号
			jsession = json.jsession;
			//取得会话号则加载视频
			loadDeviceInfo();
		}else {
			alert("登录失败！");
		}
	});
	
	
}


//初始化视频插件
function initPlayer() {
      //视频插件初始化参数
      var params = {
        allowFullscreen: "true",
        allowScriptAccess: "always",
        bgcolor: "#FFFFFF",
        wmode: "transparent"
        };
      //初始化flash
      swfobject.embedSWF("player.swf", "cmsv6flash", swfwidth, swfheight, "11.0.0", null, null, params, null);
      initFlash();
}

//设置flash窗口数量
function loadFlashWindowNum(num) {
	if (num <= 4) {
		swfobject.getObjectById('cmsv6flash').setWindowNum(4);
	} else if (num > 5 && num <= 9) {
		swfobject.getObjectById('cmsv6flash').setWindowNum(9);
	} else {
		swfobject.getObjectById('cmsv6flash').setWindowNum(16);
	}
}
//如果传入设备号，则直接判断设备号；
//如果没传入设备号，则判断传入的车牌号
//根据设备号或者车牌号获取设备信息，并判断是否有效和是否视频设备
//获取设备通道
function loadDeviceInfo() {
	var param = [];
	param.push({name: 'jsession', value: jsession});
	param.push({name: 'vehiIdno', value: vehiIdno});
	this.doAjaxSubmit('StandardApiAction_getVideoDevice.action', param, function(json, action, success) {
		if(success) {
			//不是视频设备
			if(!json.isVideoDevice) {
				alert(lang.errorVideoDevice);
			}else {
				devIdno = json.devIdno;
				chnCount = json.chnCount;
				//播放视频
				previewVideo();
				fillOptions();
			}
		}else {
			//没有操作权限
			if(json) {
				if(json.result == 5) {
					alert(lang.jsessionError);
				}else if(json.result == 8) {
					alert(lang.vehicleNotOperate);
				}else {
					alert(lang.deviceNoExist);
				}
			}else {
				alert(lang.deviceNoExist);
			}
		}
	});
}


//开始预览视频
function previewVideo() {
	//loadFlashWindowNum(chnCount);
	//视频插件初始化完成
	if (isInitFinished && isLoadLoginServer) {
//		//再一次设置flash窗口数量
		//loadFlashWindowNum(maxChnCount);
        loadFlashWindowNum(chnCount);
		if (closeSecond != 0) {
			if(isShowResidualSeconds){
				$("#closeTip").show();		
			}
			setTimeout(closeVideo, 1000);
		}
		
		for (var i = 0; i < chnCount; i++) {
			swfobject.getObjectById('cmsv6flash').setBufferTime(i, 4);
			swfobject.getObjectById('cmsv6flash').setVideoInfo(i, vehiIdno + " - CH" + (i + 1));
			swfobject.getObjectById('cmsv6flash').startVideo(i, jsession, devIdno, i, 1, true);
		}	
	} else {
		setTimeout(previewVideo, 500);
	}
}

function closeVideo() {
//	for (var i = 0; i < 4; ++ i) {
//		swfobject.getObjectById('cmsv6flash').stopVideo(i);
//	}
	if (closeSecond > 0) {
		closeSecond -- ;
		$("#spanCloseSecond").text(closeSecond + lang.closeTip);
		setTimeout(closeVideo, 1000);
	} else {
		$("#closeTip").hide();
		swfobject.getObjectById('cmsv6flash').stopVideo(stopIndex);
		stopIndex ++ ;
		if (stopIndex < chnCount) {
			setTimeout(closeVideo, 50);
		}
	}
}
//视频插件是否加载完成
function initFlash() {
	if (swfobject.getObjectById("cmsv6flash") == null ||
        typeof swfobject.getObjectById("cmsv6flash").setWindowNum == "undefined" ) {
        setTimeout(initFlash, 50);
    } else {
		  //设置视频插件的语言
		  if (getUrlParameter("lang") == "en") {
			  swfobject.getObjectById('cmsv6flash').setLanguage("en.xml");
		  } else {
			  swfobject.getObjectById('cmsv6flash').setLanguage("cn.xml");
		  }
    	  //先将全部窗口创建好
          swfobject.getObjectById("cmsv6flash").setWindowNum(16);
          //再配置当前的窗口数目
	      loadFlashWindowNum(chnCount);
          //设置视频插件的服务器
	      getLoginServer();
	      isInitFinished = true;
//        swfobject.getObjectById("cmsv6flash").setServerInfo("127.0.0.1", "6605");
//	      playAllVideos(num);
    }
}

//播放所有视频
function playAllVideos(num){
	for(i=0;i<num;i++){
		playVideo(i);
	}
	setTimeout("stopAllVideos(num)",3*60*1000);
}

//停止所有视频
function stopAllVideos(num){
	for(i=0;i<num;i++){
		stopVideo(i);
	}
}

//视频插件要初始化完成后才能调用
//播放视频
function playVideo(index) {
  //停止播放视频
  swfobject.getObjectById("cmsv6flash").stopVideo(index);
  //设置视频窗口标题
  swfobject.getObjectById("cmsv6flash").setVideoInfo(index, PlateNum+"-CH"+(index+1));
  //播放视频
  swfobject.getObjectById("cmsv6flash").startVideo(index, jsession , devi, 0, 1, true);
}

//停止播放视频
function stopVideo(index) {
  swfobject.getObjectById("cmsv6flash").stopVideo(index);
}

////重置视频窗口
//function reSetVideo(index) {
//  swfobject.getObjectById("cmsv6flash").reSetVideo(index);
//}


//获取URL参数信息
function getUrlParameter(name){
	if(location.search==''){
		return '';
	}
	
	var o={};
	var search=location.search.replace(/\?/,'');//只替换第一个问号,如果参数中带有问号,当作普通文本
	var s=search.split('&');
	for(var i=0;i<s.length;i++){
		o[s[i].split('=')[0]]=s[i].split('=')[1];
	}
	return o[name]==undefined?'':o[name];
}

//切换窗口数目
function doSwitchWindow(num) {
	swfobject.getObjectById('cmsv6flash').setWindowNum(num);
//	maxChnCount=num;
//	loadDeviceInfo();
}

//提交ajax
function doAjaxSubmit(action, params, callback) {
	$.ajax({
		type: 'POST',
		url: action,
		data: params,
		cache:false,/*禁用浏览器缓存*/
		dataType: 'json',
		success: function (json) {
			if(json.result == 0){
				callback.call(this, json, action, true);
			} else {
				callback.call(this, json, action, false);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			try {
				if (p.onError) p.onError(XMLHttpRequest, textStatus, errorThrown);
			} catch (e) {}
			callback.call(this, null, action, false);
		}
	});
}

/**
 * 判断clientIp与url是否相同，取相同的clientIp  公用方法
 */
function getLoginServer() {
	if(isLoadLoginServer) {
		serverIp = loginServer.ip;
		serverPort = loginServer.port;
		swfobject.getObjectById('cmsv6flash').setServerInfo(serverIp,serverPort);
//		isInitFinished = true;
//		setViewSize();
//		setBufferTime();
//		setPanelWidth();
	}else {
		setTimeout(getLoginServer,50);
	}
}

//获取服务器地址
function getServer() {
	$.myajax.jsonGet('StandardLoginAction_getLoginServer.action', function(json,action,success){
		if(success) {
			var server = JSON.parse(atob(json.loginServer));
			var lstSvrIp = [];
			lstSvrIp.push(server[0].clientIp);
			lstSvrIp.push(server[0].lanip);
			lstSvrIp.push(server[0].clientIp2);
			lstSvrIp.push(server[0].clientIp3);
			loginServer = {};
			loginServer.ip = getComServerIp(lstSvrIp);
			loginServer.port = server[0].clientPort;
			isLoadLoginServer = true;
		} 
	}, null);
}
function setPanelWidth(){
	
//	var _width = document.body.clientWidth;
//	var _height = document.body.clientHeight;
	var _width =min($(window).innerWidth(),document.body.clientWidth,document.documentElement.clientWidth);
	var _height =min($(window).innerHeight(),document.body.clientHeight,document.documentElement.clientHeight) - 39;
	if(_width<600){
		$('body').width(600);
		swfwidth=600-7;
		$('#bottomSelection').width(swfwidth);
	}else{
		$('body').width(_width-8);
		swfwidth=_width-15;
		$('#bottomSelection').width(swfwidth);
	}
	swfheight=_height-39-$('#bottomSelection').height();
	$("#cmsv6flash").width(swfwidth);
	$("#cmsv6flash").height(swfheight);
	
}
function min(a,b,c){
	var i=a;
	if(a>b){
		i=b;
	}else if(i>c){
		i=c
	}
	return c;
}
