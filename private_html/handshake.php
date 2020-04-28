<?php

$member_list = json_decode('[
	["Cherprang", "Izurina", "Jaa", "Jane", "Jennis", "Jib", "Kaew", "Kaimook", "Kate", "Korn", "Mind", "Miori", "Mobile", "Music", "Namneung", "Namsai", "Nink", "Noey", "Orn", "Piam", "Pun", "Pupe", "Satchan", "Tarwaan"],
	["Aom", "Bamboo", "Cake", "Deenee", "Faii", "Fifa", "Fond", "Gygee", "Juné", "Khamin", "Kheng", "Maira", "Mewnich", "Minmin", "Myyu", "Natherine", "New", "Niky", "Nine", "Oom", "Pakwan", "Panda", "Phukkhom", "Ratah", "Stang", "View", "Wee"]
]', true);
$round_list = json_decode('["09<br>00","10<br>30","12<br>00","13<br>30","15<br>00","16<br>30","<span style=\"color:#f8f9fa;\">18<br>30</span>","09<br>00","10<br>30","12<br>00","13<br>30","15<br>00","16<br>30","<span style=\"color:#f8f9fa;\">18<br>30</span>"]', true);

$s = $_POST['s']?:'';

$iS = 0;
$btn_mem = array();
foreach($member_list as $igen => $gen){
	$btn_mem[$igen] = array();
	foreach($gen as $imem => $mem){
		$btn_mem[$igen][$imem] = $s[$iS++]?true:false;
	}
}

