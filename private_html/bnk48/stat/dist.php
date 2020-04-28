<div id="chartContainer" style="height: 700px; width: 100%;"></div>
<script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>
<script type="text/javascript">
  window.onload = function () {

    var chart = new CanvasJS.Chart("chartContainer", {
      animationEnabled: true,
      title:{
        //text: "กราฟการกระจายยอดไลก์",
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
        name: "คอมพ์ 3",
        indexLabel: "{label}",
        indexLabelFontSize: 20,
        dataPoints: getDP()
      }]
    });
    chart.render();
  };

  // function getDP(){
  //   dataPoints = [];
  //   hist = [];
  //   aSet = 26*3;
  //   sCount = 0;
  //   nBuy = 10*5;
  //   nSample = 1000000;
  //   for(i in [...Array(nSample)]){
  //     nSucess = 0;
  //     r = [];
  //     for(j in [...Array(nBuy)]){
  //       r.push(Math.floor(Math.random()*aSet));
  //     }
  //     r = _.countBy(r);
  //     for(j in [...Array(aSet/3)]){
  //       rmin = _.min([r[3*parseInt(j)],r[3*parseInt(j)+1],r[3*parseInt(j)+2]])
  //       nSucess += _.min([r[3*parseInt(j)],r[3*parseInt(j)+1],r[3*parseInt(j)+2],r[3*parseInt(j)]&&r[3*parseInt(j)+1]&&r[3*parseInt(j)+2]?nBuy:0]);
  //     }
  //     hist[nSucess] = hist[nSucess]? hist[nSucess] + 1 : 1;
  //   }
  //   for(i in hist){
  //     dataPoints.push({x: parseInt(i), y: hist[i]*100/nSample});
  //   }
  //   console.log(dataPoints);
  //   return dataPoints;
  // }

  // function getDP(){
  //   dataPoints = [];
  //   hist = [];
  //   aSet = 53;
  //   sCount = 0;
  //   nBuy = 50;
  //   nSample = 100000;
  //   for(i in [...Array(nSample)]){
  //     r = 0;
  //     nSucess = 0;
  //     for(j in [...Array(nBuy)]){
  //       if(Math.random()*aSet < 1){
  //         nSucess++;
  //       }
  //     }
  //     hist[nSucess] = hist[nSucess]? hist[nSucess] + 1 : 1;
  //   }
  //   for(i in hist){
  //     dataPoints.push({x: parseInt(i), y: hist[i]/nSample});
  //   }
  //   console.log(dataPoints);
  //   return dataPoints;
  // }
</script>
<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>