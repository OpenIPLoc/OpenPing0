<?php
include_once(__DIR__.'/utils.php');
?><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="referrer" content="unsafe-url">
    <script src="/static/js/vue.min.js"></script>
    <script src="/static/js/axios.min.js"></script>
    <link rel="icon" href="/favico.ico?v=1475298169" type="image/x-icon" />
    <title><?php echo $concurrentIpAddr; ?>-高精度IP地址归属地查询-IP风控值查询-原生IP查询-IP类型查询-家庭宽带IP查询-全球小鸡监控平台</title>
    <meta name="description" content="<?php echo $concurrentIpAddr.'-'.$asnNum.'-'.$asnName.'-'.$asnDomain; ?>">
    <style type="text/css">
        html, body{
            margin: 0;
            padding: 0;
            background: #ebebeb url('/static/images/light.png');
            overflow: auto;
        }
        body{
            position: relative;
        }
        #top{
            background: #00050a;
            color: #eee;
            font-size: 18px;
            display: flex;
            align-items: center;
        }
        #top>.container{
            display: flex;
            justify-content: center;
            background: #00050a;
            padding:20px 0;
            width: 1150px;
        }

        .container{
            width: 100%;
            margin: 0 auto;
            min-width: 1200px;
            position: relative;
        }
        #top .title{
            display: flex;
            align-items: center;
            font-size: 28px;
            margin-right: 40px;
        }
        #top .title>a{
            color:#f3e998;
            text-decoration: none;
        }
        #top .title>a>span{
            margin-left: 2px;
            color:#B28349;
        }
        #top>.container>.menu{
            display: none;
            position: absolute;
            top: 10px;
            right: 10px;
        }
        #top>.container>.menu>img{
            width: 32px;
        }
        .tabs{
            display: flex;
            font-size: 14px;
        }
        .tabs>a,.tabs>div,#top .multimenu>.submenu>a{
            padding: 10px;
            margin-left: 30px;
            display: block;
            color:#f0f0f0;
            text-decoration: none;
        }
        .tabs>a:hover,#top .multimenu>.submenu>a:hover{
            color: #0096e6;
            cursor: pointer;
        }
        .tabs>.active,#top .multimenu>.submenu>a.active{
            color: #0096e6;
        }
        #top .multimenu{
            position: relative;
        }
        #top .multimenu>.submenu{
            position: absolute;
            left: 0;
            top: 40px;
            width: max-content;
            display: flex;
            flex-direction: column;
            z-index: 10;
            background: #00050a;
            box-shadow: 0 0 2px #666;
        }
        #top .multimenu>.submenu>a{
            margin-left: 0;
            padding: 10px 30px;
        }
        #top>.container>.search{
            margin-left: 50px;
            display: flex;
            align-items: center;
        }
        @media screen and (min-width: 1100px) {
            .container{
                width: 1080px;
            }
        }
        @media screen and (min-width: 1400px) {
            .container{
                width: 1200px;
            }
        }
        @media screen and (max-width: 1300px) {
            #top>.container{
                justify-content: center;
                min-width: 0;
            }
            #top>.container>.menu{
                display: block;
            }
            #top>.container>.search,#top>.container>.tabs{
                display: none;
                flex-direction: column;
                position: absolute;
                background: #333;
                top: 50px;
                right: 10px;
                z-index: 999;
            }
            #top>.container>.tabs>a{
                margin-left: 0;
                padding-left: 30px;
            }
        }
    </style>
</head>
<body>
<div class="top" id="top">
    <div class="container">
        <div class="title"><a href="/">PING<span>0</span></a></div>
        <div class="menu" @click="showmenu">
            <img src="/static/images/menu.png" alt="">
        </div>
        <div class="tabs">
            <a :class="{'active': activeItem == 'ipcheck'}" href="/">
                IP 查询
                <sup style="color:red;margin-left: 2px;"></sup>
            </a>
            <a :class="{'active': activeItem == 'latency'}" href="https://ipyard.com/#download">
                Latency
                <sup style="color:red;margin-left: 2px;">New</sup>
            </a>
            <a :class="{'active': activeItem == 'ping'}" href="https://ipyard.com/#download">
                Ping
            </a>

            <a :class="{'active': activeItem == 'trace'}" href="https://ipyard.com/#download">
                Trace

            </a>

            <div :class="{'active': activeItem == '24hour' || activeItem == '5day' || activeItem == '30day'
            || activeItem == '300day'}" class="multimenu" @mouseenter="showmenuvps=true" @mouseleave="showmenuvps=false">
                小鸡监控 <span style="transform: scale(1.2, 0.5);display: inline-block;margin-left: 4px;">V</span>
                <div class="submenu" v-show="showmenuvps">
                    <a :class="{'active': activeItem == '24hour'}" href="/">
                        近24小时
                    </a>
                    <a :class="{'active': activeItem == '5day'}" href="/">
                        近5天
                    </a>
                    <a :class="{'active': activeItem == '30day'}" href="/">
                        近一个月
                    </a>
                    <a :class="{'active': activeItem == '300day'}" href="/">
                        近一年
                    </a>

                </div>
            </div>

            <a :class="{'active': activeItem == 'asnmon'}" href="/">
                ASN 变化监控
            </a>
            <a :class="{'active': activeItem == 'ipleak'}" href="/">
                IP Leak 检测
            </a>
            <!--
            <a :class="{'active': activeItem == 'port'}" href="/">
                端口检测
            </a>
            -->
            <a :class="{'active': activeItem == 'api'}" href="/">
                API接口
                <sup style="color:red;margin-left: 2px;"></sup>
            </a>
        </div>
        <div class="search" style="display: none;">
            <input type="text" placeholder="输入IP" v-model="ip" @keyup.enter="oncheckip" style="padding: 5px 10px;outline: none;"/>
            <button style="font-size: 12px;padding: 5px 10px;" @click="oncheckip">查询</button>
        </div>
    </div>