$iS = 54;
$is_bu = array();
foreach ($round_list as $iround => $round) {
	$is_bu[$iround] = $s[$iS++]?false:true;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Handshake | เช้าเย็น BNK48</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=360,initial-scale=1,shrink-to-fit=no">
	<meta property="og:url"                content="https://bnkweek.com/handshake/" />
	<meta property="og:type"               content="article" />
	<meta property="og:title"              content="ตารางจับมือ BNK48 3-4 พฤศจิกายน" />
	<meta property="og:description"        content="1. เลือกเมมเบอร์ 2. แคปตารางไปใช้เลย! สร้างสรรค์โดย เช้าเย็น BNK48 - fb.me/nightdayBNK48" />
	<meta property="og:image"              content="https://bnkweek.com/handshake/handshake_og_wide.jpg" />
	<meta property="og:image:width"        content="1080" />
	<meta property="og:image:height"       content="536" />
	<link rel="shortcut icon" type="image/png" href="/assets/img/nav/logo_pink.png"/>
	<link href="/assets/css/Kanit.css" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="/assets/css/bootstrap.min.css"/>
	<link type="text/css" rel="stylesheet" href="/assets/css/bootstrap-vue.css"/>
	<style type="text/css">
		body,html{
			font-family:Kanit,sans-serif;
			min-height: 100%;
		}
		body{
			background-color: #A2BF8F;
			background-repeat: no-repeat;
			background-size: cover;
			background-position: center;
		}
		table{
			color: #1D68A1;
		}
		th{
			border: solid #1D68A1 1px;
			border-top: 0;
			border-left: 0;
			border-right: 0;
		}
		th:first-child{
			width: 82px;
		}
		th:not(:first-child){
			line-height: 0.9;
			font-size: 12px;
			cursor: pointer;
			width: 20px;
		}
		th:nth-child(8){
			border-right: solid #1D68A1 2px;
		}
		tr:first-child > th:not(:first-child) {
    		color: #f8f9fa;
    	}
		td{
			border: solid #A2BF8F 1px;
		}
		td:first-child{
			text-align: left;
		}
		td:not(:first-child) {
			color: #f8f9fa;
		}
		.div_sel_mem{
			max-width: 340px;
			display: inline-block;
			vertical-align: top;
		}
		.div_tbl_mem{
			display: inline-block;
			vertical-align: top;
		}
		.sel_mem{
		}
		.btn-outline-primary {
		    color: #1D68A1;
		    border-color: #1D68A1;
		    font-weight: 600;
		}
		.btn-outline-primary:hover{
		    color: #1D68A1;
		    border-color: #1D68A1;
			background-color: transparent;
		}
		.btn-primary{
		    border-color: #1D68A1;
		    background-color: #1D68A1;
		}
		.btn-primary:hover {
		    border-color: #1D68A1;
		    background-color: #1D68A1;
		}
		.text-primary{
		    color: #1D68A1!important;
		}
		.bg_bu{
			background-color: #1D68A1;
		}
	</style>
</head>
<body>
	<div id="handshakeApp" class="text-center text-light pt-3">
		<h1><u>ตารางจับมือ</u> <b>BNK48</b> <u>3-4 พฤศจิกายน</u></h1>
		<h3 class="mt-3 mb-0">1. เลือกเมมเบอร์</h3>
		<div class="div_sel_mem mx-3 text-left" v-for="(gen, igen) in member">
			<h6 :key="'gen' + igen" class="mt-3 ml-3"><u>รุ่น {{ igen + 1 }}&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</u></h6>
			<b-btn v-for="(mem, imem) in gen" :key="'mem' + imem" class="sel_mem rounded-0" variant="outline-primary" :pressed.sync="btn_mem[igen][imem]">{{ mem }}</b-btn>
		</div>
		<h3 class="mt-5">2. แคปตารางไปใช้เลย!</h3>
		<div class="div_tbl_mem">
			<hr class="mt-5" style="border-top: 8px solid rgba(0,0,0,.1);box-shadow: rgba(0, 0, 0, 0.1) 0 -3px 10px;">
			<div id="tbl_sel_mem">
				<div style="visibility: hidden;">...........</div>
				<h3 class="mx-3"><u>ตารางจับมือ</u> <b>BNK48</b> <u>3-4 พฤศจิกายน</u></h3>
				<table class="d-inline-block">
					<tr>
						<th>ฉันไปงาน</th>
						<th v-for="(round, iround) in round_list" :key="'round' + iround" @click="reBu(iround)">
							{{ is_bu[iround] ? '✓' : '-' }}
						</th>
					</tr>
					<tr>
						<th>รอบเวลา</th>
						<th v-for="(round, iround) in round_list" :key="'round' + iround" @click="reBu(iround)" v-html="round"></th>
					</tr>
					<tr v-for="(mem, imem) in sel_table" :key="'mem' + imem">
						<td>{{ mem.name }}</td>
						<td v-for="(round, iround) in mem.round" :key="'round' + iround" :class="{'bg_bu': is_bu[iround]&round}">
							{{ is_bu[iround]&round ? mem.lane[iround] : '' }}
						</td>
					</tr>
				</table>
				<div class="mt-3">
					<div class="d-inline-block align-bottom mr-3">
						<div>ทำเองได้ที่เพจ <img src="/static/fblogo.png" height="24" class="mb-2"> เช้าเย็น BNK48</div>
						<div><a href="#handshakeApp" class="text-primary">bnkweek.com/handshake</a></div>
					</div>
					<div class="d-inline-block"><img src="/static/logo_80.png" height="64px;" class="rounded-circle"></div>
				</div>
				<div style="visibility: hidden;">...........</div>
				<div style="visibility: hidden;">...........</div>
			</div>
			<hr style="border-top: 8px solid rgba(0,0,0,.1);box-shadow: rgba(0, 0, 0, 0.1) 0 3px 10px;">
			<div class="mt-5">
				<b-btn variant="primary" class="rounded-0" @click="saveToImg">เซฟรูปลงเครื่อง</b-btn> หรือ 
				<b-btn variant="primary" class="rounded-0" @click="shareToFb">แชร์รูปลง Facebook</b-btn>
			</div>
			<h6 class="mt-3">หรือเก็บลิงก์นี้ไว้</h6>
			<div class="d-inline-block rounded bg-white pb-1">
				<canvas id="qr_hs" style="margin-bottom: -8px;"></canvas><br>
				&nbsp;<a :href="'https://'+url_hs" class="text-dark">{{ url_hs }}</a>&nbsp;
			</div>
		</div>
		<h5 class="mt-5">© 2018 สร้างสรรค์โดย เช้าเย็น BNK48</h5>
		<a target="_blank" href="//fb.me/nightdayBNK48">
			<img src="/static/logo_80.png" height="80px;" class="rounded-circle"><br>
			fb.me/nightdayBNK48
		</a>
		<div class="mb-5"></div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/vue-resource@1.5.1"></script>
	<script src="https://unpkg.com/babel-polyfill@latest/dist/polyfill.min.js"></script>
	<script src="https://unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.js"></script>
	<script src="/static/qr.js"></script>
	<script src="/static/dom-to-image.js"></script>
	<script type="text/javascript">
		member_list = <?=json_encode($member_list)."\n"?>
	  	round_list = <?=json_encode($round_list)."\n"?>
	  	mem_round = {
	  		Aom: 		[0,0,1,0,0,1,0,0,0,1,0,0,1,1],
			Bamboo: 	[0,1,0,0,1,0,1,0,1,0,0,1,0,0],
			Cake: 		[0,0,1,0,0,1,0,0,0,1,0,0,1,1],
			Cherprang: 	[0,1,1,0,1,1,0,0,1,1,0,1,1,1],
			Deenee: 	[1,0,0,1,0,0,1,1,0,0,1,0,0,0],
			Faii: 		[1,0,0,1,0,0,0,1,0,0,1,0,0,1],
			Fifa: 		[0,1,0,0,1,0,1,0,1,0,0,1,0,0],
			Fond: 		[0,1,1,0,1,0,1,0,1,1,0,1,0,0],
			Gygee: 		[0,1,0,0,1,0,0,0,1,0,0,1,0,1],
			Izurina:  	[1,0,0,1,0,0,0,1,0,0,1,0,0,1],
			Jaa: 		[0,0,1,0,0,1,1,0,0,1,0,0,1,0],
			Jane: 		[1,0,1,0,1,0,1,1,0,1,0,1,0,0],
			Jennis: 	[1,1,0,1,1,0,0,1,1,0,1,1,0,1],
			Jib: 		[0,1,0,0,1,0,1,0,1,0,0,1,0,0],
			Juné:  		[1,0,1,0,0,1,0,1,0,1,0,0,1,1],
			Kaew: 		[0,1,1,0,1,1,0,0,1,1,0,1,1,1],
			Kaimook: 	[1,0,1,0,1,0,1,1,0,1,0,1,0,0],
			Kate: 		[0,0,1,0,0,1,1,0,0,1,0,0,1,0],
			Khamin: 	[1,0,0,1,0,0,1,1,0,0,1,0,0,0],
			Kheng:  	[0,1,0,0,1,0,1,0,1,0,0,1,0,0],
			Korn:  		[0,1,0,1,0,1,0,0,1,0,1,0,1,1],
			Maira:  	[0,0,1,0,0,1,1,0,0,1,0,0,1,0],
			Mewnich: 	[1,0,0,1,0,1,0,1,0,0,1,0,1,1],
			Mind: 		[1,0,1,0,1,0,0,1,0,1,0,1,0,1],
			Minmin:  	[0,0,1,0,0,1,0,0,0,1,0,0,1,1],
			Miori:  	[0,1,0,0,1,0,0,0,1,0,0,1,0,1],
			Mobile:  	[0,1,1,0,1,1,1,0,1,1,0,1,1,0],
			Music:  	[1,1,0,1,1,0,1,1,1,0,1,1,0,0],
			Myyu: 		[1,0,0,1,0,0,1,1,0,0,1,0,0,0],
			Namneung:  	[1,0,1,1,0,1,0,1,0,1,1,0,1,1],
			Namsai:   	[1,0,0,1,0,0,0,1,0,0,1,0,0,1],
			Natherine:  [0,1,0,0,1,0,1,0,1,0,0,1,0,0],
			New:  		[0,1,0,1,1,0,1,0,1,0,1,1,0,0],
			Niky:  		[0,1,0,0,1,0,0,0,1,0,0,1,0,1],
			Nine:  		[0,0,1,0,0,1,1,0,0,1,0,0,1,0],
			Nink:   	[0,1,0,0,1,0,0,0,1,0,0,1,0,1],
			Noey:  		[1,0,1,1,0,1,1,1,0,1,1,0,1,0],
			Oom:   		[0,0,1,0,0,1,0,0,0,1,0,0,1,1],
			Orn:  		[1,1,0,1,1,0,0,1,1,0,1,1,0,1],
			Pakwan:  	[1,0,0,1,0,0,1,1,0,0,1,0,0,0],
			Panda:  	[0,1,0,0,1,0,0,0,1,0,0,1,0,1],
			Phukkhom: 	[0,1,1,0,0,1,0,0,1,1,0,0,1,1],
			Piam:  		[1,0,0,1,0,0,1,1,0,0,1,0,0,0],
			Pun:  		[1,0,1,1,0,1,1,1,0,1,1,0,1,0],
			Pupe:  		[0,1,0,1,0,1,0,0,1,0,1,0,1,1],
			Ratah:  	[1,0,0,1,0,0,1,1,0,0,1,0,0,0],
			Satchan:	[0,1,0,1,0,1,0,0,1,0,1,0,1,1],
			Stang:  	[1,0,0,1,0,0,0,1,0,0,1,0,0,1],
			Tarwaan:  	[1,0,1,1,0,1,1,1,0,1,1,0,1,0],
			View:  		[0,0,1,0,0,1,0,0,0,1,0,0,1,1],
			Wee:   		[1,0,0,1,1,0,0,1,0,0,1,1,0,1]
	  	}
	  	mem_lane = {
	  		Aom: 		[9,9,9,9,9,9,'SP',9,9,9,9,9,9,'SP'],
			Bamboo: 	[22,22,22,22,22,22,'SP',22,22,22,22,22,22,'SP'],
			Cake: 		[16,16,16,16,16,16,'SP',16,16,16,16,16,16,'SP'],
			Cherprang: 	[1,1,1,1,1,1,'SP',1,1,1,1,1,1,'SP'],
			Deenee: 	[23,23,23,23,23,23,'SP',23,23,23,23,23,23,'SP'],
			Faii: 		[5,5,5,5,5,5,'SP',5,5,5,5,5,5,'SP'],
			Fifa: 		[23,23,23,23,23,23,'SP',23,23,23,23,23,23,'SP'],
			Fond: 		[8,8,8,8,8,8,'SP',8,8,8,8,8,8,'SP'],
			Gygee: 		[21,21,21,21,21,21,'SP',21,21,21,21,21,21,'SP'],
			Izurina:  	[21,21,21,21,21,21,'SP',21,21,21,21,21,21,'SP'],
			Jaa: 		[17,17,17,17,17,17,'SP',17,17,17,17,17,17,'SP'],
			Jane: 		[7,7,7,7,7,7,'SP',7,7,7,7,7,7,'SP'],
			Jennis: 	[6,6,6,6,6,6,'SP',6,6,6,6,6,6,'SP'],
			Jib: 		[16,16,16,16,16,16,'SP',16,16,16,16,16,16,'SP'],
			Juné:  		[12,12,12,12,12,12,'SP',12,12,12,12,12,12,'SP'],
			Kaew: 		[13,13,13,13,13,13,'SP',13,13,13,13,13,13,'SP'],
			Kaimook: 	[4,4,4,4,4,4,'SP',4,4,4,4,4,4,'SP'],
			Kate: 		[14,14,14,14,14,14,'SP',14,14,14,14,14,14,'SP'],
			Khamin: 	[16,16,16,16,16,16,'SP',16,16,16,16,16,16,'SP'],
			Kheng:  	[14,14,14,14,14,14,'SP',14,14,14,14,14,14,'SP'],
			Korn:  		[3,3,3,3,3,3,'SP',3,3,3,3,3,3,'SP'],
			Maira:  	[19,19,19,19,19,19,'SP',19,19,19,19,19,19,'SP'],
			Mewnich: 	[8,8,8,8,8,8,'SP',8,8,8,8,8,8,'SP'],
			Mind: 		[3,3,3,3,3,3,'SP',3,3,3,3,3,3,'SP'],
			Minmin:  	[6,6,6,6,6,6,'SP',6,6,6,6,6,6,'SP'],
			Miori:  	[17,17,17,17,17,17,'SP',17,17,17,17,17,17,'SP'],
			Mobile:  	[18,18,18,18,18,18,'SP',18,18,18,18,18,18,'SP'],
			Music:  	[24,24,24,24,24,24,'SP',24,24,24,24,24,24,'SP'],
			Myyu: 		[17,17,17,17,17,17,'SP',17,17,17,17,17,17,'SP'],
			Namneung:  	[22,22,22,22,22,22,'SP',22,22,22,22,22,22,'SP'],
			Namsai:   	[18,18,18,18,18,18,'SP',18,18,18,18,18,18,'SP'],
			Natherine:  [2,2,2,2,2,2,'SP',2,2,2,2,2,2,'SP'],
			New:  		[12,12,12,12,12,12,'SP',12,12,12,12,12,12,'SP'],
			Niky:  		[19,19,19,19,19,19,'SP',19,19,19,19,19,19,'SP'],
			Nine:  		[23,23,23,23,23,23,'SP',23,23,23,23,23,23,'SP'],
			Nink:   	[9,9,9,9,9,9,'SP',9,9,9,9,9,9,'SP'],
			Noey:  		[10,10,10,10,10,10,'SP',10,10,10,10,10,10,'SP'],
			Oom:   		[21,21,21,21,21,21,'SP',21,21,21,21,21,21,'SP'],
			Orn:  		[15,15,15,15,15,15,'SP',15,15,15,15,15,15,'SP'],
			Pakwan:  	[14,14,14,14,14,14,'SP',14,14,14,14,14,14,'SP'],
			Panda:  	[5,5,5,5,5,5,'SP',5,5,5,5,5,5,'SP'],
			Phukkhom: 	[11,11,11,11,11,11,'SP',11,11,11,11,11,11,'SP'],
			Piam:  		[9,9,9,9,9,9,'SP',9,9,9,9,9,9,'SP'],
			Pun:  		[20,20,20,20,20,20,'SP',20,20,20,20,20,20,'SP'],
			Pupe:  		[7,7,7,7,7,7,'SP',7,7,7,7,7,7,'SP'],
			Ratah:  	[13,13,13,13,13,13,'SP',13,13,13,13,13,13,'SP'],
			Satchan:  	[4,4,4,4,4,4,'SP',4,4,4,4,4,4,'SP'],
			Stang:  	[19,19,19,19,19,19,'SP',19,19,19,19,19,19,'SP'],
			Tarwaan:  	[2,2,2,2,2,2,'SP',2,2,2,2,2,2,'SP'],
			View:  		[5,5,5,5,5,5,'SP',5,5,5,5,5,5,'SP'],
			Wee:   		[11,11,11,11,11,11,'SP',11,11,11,11,11,11,'SP']
	  	}
		var app = new Vue({
		  el: '#handshakeApp',
		  data: {
		  	member: member_list,
		  	btn_mem: <?=json_encode($btn_mem)?>,
		  	round_list: round_list,
		  	mem_round: mem_round,
		  	mem_lane: mem_lane,
		  	is_bu: <?=json_encode($is_bu)?>,
		  	sel_table: [],
		  	unsel_table: []
		  },
		  watch: {
		  	btn_mem () {
		  		this.render()
		  	},
		  	is_bu () {
		  		this.render()
		  	},
		  	url_hs () {
		  		QRCode.toCanvas(document.getElementById('qr_hs'), this.url_hs, function () {})
		  	}
		  },
		  methods: {
		  	render () {
		  		sel_mem = []
		  		unsel_mem = []
		  		for(igen in member_list){
		  			for(imem in member_list[igen]){
		  				if(this.btn_mem[igen][imem]){
		  					sel_mem.push({
		  						name:  member_list[igen][imem],
		  						round: mem_round[member_list[igen][imem]],
		  						lane:  mem_lane[member_list[igen][imem]]
		  					})
		  				}
		  				else{
		  					unsel_mem.push({
		  						name:  member_list[igen][imem],
		  						round: mem_round[member_list[igen][imem]],
		  						lane:  mem_lane[member_list[igen][imem]]
		  					})
		  				}
		  			}
		  		}
				this.sel_table = this.sortMem(sel_mem)
				this.unsel_table = this.sortMem(unsel_mem)
		  	},
		  	sortMem (mem_sort) {
		  		l = mem_sort.length;
				for(j=l;j>0;j--){
					for(k=1;k<j;k++){
						if(this.compareRound(mem_sort[k-1], mem_sort[k])){
							tmem = mem_sort[k-1];
							mem_sort[k-1] = mem_sort[k];
							mem_sort[k] = tmem;
						}
					}
				}
				return mem_sort
		  	},
		  	compareRound (a, b) {
		  		for(i = 0; i < this.is_bu.length; i++){
					if(!this.is_bu[i])
						continue;
					if(!a.round[i]&&!b.round[i])
						continue;
					if(a.round[i]!=b.round[i])
						return a.round[i] < b.round[i];
					if(a.round.reduce(this.bu_sort,0) == b.round.reduce(this.bu_sort,0))
						continue;
					return a.round.reduce(this.bu_sort,0) > b.round.reduce(this.bu_sort,0)
				}
				return a.round.reduce(this.bu_sort,0) < b.round.reduce(this.bu_sort,0)
		  	},
		  	bu_sort (s,v,i) {
				return s + this.is_bu[i]*v
		  	},
		  	reBu (i) {
		  		Vue.set(this.is_bu, i, !this.is_bu[i])
		  	},
		  	saveToImg () {
		  		domtoimage.toPng(document.getElementById('tbl_sel_mem'), {bgcolor: '#A2BF8F'}).then(function (dataUrl) {
				    lnk = document.createElement('a')
				    lnk.setAttribute('href', dataUrl)
				    lnk.setAttribute('download', 'BNK48handshake.png')
				    lnk.click()
				})
		  	},
		  	shareToFb () {
		  		wFb = window.open('','Handshake','width=626,height=436')
		  		domtoimage.toPng(document.getElementById('tbl_sel_mem'), {bgcolor: '#A2BF8F'}).then(function (dataUrl) {
		  			var xhttp = new XMLHttpRequest()
					xhttp.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							wFb.location.href = 'https://www.facebook.com/dialog/feed?app_id=2043330669254065&link=https://bnkweek.com/handshake/img/'+(this.responseText)+'.png'
						}
					}
					xhttp.open("POST", "/handshake/upToFb.php", true)
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
					xhttp.send("action=upPic&base64="+encodeURIComponent(dataUrl))
		  		})
		  	}
		  },
		  computed: {
		  	url_hs () {
		  		return (
		  			'bnkweek.com/hs/' + 

		  			this.btn_mem.reduce((s,v)=>s+v.reduce((s,v)=>s+(v?'1':'0'),''),'').match(/.{1,6}/g).map(x=>parseInt((x+'000000').slice(0,6),2)).map(
						x=> x<26?x+65:(x<52?x+71:(x<62?x-4:(x<63?64:38)))
					).map(x=>String.fromCharCode(x)).join('') +
					
					this.is_bu.reduce((s,v)=>s+(v?'0':'1'),'').match(/.{1,6}/g).map(x=>parseInt((x+'000000').slice(0,6),2)).map(
						x=> x<26?x+65:(x<52?x+71:(x<62?x-4:(x<63?64:38)))
					).map(x=>String.fromCharCode(x)).join('')
				).replace(/^|A+$/g, '')
		  	}
		  },
		  mounted () {
		  	this.render()
		  	QRCode.toCanvas(document.getElementById('qr_hs'), this.url_hs, function () {})
		  }
		})
	</script>
</body>
</html>