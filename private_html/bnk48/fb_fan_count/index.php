<?php

// include_once(dirname(__FILE__).'/../../../private_php/api/Facebook/autoload.php');
// $fb = new Facebook\Facebook([
//  'app_id' => '2043330669254065',
//  'app_secret' => 'c447961cdc92b7e1f47cf94ef610a060',
//  'default_graph_version' => 'v2.12',
// ]);
// // echo $fb->get('/me/accounts?fields=access_token', '2043330669254065|c447961cdc92b7e1f47cf94ef610a060');
// $token = 'EAACEdEose0cBAJMgJeewFgXXYpRUQJrLQ0tnnmeR51PWZCRgRZBO94eMIWwDNYrb7HyVbEY9QbghNEpjn9gjYSgeQE3V5MQPbtheMUVD187IqLaNTBkmKVrcHsVMExhT4kvvzg90rQylpq4sfb06InCBt490qwuSWgwF0aW5NoogPfWlhvhNshmF4HL7ZBAOksnyCancAZDZD';
// $fb_page_list = [
//   "bnk48official.pun",
//   "bnk48official.cherprang",
//   "bnk48official.orn",
//   "bnk48official.noey",
//   "bnk48official.jennis",
//   "bnk48official.music",
//   "bnk48official.kaimook",
//   "bnk48official.mobile",
//   "bnk48official.kaew",
//   "bnk48official.satchan",
//   "bnk48official.tarwaan",
//   "bnk48official.namneung",
//   "bnk48official.pupe",
//   "bnk48official.can",
//   "bnk48official.namsai",
//   "bnk48official.miori",
//   "bnk48official.mewnich",
//   "bnk48official.jane",
//   "bnk48official.korn",
//   "bnk48official.mind",
//   "bnk48official.maysa",
//   "bnk48official.nink",
//   "bnk48official.jaa",
//   "bnk48official.piam",
//   "bnk48official.izutarina",
//   "bnk48official.fond",
//   "bnk48official.jib",
//   "bnk48official.ratah",
//   "bnk48official.kate",
//   "bnk48official.june",
//   "bnk48official.bamboo",
//   "bnk48official.cake",
//   "bnk48official.panda",
//   "bnk48official.wee",
//   "bnk48official.phukkhom",
//   "bnk48official.kheng",
//   "bnk48official.new",
//   "bnk48official.niky",
//   "bnk48official.natherine",
//   "pakwan.bnk48official",
//   "bnk48official.stang",
//   "bnk48official.oom",
//   "bnk48official.aom",
//   "bnk48official.view",
//   "bnk48official.faii",
//   "bnk48official.minmin",
//   "bnk48official.maira",
//   "bnk48official.deenee",
//   "bnk48official.gygee",
//   "bnk48official.myyu",
//   "bnk48official.khamin",
//   "bnk48official.nine",
//   "bnk48official.fifa"
// ];
// $fb_table = [];
// foreach ($fb_page_list as $fb_page) {
//  $fb_table[] = json_decode(@file_get_contents("https://graph.facebook.com/$fb_page?access_token=$token&fields=name,fan_count"), true);
// }
// echo json_encode($fb_page_list, JSON_PRETTY_PRINT);

?>
<div id="chartContainer" style="height: 700px; width: 100%;"></div>
<script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
<script type="text/javascript">
  fb_token = 'EAACEdEose0cBAL1qSoZAReRMSo6c3ZAISy0KjFvNGMXpuCVUyPDCbyLiMKR3ZA4wvQVjzsx6xQm8g65V0NjeqOmDeIZAVFVATp5bQD5xEyiemAxBwERF6QSCRzsZAID73s92Lpb0cybebKA5NUNDx4SE04o8ceeZByFkK5SYRyXYNQqajZB4kiX8jhOsXceRpcZD';
  fb_page_list = [
    // "bnk48official.pun",
    // "bnk48official.cherprang",
    // "bnk48official.orn",
    // "bnk48official.noey",
    // "bnk48official.jennis",
    // "bnk48official.music",
    // "bnk48official.kaimook",
    // "bnk48official.mobile",
    // "bnk48official.kaew",
    // "bnk48official.satchan",
    // "bnk48official.tarwaan",
    // "bnk48official.namneung",
    // "bnk48official.pupe",
    // "bnk48official.can",
    // "bnk48official.namsai",
    // "bnk48official.miori",
    "bnk48official.mewnich",
    // "bnk48official.jane",
    // "bnk48official.korn",
    // "bnk48official.mind",
    // "bnk48official.maysa",
    // "bnk48official.nink",
    // "bnk48official.jaa",
    // "bnk48official.piam",
    // "bnk48official.izutarina",
    "bnk48official.fond",
    // "bnk48official.jib",
    "bnk48official.ratah",
    // "bnk48official.kate",
    "bnk48official.june",
    "bnk48official.bamboo",
    "bnk48official.cake",
    "bnk48official.panda",
    "bnk48official.wee",
    "bnk48official.phukkhom",
    "bnk48official.kheng",
    "bnk48official.new",
    "bnk48official.niky",
    "bnk48official.natherine",
    "pakwan.bnk48official",
    "bnk48official.stang",
    "bnk48official.oom",
    "bnk48official.aom",
    "bnk48official.view",
    "bnk48official.faii",
    "bnk48official.minmin",
    "bnk48official.maira",
    "bnk48official.deenee",
    "bnk48official.gygee",
    "bnk48official.myyu",
    "bnk48official.khamin",
    "bnk48official.nine",
    "bnk48official.fifa"
  ];
  fb_req = [];
  for(i in fb_page_list){
    fb_req.push($.ajax({
      url: 'https://graph.facebook.com/'+fb_page_list[i]+'?access_token='+fb_token+'&fields=name,fan_count',
      dataType: 'json'
    }));
  }

  function findMinMax(arr) {
    let min = arr[0].fan_count, max = arr[0].fan_count;

    for (let i = 1, len=arr.length; i < len; i++) {
      let v = arr[i].fan_count;
      min = (v < min) ? v : min;
      max = (v > max) ? v : max;
    }

    return {min: min, max: max};
  }

  window.onload = function () {


    Promise.all(fb_req).then(function(data){
      fb_bind = 2285;
      fb_minmax = findMinMax(data);
      fb_min = fb_minmax.min;
      fb_max = fb_minmax.max;
      fb_hist = [];
      for(i in data){
        data[i].x = Math.floor((data[i].fan_count)/fb_bind);
        fb_hist[data[i].x] = fb_hist[data[i].x]? fb_hist[data[i].x] : {y:0, label: ''};
        fb_hist[data[i].x].y++;
        fb_hist[data[i].x].label += data[i].name + ' ';
      }
      dataPoints = [];
      for(i in fb_hist){
        dataPoints.push({x: parseInt(i)*fb_bind, y: fb_hist[i].y, label: fb_hist[i].label.slice(0, 200)});
      }
      console.log(dataPoints);

      var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        title:{
          text: "กราฟการกระจายยอดไลก์",
          fontSize: 20
        },
        axisX:{
          labelFontSize: 12
        },
        axisY :{
          includeZero: true,
          labelFontSize: 20
        },
        toolTip: {
          shared: true
        },
        legend: {
          fontSize: 15
        },
        data: [{
          type: "splineArea",
          showInLegend: true,
          name: "รุ่น 1",
          indexLabel: "{label}",
          indexLabelFontSize: 20,
          dataPoints: dataPoints
        }]
      });
      chart.render();

    });

  }
</script>
<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>