</div>
<script type="text/javascript">
    function checkip(ip){
        ip = ip.trim()
        if ((ip.substr(0, 2).toUpperCase() === 'AS' && ip.substr(2).match(/^\d+$/)) || (ip.match(/^\d+$/) && ip < 2147483647)) {
            window.location.href = '/as/'+ip;
        }
        else {
            if (ip.substr(-1) == '.')
                ip = ip.substr(0, ip.length - 1)
            var exp=/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/;
            var reg = ip.match(exp);
            if(reg == null && ip.length != 0){
                if (ip.substr(0, 4) == 'http'){
                    ip = ip.substr(ip.indexOf('//') + 2);
                    if (ip.indexOf('/') != -1){
                        ip = ip.substr(0, ip.indexOf('/'));
                    }
                }
            }
            window.location.href = '/ip/'+ip;
        }

    }
    var apptop = new Vue({
        el: '#top',
        data: {
            ip: '',
            activeItem: 'ipcheck',
            showmenuvps: false,
            topmenu: false,
        },
        methods:{
            oncheckip: function(){
                checkip(this.ip)
            },
            showmenu: function(){
                const tabs = document.querySelector("#top>.container>.tabs").style
                if (tabs.display === 'block')
                    tabs.display = 'none'
                else
                    tabs.display = 'block'
            }
        }
    })

    function ipv4cb(ip, location, asn, org)
    {
        axios({
            method:'get',
            url:'/logv6/'+window.ipv6 + '/' + ip
        }).then((res)=> {

        })
    }

    function ipv6cb(ip, location, asn, org)
    {
        window.ipv6 = ip
        var oHead = document.getElementsByTagName("HEAD").item(0);
        var oScript= document.createElement("script");
        oScript.type = "text/javascript";
        oScript.src="/geo/jsonp/ipv4cb";
        oHead.appendChild( oScript);
    }
    function trackad(id) {
        axios({
            method:'get',
            url:'/trackad/'+id
        }).then((res)=> {

        })
    }
</script>

<script src="/static/js/dom-to-image.min.js"></script>
<script src="/static/js/FileSaver.min.js"></script>
<!--
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4408350776179053"
     crossorigin="anonymous"></script>
-->
<style type="text/css">
    html,body{
        background: #f5f6f7;
        font-family: "微软雅黑";
        font-size: 14px;
        overflow: auto;
    }
    #check{
        margin-bottom: 100px;
    }
    #check>.search{
        background: #00050a;
        width: 100%;
        height: 500px;
    }
    #check>.search .input>input{
        width: 700px;
        height: 60px;
        padding: 10px;
        line-height: 60px;
        background: transparent;
        border-radius: 10px;
        outline: none;
        border: 1px solid deepskyblue;
        color: deepskyblue;
        font-size: 25px;
        text-align: center;
        margin-top: 15px;
    }
    #check>.search .op>button{
        width: 100px;
        height: 40px;
        line-height: 40px;
        background: transparent;
        border: 1px solid deepskyblue;
        color: deepskyblue;
        border-radius: 6px;
        font-size: 14px;
        text-align: center;
        cursor: pointer;
        outline: 0;
        margin-top: 15px;
    }
    #check>.search .op>button:hover{
        color: #0096e6;
    }
    #check>.container{
        margin-top: -250px;
    }
    .info{
        border: 1px solid #eee;
        border-top: none;
        margin-top: 10px;
        background: white;
        border-radius: 8px 8px 0 0;
        padding: 2px;
    }
    .info.asninfo{
        margin-top: 10px;
    }
    .info>.title{
        background: linear-gradient(45deg, #e6efff, #fcfaff);
        color: #000;
        margin: 0 -1px 0 -1px;
        padding: 20px;
        text-align: center;
        border-radius: 8px 8px 0 0;
        justify-content: space-between;
    }

    .info>.content{
        font-size: 13px;
        background-color: white;
    }
    .info>.content>.warn-updating{
            background: #fff6f6;
            box-shadow: 0 0 0 1px #e0b4b4 inset, 0 0 0 0 transparent;
            margin: 5px;
            padding: 5px 10px;
            color: #9f3a38;
            display: flex;
            align-items: center;
        }
    .info>.content>.line{
        border-bottom: 1px solid #eee;
        display: flex;
        background: white;
        position: relative;
    }
    .info>.content>.line:nth-child(2n){
        background: #f5f7fb;
    }

    .info>.content>.line>.name{
        width: 200px;
        text-align: center;
        padding: 15px 10px;
        border-right: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .info>.content>.line>.content{
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 15px 10px;
    }
    .info>.content>.line>.content>img{
        width: 16px;
        margin-right: 5px;
    }
    .info>.content>.line>.content .op{
        position:absolute;
        right:20px;
        display: flex;
        justify-content: right;
        align-items: center;
        gap:10px;
        color: #2e67fe;
        user-select: none;
    }
    .info>.content>.line>.content .op>a
    {
        color:#2e67fe;
        text-decoration: none;
        cursor: pointer;
        padding-bottom: 1px;
        font-size: 14px;
        border-bottom: 1px solid #2e67fe;
        transform: scale(0.9);
    }
    .info>.content>.line.loc>.content>.report{
        color:#2e67fe;
        text-decoration: none;
        position:absolute;
        right:20px;
        cursor: pointer;
        padding-bottom: 1px;
        border-bottom: 1px solid #2e67fe;
        transform: scale(0.9);
    }
    .info>.content>.line.line-iptype .label,.info>.content>.line.line-risk .label{
        box-sizing: border-box;
        min-width: 90px;
    }
    .asninfomobile{
        display: none;
    }
    .asninfo{
        border-right: none;
        margin-top: 30px;
        padding: 2px;
        background: #fff;
        border-radius: 10px;
    }
    .asninfo>.title{
        background: linear-gradient(45deg, #e6efff, #fcfaff);
        color: #000;
        padding: 15px;
        text-align: center;
        display: flex;
        border-radius: 8px 8px 0 0;
        font-size: 12px;
    }
    .asninfo>.title>div:first-child{
        display: flex;
        align-items: center;
    }
    .asninfo>.title>div:first-child>img{
        width: 18px;
        height: 12px;
        margin-right: 8px;
    }
    .asninfo>.title>div{
        width: 150px;
    }

    .asninfo>.line{
        font-size: 13px;
    }
    .asninfo>.line{
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        background: white;
        padding: 15px 10px;
        color: #666;
    }

    .asninfo>.line.invalid{
        filter: grayscale(0.5);
        background: #fafafa;
        color: #888;
        font-size: 12px;
    }
    .asninfo>.line:last-child{
        border-radius: 0 0 10px 10px;
    }
    .line.asn a,.line.asn a:visited{
        color: #1E0BED;
        cursor: pointer;
    }
    .line.asnname a,.line.asnname a:visited
    , .line.orgname a,.line.orgname a:visited{
        color: #8E7BaD;
        cursor: pointer;
    }
    .asninfo>.line a, .asninfo>.line.asn a:visited{
        color: #1E0BED;
        cursor: pointer;
    }

    .asninfo>.line>div{
        width: 150px;
        text-align: center;
    }
    .asninfo.prefixes>.title>div:nth-child(3){
        flex: 1;
    }
    .asninfo.orgs>.title>div:nth-child(1){
        flex: 1;
    }
    .asninfo.prefixes>.line>div:nth-child(3){
        flex: 1;
        text-align: left;
    }
    .asninfo.orgs>.line>div:nth-child(1){
        flex: 1;
        text-align: left;
    }
    .asninfo.prefixes>.line>div:first-child{
        display: flex;
        align-items: center;
    }
    .asninfo.orgs>.line>div:first-child{
        display: flex;
        justify-content: left;
        align-items: center;
    }
    .asninfo>.line>div>div{
        margin-right: 5px;
        width: 16px;
        height: 16px;
    }
    .asninfo>.line>div>div.valid{
        background: url('/static/images/yes.png');
        background-size: cover;
    }
    .asninfo>.line>div>div.invalid{
        background: url('/static/images/no.png');
        background-size: cover;
    }
    .asninfo.prefixes>.line>.country{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .asninfo.prefixes>.line>.country>img{
        width: 16px;
        margin-right: 5px;
    }
    .asninfo.otherinfo-switch{
        width: 180px;
        display: flex;
        gap: 10px;
        justify-content: space-between;
        padding: 0;
        border-radius: 5px 5px 0 0;
    }
    .otherinfo-switch>.switch {
        color: #303133;
        padding: 5px 10px;
        cursor: pointer;
        border: 0.2em solid transparent;
        border-bottom: none;
        display: flex;
        align-items: center;
    }
    .otherinfo-switch>.switch:hover{
        color: deepskyblue;
    }
    .otherinfo-switch>.switch.active{
        color: deepskyblue;
        border: 0.2em solid deepskyblue;
        border-bottom: none;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }
    #error_report{
        width: 500px;
        border: 1px solid #000;
        box-shadow: 0 0 10px 5px #fff;
        position: absolute;
        left: calc(50% - 250px);
        top: 500px;
        background: #fff;
        padding: 20px;
        padding-top: 0;
        font-size: 12px;
    }
    #error_report>.title{
        height: 40px;
        border-bottom: 1px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        font-size: 14px;
    }
    #error_report>.close{
        position: absolute;
        right: 15px;
        top: 5px;
        transform: scaleX(1.5);
        cursor: pointer;
    }
    #error_report>.close:hover{
        color: #aaa;
    }
    #error_report .line{
        display: flex;
        justify-content: space-between;
        height: 40px;
        align-items: center;
    }
    #error_report .line input{
        padding: 5px 10px;
        width: 350px;
        outline: none;
        font-size: 12px;
    }
    #error_report>.btnreport{
        padding: 10px 20px;
        border: 1px solid deepskyblue;
        color: deepskyblue;
        margin: 0 auto;
        width: 70px;
        text-align: center;
        margin-top: 20px;
        cursor: pointer;
    }
    #error_report>.btnreport:hover{
        border: 1px solid #008fcf;
        color: #008fcf;
    }
    body #bdmap{
        height: 200px;
        margin-top: 2px;
        resize: vertical;
    }
    .maptitle {
        background: #888;
        color: white;
        padding: 20px;
        text-align: center;
        border-top: 0.2em solid deepskyblue;
        border-radius: 20px 20px 0 0;
        margin-top: 50px;
    }
    .nextip{
        margin-top: 50px;
        display: flex;
        justify-content: space-between;
    }
    .nextip>a{
        background: #fff;
        border-radius: 8px;
        border: 1px solid #2e67fe;
        text-decoration: none;
        overflow: hidden;
        color: #000;
        display: flex;
        align-items: center;
        box-sizing: border-box;
        height: 34px;
    }

    body .asninfo.oterinfo>.title>div{
        width: 100%;
        text-align: left;
    }
    body .asninfo.oterinfo>.line>div{
        width: 100%;
        text-align: left;
    }

    .label{
        padding: 5px 13px;
        color: white;
        border-radius: 3px;
        margin-right: 3px;
        display: inline-block;
        cursor: default;
    }
    .label.mini{
        padding: 3px 8px;
        transform: scale(0.7);
        font-size: 12px;
    }
    .label.tiny{
        padding: 3px 8px;
        transform: scale(0.55);
        font-size: 12px;
    }
    .label.green{
        background: green;
    }
    .label.lightgreen{
        background: limegreen;
    }
    .label.orange{
        background: lightcoral;
    }
    .label.yellow{
        background: orange;
    }
    .label.darkgreen{
        background: seagreen;
    }
    .label.red{
        background: red;
    }

    .riskbar>.riskitem{
        width: 12px;
        height: 12px;
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
    }
    .riskbar>.riskitem.riskcurrent{
        width: 97px;
        height: 26px;
        border: 1px solid #fff;
        border-radius: 3px;
        padding: 0 10px;
        margin: 0 5px;
        cursor: default;
    }
    .riskbar>.riskitem.riskcurrent .lab{
        margin-left: 10px;
    }
    .line-usecount .riskbar>.riskitem.riskcurrent{
        width: auto;
        min-width: 57px;
    }
    .usecountbar{
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2px;
        border-radius: 3px;
        color: white;
        font-size: 12px;
        width: 150px;
        margin: 0 auto;
        position: relative;
        cursor: default;
    }
    .usecountbar[usecount='1 - 10 (极好)']{
        background: limegreen;
    }
    .usecountbar[usecount='10 - 100 (一般)']{
        background: #b5db60;
    }
    .usecountbar[usecount='100 - 1000 (风险)']{
        background: #f7aa1d;
    }
    .usecountbar[usecount='1000 - 10000 (高危)']{
        background: #e06133;
    }
    .usecountbar[usecount='10000+ (极度风险)']{
        background: #f3141d;
    }
    .tipad,.closead{
        position: absolute;
        top: 0;
        right: 0;
        background: rgba(0,0, 0, 0.1);
        color: #fff;
        z-index: 3;
        display: block;
        font-size: 12px;
        transform: scale(0.8);
    }
    .closead{
        left: 0px;
        right: auto;
        cursor: pointer;
        z-index: 4;
    }
    .aditems{
        width: 100%;
        height: 70px;
        display: flex;
        gap: 10px;
        margin: 10px 0;
        color: #888;
    }
    .aditems img{
        border-radius: 10px;
    }
    .websites{
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }
    .websites>a{
        margin-top: 10px;
        overflow:hidden;
        width:130px;
        height: 30px;
    }
    .websites>a>img{
        width: 130px;
        height: 30px;
        border-radius: 3px;
    }
    #ad_right{
        position: absolute;
        display: flex;
        flex-direction:column;
        right: 0;
        margin-right:-265px;
        top:245px;
        width: 260px;
        height: 1200px;
    }
    #ad_right>div{
        width: 260px;
        height: 260px;
    }
    /*
    #ad_right>a{
        border-bottom: 1px solid #aaa;
        background: #f0f0f0 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 260px;
        height: 80px;
    }*/

    @media screen and (max-width: 1919px) {
        .container{
            min-width: 1000px;
            width: 1000px;
        }
    }
    @media screen and (max-width: 1530px) {
        html,body{
            min-width: 1530px;
            overflow-x: auto;
        }
    }

    @media screen and (max-width: 1300px) {
        .container{
            width: 100%;
            min-width: 0px;
        }
        .toprow{
            margin-top: 50px;
            margin-left: 20px;
            margin-right: 20px;
        }
        #check{
            margin-bottom: 10px;
            position: relative;
        }
        #check>.search{
            padding: 0 20px;
            box-sizing: border-box;
        }
        #check>.search>.container{
            padding-top: 30px !important;
        }
        #check>.search .input>input{
            width: 100%;
        }
        #check>.search .op>button{
            width: 100%;
            height: 60px;
            border-radius: 10px;
        }
        #check>.container{
            margin-top: -330px;
        }
        #check .info{
            margin: 0 10px;
        }
        #check>.container>.info>.title>a{
            display: none;
        }
        .asninfo>.line{
            font-size: 12px;
        }
        .info>.content>.line>.name{
            width: 120px;
        }
        .info>.content>.line>.content{
            display: block;
        }
        .info>.content>.line.loc>.content{
            display: flex;
        }
        .info>.content>.line.ip>.content{
            display: flex;
            justify-content: center;
            align-items: center;
            word-break: break-word;
        }
        .info>.content>.line.loc>.content>.report{
            display: none;
        }
        #check .asninfo{
            margin: 0 10px;
            margin-top: 10px;
        }
        .asninfo>.title{
            border-radius: 8px 8px 0 0;
        }
        .asninfo>.title>div:nth-child(1){
            width: 100px;
        }
        .asninfo>.line{
            overflow-x: auto;
        }
        .asninfo>.line>div {
            width: 130px;
        }
        .asninfo.oterinfo>.line>div {
            width: 100%;
        }
        .asninfo.oterinfo>.title>div:nth-child(1){
            width: 100%;
        }
        .asninfo.oterinfo>.line>div {
            width: 100%;
        }
        #check .maptitle{
            margin-left: 10px;
            margin-right: 10px;
            margin-top: 20px;
            border-radius: 8px 8px 0 0;
        }
        #bdmap{
            margin: 0 10px;
        }
        .nextip{
            margin-top: 10px;
            padding: 0 10px;
        }
    }
    @media screen and (max-width: 1200px){
        .aditems{
            margin-left: 10px;
            margin-right: 10px;
            width: auto;
        }
        .toprow{
            margin-top: 50px;
        }
        html,body{
            min-width: auto;
            overflow-x: auto;
        }
        #ad_right{
            display: none;
        }
    }
    @media screen and (max-width: 800px) {
        .ispwhy{
           display: none;
        }
        #websites-wrap{
            display: none;
        }
        #check>.search .input>input{
            height: 50px;
            line-height: 50px;
            font-size: 20px;
            border-radius: 8px;
        }
        #check>.search .op>button{
            height: 50px;
            border-radius: 8px;
        }
        .toprow{
            margin: 20px 10px 10px 10px;
        }
        #check>.container{
            margin-top: -310;
        }
        .info>.content{
            font-size: 12px;
        }
        .info>.content>.line>.name{
            width: 80px;
        }
        .info>.content>.line>.content .op{
            display: none;
        }
        .asninfo.prefixes,.asninfo.orgs,.asninfo.rirs{
            display: none;
        }

        #bdmap{
            margin: 0;
        }
        .nextip{
            display: none;
        }
        .showinvalidwrap{
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .showinvalid{
            background: #cee4ff;
            display: inline-block;
            padding: 2px 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            color: #345587;
        }
        .fielddesc{
            display: none;
        }
        .asninfomobile{
            margin: 10px;
            color: #666;
            font-size: 12px;
            display: block;
            border: 2px solid #fff;
            border-radius: 8px;
        }
        .asninfomobile>.title{
            padding: 15px;
            display: flex;
            align-items: center;
            background: linear-gradient(45deg, #e6efff, #fcfaff);
            color: black;
            border-radius: 8px 8px 0 0;
        }
        .asninfomobile>.title>img{
            width: 18px;
            height: 12px;
            margin-right: 8px;
        }
        .asninfomobile>.item{
            margin-bottom: 10px;
        }
        .asninfomobile>.item.invalid{
            display: none;
            filter: brightness(0.9) grayscale(0.5);
        }
        .asninfomobile>.item.invalid.show{
            display: flex;
        }
        .asninfomobile>.item>.asn{
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            padding: 5px 10px;
            border-radius: 5px 5px 0 0;
        }
        .asninfomobile>.item>.asn>.num{
            color: #666;
            display: flex;
            align-items: center;
        }
        .asninfomobile>.item.valid>.asn>.num{
            color: #000;
            font-weight: bold;
        }
        .asninfomobile>.item>.asn>.num>.valid{
            background: url('/static/images/yes.png');
            background-size: cover;
            margin-right: 5px;
            width: 16px;
            height: 16px;
        }
        .asninfomobile>.item>.asn>.num>.invalid{
            background: url('/static/images/no.png');
            background-size: cover;
            margin-right: 5px;
            width: 16px;
            height: 16px;
        }
        .asninfomobile>.item>.name{
            margin-top: 2px;
            background: #fff;
            padding: 5px 10px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .asninfomobile>.item.valid>.name{
            color: #000;
        }
        .asninfomobile>.item>.name>.label{
            margin-left: -10px;
            margin-right: -5px;
        }
        .asninfomobile>.item>.country{
            padding: 5px 10px;
            background: #fff;
        }
        .asninfomobile>.item>.country img{
            width: 16px;
            margin-right: 5px;
        }
        .asninfomobile>.item>.times{
            background: #fff;
            padding: 5px 10px;
            border-radius: 0 0 5px 5px;
            color: #aaa;
            display: flex;
            align-items: cener;
            justify-content: space-between;
        }
        .asninfomobile.rirs>.item>.country{
            margin-top: 2px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .asninfomobile.rirs>.item>.country>div{
            display: flex;
            align-items: center;
        }
        .asninfo.oterinfo>.line>div{
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        body .asninfo.oterinfo>.title>div{
            text-align: center;
        }
    }

</style>

<div id="check">
    <div class="search">
        <div class="container" style="text-align: center;padding-top: 80px;">
            <div class="location" style="display: flex; justify-content: center; align-items: center;">
                <img src="/static/images/location.png" alt="" height="60" style="height: 28px;width: 22px;" />
                <span style="color: #ddd;font-size: 18px;margin-left: 15px;">IP、域名、ASN</span>
            </div>
            <div class="input">
                <input type="text" placeholder="输入IP / 域名 / ASN" v-model="ip" @keyup.enter="oncheckip">
            </div>
            <div class="op">
                <button @click="oncheckip">查询</button>
        </div>

        </div>
    </div>
    <div class="container">
        <div class="aditems toprow">
            <a rel="noopener nofollow" href="https://www.die.lu" target="_blank" style="display:block; flex: 1;position: relative;" @click="trackad(3)">
                <span class="closead">关闭</span>
                <span class="tipad">广告</span>
                <img src="/static/images/ad/8c0e97165f9193cadf2d90966867b770.gif" width="100%" style="max-height: 70px;">
            </a>
            <a rel="noopener nofollow" href="https://www.cheatfirst.com" target="_blank" style="display:block; flex: 1;position: relative;" @click="trackad(7)">
                <span class="closead">关闭</span>
                <span class="tipad">广告</span>
                <img src="/static/images/ad/1cda98b9d4a5e24751ee79447e5a2148.gif" width="100%" style="max-height: 70px;">
            </a>
            <a rel="noopener nofollow" href="https://www.62v.net" target="_blank" style="display:block; flex: 1;position: relative;" @click="trackad(8)">
                <span class="closead">关闭</span>
                <span class="tipad">广告</span>
                <img src="/static/images/ad/68b1bff33a5e068b1c0a6cbb72.gif" width="100%" style="max-height: 70px;">
            </a>
        </div>
        <div id="ad_left" style="position: absolute;display: none;flex-direction:column;height: 600px;width: 80px;left:0;margin-left: -90px;">
            <div style="display: flex;flex:1;align-items: center;justify-content: center;">
                80 x 300
            </div>
            <div style="display: flex;flex:1;align-items: center;justify-content: center;">
                30 x 300
            </div>
        </div>
        <div id="ad_right">
        <!-- rightside -->
         <!--
        <ins class="adsbygoogle"
            style="display:block"
            data-ad-client="ca-pub-4408350776179053"
            data-ad-slot="5179180574"
            data-ad-format="auto"
            data-full-width-responsive="true"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
        -->
            <!--
            <a rel="noopener nofollow" target="_blank" style="display:block; flex: 1;position: relative;" @click="">
                <a>
                <span class="closead">关闭</span>
                <span class="tipad">广告</span>
                <img src="/static/images/ad/4cdf762904f219b76197c147e0b91cb5.png" width="100%" style="max-height: 100%;">
            </a>
        -->

        </div>
        <div class="info asninfo">
            <div class="title">
                <div>
                    <img src="/static/images/lead.png" alt="">查询结果
                </div>

                <a style="float:right;color:white;cursor: pointer;text-decoration: none;border-bottom: 1px solid deepskyblue;color:#2e67fe;
                border-radius: 5px;border:1px solid #2e67fe;padding: 2px 8px;display: flex;align-items: center;" @click="toimage">
                    <img src="/static/images/jiandao.png" alt="" style="width: 10px;height: 10px;margin-right: 5px;">
                    截图结果
                </a>
            </div>
            <div class="content">
                                <div class="line ip" style="margin:0 -1px 0 -1px;">
                    <div class="name">
                        IP 地址
                    </div>
                    <div class="content">
                        <div class="ip" style="display: flex;justify-content: center;align-items: center;">
                            <span style="margin-right: 5px;display: inline-block;">{{ showip ? ip : '************' }}</span>
                            <img :src="showip ? '/static/images/show.png' : '/static/images/hide.png'" alt="" @click="showip=!showip">
                        </div>

                        <span v-if="rdns.length>0">({{rdns}})</span>

                        <div class="op">
                            <a target="_blank" href="/">ping</a>
                            |
                            <a target="_blank" href="/">trace</a>
                        </div>

                    </div>
                </div>
                <div class="line loc" style="margin:0 -1px 0 -1px;">
                    <div class="name">
                        IP 位置
                    </div>
                    <div class="content">
                        <?php echo $ip_country.' '.$ip_stateOProvince.' '.$ip_city; ?>                        <span class="report" @click="showErrorReport">错误提交</span>
                    </div>
                </div>
                <div class="line asn">
                    <div class="name">
                        ASN
                    </div>
                    <div class="content">
                        <a href="/" target="_blank">AS<?php echo $asnNum; ?></a>
                    </div>
                </div>
                                <div class="line asnname">
                    <div class="name">
                        ASN 所有者
                    </div>
                    <div class="content">
                                                <span class="mini label orange" style="background:limegreen;" title="宽带运营商">ISP</span>
                                                <?php echo $asnName; ?> <span v-if="asndomain.length>0"> — <a :href='asndomain.substr(0,5) === "http:" ? asndomain : "http://"+asndomain' target="_blank">{{asndomain}}</a></span>
                    </div>
                </div>
                                <div class="line orgname">
                    <div class="name">
                        企业
                    </div>
                    <div class="content">
                                                <span class="mini label orange" style="background:limegreen;" title="宽带运营商">ISP</span>
                                                <?php echo $asnCompany; ?> <span v-if="orgdomain.length>0"> — <a :href='orgdomain.substr(0,5) === "http:" ? orgdomain : "http://"+orgdomain' target="_blank">{{orgdomain}}</a></span>
                    </div>
                </div>
                <div class="line">
                    <div class="name">
                        经度
                    </div>
                    <div class="content">
                        <?php echo $ip_longitude; ?>                    </div>
                </div>
                <div class="line">
                    <div class="name">
                        纬度
                    </div>
                    <div class="content">
                        <?php echo $ip_latitude; ?>                    </div>
                </div>
                                <div class="line line-iptype">
                    <div class="name">
                        <span>IP类型</span><a class="fielddesc" style="margin-left: 10px;font-size: 10px;color:deepskyblue;"
                        href="/" target="_blank"
                        title='"IDC机房IP" 特指机房专用的IP，除此外的IP均标记为 "家庭宽带IP"。
关于识别是 IDC机房IP 还是 家庭宽带IP，可以根据上面的 ASN所有者 和 企业是否为ISP来简单的判断。
这也是其它网站所使用的方法，然而这只能说明这个IP的拥有者是ISP，并不代表此IP实际使用在家庭宽带里。
例如国内的IDC服务器IP，大部分都是归属于电信联通移动三大运营商，他们是ISP，但这些IP是用在IDC服务器里的，国外也同理。
要更准确的标识一个IP实际使用在家庭宽带里还是IDC机房里，需要对每个IP段进行标识，这也是 Ping0 所使用的方法。
Ping0 花费了大量的人力和时间对每一个IP段进行了标识，以准确的标识IP实际是用在家庭宽带里还是IDC机房里。
所以有的 IP 在其它网站里显示为ISP，但是 Ping0 里会显示为 IDC ，因为 Ping0 的数据粒度更小，更精确。
综上所述，其它网站只能标识IP的所有者是ISP还是IDC，而 Ping0 能准确识别IP的实际使用者是家庭还是IDC。
'>(说明?)</a>
                    </div>
                    <div class="content">
                                                <span class="label green">家庭宽带IP</span>

                        <a class="ispwhy" href="/" target="_blank"
                        style="color: #2e67fe;
                        text-decoration: none;
                        position: absolute;
                        right: 20px;
                        cursor: pointer;
                        padding-bottom: 1px;
                        border-bottom: 1px solid #2e67fe;transform: scale(0.9);">
                            为什么双ISP还会显示"IDC机房IP"?
                        </a>
                    </div>
                </div>
                                <div class="line line-risk">
                    <div class="name">
                        <span>风控值
                            <a class="fielddesc" style="margin-left: 10px;font-size: 10px;color:deepskyblue;"
                            href="/" target="_blank"
                            title="风控值代表IP的风险程度，值越大，风险越高, 50以内安全，70以上可直接拉黑。
Ping0 通过大数据监测IP是否有扫描，爆破，爬虫，对外攻击，发送垃圾邮件，作为木马病毒的C&C服务等行为，以及危险行为的次数和频率来进行风控打分。
请查看 CentOS 的 /var/log/secure 文件或者 Debian 的 journalctl -u ssh.service 的内容，这里都是一些尝试爆破你SSH密码的风险IP。">(说明?)</a>
                        </span>
                    </div>
                    <div class="content">
                        <div class="riskbar" style="display: flex;justify-content: center;align-items: center;">
                                                        <div class="riskitem riskcurrent" style="background:green" title="0-15 极度纯净"><span class="value">0%</span><span class="lab"> 极度纯净</span></div>
                                                        <div class="riskitem" style="background:limegreen" title="15-25 纯净"></div>
                                                        <div class="riskitem" style="background:#b2dc5e" title="25-40 中性"></div>
                                                        <div class="riskitem" style="background:#dddd00" title="40-50 轻微风险"></div>
                                                        <div class="riskitem" style="background:#ffaa00" title="50-70 稍高风险"></div>
                                                        <div class="riskitem" style="background:red" title="70-100 极度风险"></div>
                                                    </div>
                    </div>
                </div>
                                <div class="line line-nativeip">
                    <div class="name">
                        <span>原生 IP</span>
                        <a class="fielddesc" style="margin-left: 10px;font-size: 10px;color:deepskyblue;"
                        href="/" target="_blank"
                        title="绝大部分的家庭宽带 IP 都属于原生 IP，部分当地的本地 IDC 提供商也会使用原生 IP。
而一些全球性的跨国 IDC 服务商(AWS、GCP、Azure 等)为了方便管理，通常会购买一些大的 IP 段，然后广播到不同的国家, 这些即为 广播 IP。
所以，当一个 IP 显示为 原生 IP 时，通常代表这是一个家庭宽带 IP 或者本地 IDC提供商 IP。
当一个 IP 显示 为广播 IP 时，通常代表这是一个 非家庭宽带 IP，非本地 IDC 提供商 IP。">(说明?)</a>
                        </span>
                    </div>
                    <div class="content">
                                                <?php if($broadcast_status): ?><span class="label orange" style="background: rgb(255, 170, 0);">广播 IP</span><?php else: ?><span class="label orange" style="background:limegreen;">原生 IP</span><?php endif; ?>
                                            </div>
                </div>

                <div class="line line-aicheck">
                    <div class="name">
                        <span>大模型检测</span>
                        <a class="fielddesc" style="margin-left: 10px;font-size: 10px;color:deepskyblue;"
                        href="/" target="_blank"
                        title="使用AI大模型来判断是否为家庭宽带，该模型，基于 qwen2.5-0.5b 模型，使用1000多万条IP数据进行微调训练所得。大模型判断的结果仅供参考，准确率未知, 请自行判断。">(说明?)</a>
                        </span>
                    </div>
                    <div class="content">
                        <div v-if="aicheck" v-html="aicheck"></div>
                        <a class="" @click="doaicheck"
                        style="color: deepskyblue;
                        text-decoration: none;
                        cursor: pointer;
                        padding-bottom: 1px;
                        border-bottom: 1px solid deepskyblue;" v-else>
                            {{ aichecktext }}
                        </a>
                    </div>
                </div>

                <div class="line ipnum" style="margin:0 -1px 0 -1px;">
                    <div class="name">
                        IP 地址(数字)
                    </div>
                    <div class="content">
                        <span style="margin-right: 5px;display: inline-block;width:80px;">{{ showipnum ? ipnum : '********' }}</span>
                        <img :src="showipnum ? '/static/images/show.png' : '/static/images/hide.png'" alt="" @click="showipnum=!showipnum" style="width:auto;">
                    </div>
                </div>

                                <div class="line line-usecount">
                    <div class="name">
                        <span>共享人数
                            <a class="fielddesc" style="margin-left: 10px;font-size: 10px;color:deepskyblue;"
                            href="/" target="_blank"
                            title="该 IP 过去一年的使用人数，基于全网大数据监控的估算值，可以判断该 IP 有多少个人一起使用。">(说明?)</a>
                        </span>
                    </div>
                    <div class="content">
                        <div class="usecountbar" usecount="1 - 10 (极好)" title="1-10(极好), 10-100(一般), 100-1000(风险), 1000-10000(高危), 10000+(极度风险)">
                        1 - 10 (极好)                        </div>
                    </div>
                </div>
                            </div>
        </div>
        <div class="aditems">
            <a rel="noopener nofollow" target="_blank" style="display:block; flex: 1;position: relative;">

            </a>
            <a rel="noopener nofollow" href="/" target="_blank" style="display:block; flex: 1;position: relative;" @click="trackad(5)">
                <span class="closead">关闭</span>
                <span class="tipad">广告</span>
                <img src="/static/images/ad/189722caa53bcad8d2e7c2e0e1048d5f.png" width="100%" style="max-height: 100%;">
            </a>
            <a rel="noopener nofollow" target="_blank" style="display:block; flex: 1;position: relative;">

            </a>
        </div>
        <div class="aditems" style="display: none;">
            <a rel="noopener nofollow" href="" target="_blank" style="display:block; flex: 1;position: relative;" @click="trackad(2)">
            </a>
            <a rel="noopener nofollow" target="_blank" style="display:block; flex: 1;position: relative;">

            </a>
            <a rel="noopener nofollow" target="_blank" style="display:block; flex: 1;position: relative;">

            </a>
        </div>
                <div class="asninfo">
            <div class="title">
                <div>
                    <img src="/static/images/lead.png" alt="">地图定位
                </div>
            </div>
            <div  id="bdmap">

            </div>
        </div>

        <div class="aditems">
            <div rel="noopener nofollow"  target="_blank" style="display:flex; align-items: center;justify-content: center;position: relative;" >

               <!-- <ins class="adsbygoogle"
                    style="display:inline-block;width:320px;height:70px"
                    data-ad-client="ca-pub-4408350776179053"
                    data-ad-slot="4051400348"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>-->
            </div>
            <div rel="noopener nofollow"  target="_blank" style="display:flex; align-items: center;justify-content: center;position: relative;" >
                <!--<ins class="adsbygoogle"
                    style="display:inline-block;width:320px;height:70px"
                    data-ad-client="ca-pub-4408350776179053"
                    data-ad-slot="7771153506">
                </ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>-->
            </div>
            <div rel="noopener nofollow"  target="_blank" style="display:flex; align-items: center;justify-content: center;position: relative;" >
                <!--<ins class="adsbygoogle"
                    style="display:inline-block;width:320px;height:70px"
                    data-ad-client="ca-pub-4408350776179053"
                    data-ad-slot="6217526542"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>-->
            </div>
        </div>
        <div style="border-top: 1px solid #f0f0f0;margin-top: 50px;" id="websites-wrap">
            <p style="height: 24px;line-height: 24px;margin-bottom: 0;margin-left:15px; border-bottom: 1px dashed;padding-bottom: 10px;">网站大全</p>
            <div class="websites">
                <a href="/" target="_blank" rel="noopener nofollow sponsored" @click="trackad(1001)" title="IP资源整段定制，BYO-IP，IPTransit，服务器租用，机柜租用">
                    <img src="/static/images/ad/subset/2fe2b11e6227339155287b02da2fc0b7.png" alt="">
                </a>
                <a href="/" target="_blank" rel="noopener nofollow sponsored" @click="trackad(1002)" title="全球领先的代理IP服务提供商，企业级代理IP解决方案，覆盖全球200多个国家和地区的代理资源。根据您的业务需求，精准定制独享代理IP，拥有极致性价比的服务，平台专注于为企业提供全面、专业、完善的一站式解决方案，全方位满足跨境电商和社交媒体营销的各类需求。全球代理IP日新增50000+纯净住宅IP，高效，稳定，支持免费测试，API/账密一键提取，动静态住宅，数据中心任您选择，支持1V1定制量身打造解决方案，公司售后团队7×24小时保驾护航，支持免费测试。目前服务5000多家公司与个人，获得广泛好评！">
                    <img src="/static/images/ad/subset/2fe2b11e6227339155287b02da2fc0b8.jpg" alt="">
                </a>
                <a href="/" target="_blank" rel="noopener nofollow sponsored" @click="trackad(1003)" title="我们提供从未使用过的、独享代理 IP， 支持静态/动态住宅及数据中心代理，覆盖全球 200+ 国家地区，全天真人客服在线沟通。立即领取 200MB 免费流量，限时福利！">
                    <img src="/static/images/ad/subset/86a8d4cccb728bab9dddce2901e04959.png" alt="">
                </a>
                <a href="/" target="_blank" rel="noopener nofollow sponsored" @click="trackad(1004)" title="Insta IP 提供高性价比的纯净独享静态住宅 IP 和tiktok直播专线，适用于社媒矩阵、店群养号、涨粉引流等多种场景，助力跨境商家拓展全球市场。">
                    <img src="/static/images/ad/subset/417d1d71a680857961d06af7e35bce91.jpg" alt="">
                </a>
                <a href="/" target="_blank" rel="noopener nofollow sponsored" @click="trackad(1005)" title="我们的品牌：IPdodo；主营海外住宅IP和跨境国际专线网络，纯净住宅IP适配tk/fb/ins等平台使用，支持IP直连，提升网络稳定性。">
                    <img src="/static/images/ad/subset/84c1b58718073c9803888cc2555dca58.jpg" alt="">
                </a>
                <a href="/" target="_blank" rel="noopener nofollow sponsored" @click="trackad(1006)" title="Tiktok矩阵管理系统, 专业级人工智能驱动的TikTok账号自动化批量管理平台，实现多账号矩阵式管理与全自动批量化运营，让您的TikTok业务轻松扩展。">
                    <img src="/static/images/ad/subset/5088dd70e71f73cce0354031202a5619.png" alt="">
                </a>
                <a href="/" target="_blank" rel="noopener nofollow sponsored" @click="trackad(1007)" title="全球领先代理IP服务，提供纯净独享的静态住宅IP、数据中心IP、动态住宅7000W代理IP池，高速稳定，免费试用。">
                    <img src="/static/images/ad/subset/6e580e53ef140296260b34167e53ab52.png" alt="">
                </a>
            </div>
        </div>

    </div>
    <div id="error_report" v-show="errorreport" style="display: none;">
        <div class="title">错误提交</div>
        <div class="close" @click="closeErrorReport">X</div>
        <div>
            <div class="line">
                <div>IP地址:</div>
                <div style="color:#aaa;"><?php echo $concurrentIpAddr; ?></div>
            </div>
            <div class="line">
                <div>IP位置:</div>
                <div style="color:#aaa;"><?php echo $ip_country.' '.$ip_stateOProvince.' '.$ip_city; ?></div>
            </div>
            <div class="line line-newaddr">
                <div><span style="color:red;margin-right: 3px;">*</span>正确的IP位置:</div>
                <div><input type="text" v-model="newaddr" placeholder="请输入正确的IP位置" /></div>
            </div>
            <div class="line line-other">
                <div>其它说明:</div>
                <div><input type="text" v-model="otherinfo" placeholder="空"/></div>
            </div>
        </div>
        <div class="btnreport" @click="submitErrorReport">提交</div>
    </div>

    <textarea id="copy" style="width: 1px;height: 1px;opacity: 0;position: absolute;" v-model="copydata" ref="copyele">

    </textarea>
</div>

<script type="text/javascript">
    window.ip = '<?php echo $concurrentIpAddr; ?>'
    window.tar= ''
    window.ipnum = '114514'
    window.asndomain = '<?php echo $asnDomain; ?>'
    window.orgdomain = '<?php echo $asnIPPrefix; ?>'
    window.rdns = ''
    window.longitude = '<?php echo $ip_longitude; ?>'
    window.latitude = '<?php echo $ip_latitude; ?>'
    window.loc = `<?php echo $ip_country.' '.$ip_stateOProvince.' '.$ip_city; ?>`
</script>
<script src="/static/js/check.js"></script>
<script>

    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.closead').forEach(function(element) {
        element.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            this.parentNode.style.visibility = 'hidden';
        });
    })
    var width = document.documentElement.clientWidth
    if (width < 700) {
        const rows = document.querySelectorAll('.aditems');
        rows.forEach(row => {
            const links = row.querySelectorAll('a');
            const validLinks = Array.from(links).filter(link => link.querySelector('img') !== null);
            links.forEach(link => link.style.display = 'none');
            if (validLinks.length > 0) {
                const randomIndex = Math.floor(Math.random() * validLinks.length);
                validLinks[randomIndex].style.display = 'flex';
            }
        });
    }
    else if (width < 1100) {
        const rows = document.querySelectorAll('.aditems');
        rows.forEach(row => {
            const links = row.querySelectorAll('a');
            const validLinks = Array.from(links).filter(link => link.querySelector('img') !== null);
            links.forEach(link => link.style.display = 'none');

            if (validLinks.length >= 2) {
                const shuffled = [...validLinks].sort(() => Math.random() - 0.5);
                shuffled.slice(0, 2).forEach(link => {
                    link.style.display = 'flex';
                });
            } else if (validLinks.length === 1) {
                validLinks[0].style.display = 'flex';
                links[2].style.display = 'flex';
            }
        })
    }
    });
</script>
<style>
    .bottommenu{
        display: none;
    }
    .bottommenu a{
        color: #fff;
        display: block;
        text-align: left;
        text-decoration: none;
        height: 40px;
        line-height: 40px;
    }
    .footer{
        height: 60px;
        line-height: 60px;
    }
    @media screen and (max-width: 1300px){
        .bottommenu {
            display: flex;
            justify-content: space-around;
        }
    }
    @media screen and (max-width: 500px) {
        .footer{
            height: auto;
            line-height: 30px;
        }
    }

</style>
<div class="bottommenu" style="background: #333;width: 100%;color:#f0f0f0;text-align: center;">
    <div>
        <a href="/">
            近24小时
        </a>
        <a href="/">
            近5天
        </a>
        <a href="/">
            近一个月
        </a>
        <a href="/">
            近一年
        </a>
    </div>
   <div>
       <a href="/">
           IP 查询
           <sup style="color:red;margin-left: 2px;">Hot</sup>
       </a>
       <a href="/">
           PING 检测
           <sup style="color:red;margin-left: 2px;">New</sup>
       </a>

       <a href="/">
           TRACE 跟踪
           <sup style="color:red;margin-left: 2px;">New</sup>
       </a>
       <a href="/">
           ASN 变化监控
       </a>
       <a href="/">
           API接口
       </a>
   </div>
</div>
<div class="footer" style="background: #333;width: 100%;color:#f0f0f0;text-align: center;">
    © 2021 - 2025 ping0.ipyard.com 所有权利保留 | 友情链接: https://www.cheatfirst.com | 想把谁IP标记成100%风险联系: risk@ipyard.com
</div>
</body>
</html>