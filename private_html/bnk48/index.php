<?php

//http_response_code(404);
//header("Location: /fb_fan_count/");

?>
<?php
if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != 'admincpe' || $_SERVER['PHP_AUTH_PW'] != 'CPE231admin') {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authentication Failed';
    exit;
}

$con = mysqli_connect("localhost", "idolmart_cpe", "CPE231db", "idolmart_db");
if(mysqli_connect_errno()){
  http_response_code(500);
  exit;
}

$date = date("Y-m-d");
$result = mysqli_query($con,"SELECT member_code,DATEDIFF('$date',`birthdate`) / 365.25 as age FROM member");
$result = mysqli_fetch_all($result, MYSQLI_ASSOC);
$dataPoints1 = array();
foreach($result as $dp){
    $dataPoints1[] = ["y"=>$dp['age'], "label"=>$dp['member_code']];
}

$result = mysqli_query($con,"SELECT YEAR(birthdate) birthyear, COUNT(member_code) n FROM member GROUP BY YEAR(birthdate)");
$result = mysqli_fetch_all($result, MYSQLI_ASSOC);
$dataPoints2 = array();
foreach($result as $dp){
    $dataPoints2[] = ["x"=>$dp['birthyear'], "y"=>$dp['n']];
}

$result = mysqli_query($con,"SELECT tag_name, total_price, YEAR(birthdate) byear FROM (SELECT tag_name, SUM(total_price) total_price FROM (SELECT product_code, SUM(amount*price) total_price FROM order_product GROUP BY product_code) T1 JOIN (SELECT * FROM tag_product WHERE tag_name IN (SELECT member_code FROM member)) T2 USING(product_code) GROUP BY tag_name) T3 LEFT JOIN member ON member.member_code = T3.tag_name");
$result = mysqli_fetch_all($result, MYSQLI_ASSOC);
$dataPoints3 = array();
foreach($result as $dp){
    $dataPoints3[] = ["x"=>$dp['byear'], "y"=>$dp['total_price']];
}

$result = mysqli_query($con,"SELECT tag_name, total_price, YEAR(birthdate) byear, total_price/amount avg_price FROM (SELECT tag_name, SUM(total_price) total_price, SUM(amount) amount FROM (SELECT product_code, SUM(amount*price) total_price, SUM(amount) amount FROM order_product GROUP BY product_code) T1 JOIN (SELECT * FROM tag_product WHERE tag_name IN (SELECT member_code FROM member)) T2 USING(product_code) GROUP BY tag_name) T3 LEFT JOIN member ON member.member_code = T3.tag_name");
$result = mysqli_fetch_all($result, MYSQLI_ASSOC);
$dataPoints4 = array();
foreach($result as $dp){
    $dataPoints4[] = ["y"=>$dp['avg_price'], "label"=>$dp['tag_name']];
}

$result = mysqli_query($con,"SELECT member_code, COUNT(member_code) AS appearance FROM `unit_member`GROUP BY member_code");
$result = mysqli_fetch_all($result, MYSQLI_ASSOC);
$dataPoints5 = array();
foreach($result as $dp){
    $dataPoints5[] = ["y"=>$dp['appearance'], "label"=>$dp['member_code']];
}

$result = mysqli_query($con,"SELECT COUNT(member_code) n_mem, n n_app FROM (SELECT member_code, COUNT(unit_id) n FROM unit_member GROUP BY member_code) T1 GROUP BY n");
$result = mysqli_fetch_all($result, MYSQLI_ASSOC);
$dataPoints6 = array();
foreach($result as $dp){
    $dataPoints6[] = ["x"=>$dp['n_app'], "y"=>$dp['n_mem']];
}

$result = mysqli_query($con,"SELECT * FROM (SELECT unit_id, YEAR(FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(birthdate)))) avg_birth FROM (SELECT * FROM unit_member) T1 LEFT JOIN member USING(member_code) GROUP BY unit_id) T2 LEFT JOIN unit USING(unit_id)");
$result = mysqli_fetch_all($result, MYSQLI_ASSOC);
$dataPoints7 = array();
foreach($result as $dp){
    $dataPoints7[] = ["y"=>2018-$dp['avg_birth'], "label"=>$dp['name']];
}

$result = mysqli_query($con,"SELECT `datetime` FROM event");
$result = mysqli_fetch_all($result, MYSQLI_ASSOC);
$dataPoints8 = [ [0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0]];
foreach($result as $dp){
    $dataPoints8[(int)(date('H',strtotime($dp['datetime']))/6)][(int)date('w',strtotime($dp['datetime']))]++;
}

$result = mysqli_query($con,"SELECT COUNT(event_id) nEvent, n nMember FROM (SELECT event_id, COUNT(member_code) n FROM event_member GROUP BY `event_id`) T1 GROUP BY n");
$result = mysqli_fetch_all($result, MYSQLI_ASSOC);
$dataPoints9 = [];
foreach($result as $dp){
    $dataPoints9[] = ['x'=>$dp['nMember'], 'y'=>$dp['nEvent']];
}

$result = mysqli_query($con,"SELECT member_code, province FROM member");
$result = mysqli_fetch_all($result, MYSQLI_ASSOC);
$province = [
'Bangkok'=>'C',
'Chiang Mai'=>'N',
'Samut Prakan' => 'C',
'Lamphun'=>'N',
'Prachuap Khiri Khan'=>'S',
'Saitama, Japan'=>'J',
'Pathum Thani'=>'C',
'Petchaburi'=>'C',
'Lopburi'=>'C','Chonburi'=>'E',
'Phayao'=>'N',
'Khon Kaen'=>'NE',
'Nakhon Ratchasima'=>'NE',
'Ibaraki, Japan'=>'J',
'Sing Buri'=>'C',
'Nakhon Sawan'=>'C',
'Samut Sakorn'=>'C',
'Sakon Nakhon'=>'NE',
'Nakhon Pathom'=>'C',
'Saraburi'=>'C',
'Chiang Rai'=>'N',
'Nonthaburi'=>'C'];
$dataPoints10 = ['N'=>0,'NE'=>0,'E'=>0,'S'=>0,'C'=>0,'J'=>0];
foreach($result as $dp){
    $dataPoints10[$province[$dp['province']]]++;
}
$dataPoints10 = [
    ['label'=>'Northern', 'y'=>$dataPoints10['N']],
    ['label'=>'Eastern', 'y'=>$dataPoints10['E']],
    ['label'=>'Southern', 'y'=>$dataPoints10['S']],
    ['label'=>'Central', 'y'=>$dataPoints10['C']],
    ['label'=>'Japan', 'y'=>$dataPoints10['J']],
    ['label'=>'North-Eastern', 'y'=>$dataPoints10['NE']],
];

?>
<!DOCTYPE HTML>
<html>
<head>
<script>
window.onload = function () {


var chart1 = new CanvasJS.Chart("chartContainer1", {
        animationEnabled: true,
        theme: "light2",
        title:{
            text: "Member's Age Analysis"
        },
        axisY: {
            title: "Age"
        },
        data: [{
            type: "column",
            yValueFormatString: "#,##0.## years",
            dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
        }]
    });


var chart2 = new CanvasJS.Chart("chartContainer2", {
	animationEnabled: true,
	theme: "light2",
	title:{
		text: "Age Distribution"
	},
	axisX: {
		//valueFormatString: "DD MMM"
	},
	axisY: {
		title: "Total Number of Members",
		maximum: 10
	},
	data: [{
		type: "splineArea",
		color: "#6599FF",

		dataPoints: <?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>
	}]
});

var chart3 = new CanvasJS.Chart("chartContainer3", {
	animationEnabled: true,
	exportEnabled: true,
	theme: "light1",
	title:{
		text: "Age - Product sales Correlation"
	},
	axisX:{
		title: "Year",
		//suffix: " kg"
	},
	axisY:{
		title: "Product Sales",
		//suffix: " inch",
		includeZero: false
	},
	data: [{
		type: "scatter",
		markerType: "square",
		markerSize: 10,
		//toolTipContent: "Height: {y} inch<br>Weight: {x} kg",
		dataPoints: <?php echo json_encode($dataPoints3, JSON_NUMERIC_CHECK); ?>
	}]
});

var chart4 = new CanvasJS.Chart("chartContainer4", {
	animationEnabled: true,
	theme: "light2",
	title:{
		text: "Average Product Price"
	},
	axisY: {
		title: "Average Product Price"
	},
	data: [{
		type: "column",
		//yValueFormatString: "#,##0.## tonnes",
		dataPoints: <?php echo json_encode($dataPoints4, JSON_NUMERIC_CHECK); ?>
	}]
});

var chart5 = new CanvasJS.Chart("chartContainer5", {
        animationEnabled: true,
        theme: "light2",
        title:{
            text: "Most Appearance Member"
        },
        axisY: {
            title: "Appearance(times)"
        },
        data: [{
            type: "column",
            maximum: 6,
            dataPoints: <?php echo json_encode($dataPoints5, JSON_NUMERIC_CHECK); ?>
        }]
    });

var chart6 = new CanvasJS.Chart("chartContainer6", {
	animationEnabled: true,
	theme: "light2",
	title:{
		text: "Appearance Distribution"
	},
	axisX: {
		//valueFormatString: "DD MMM"
	},
	axisY: {
		title: "Total Number of Members",
		maximum: 10
	},
	data: [{
		type: "splineArea",
		color: "#6599FF",

		dataPoints: <?php echo json_encode($dataPoints6, JSON_NUMERIC_CHECK); ?>
	}]
});

var chart7 = new CanvasJS.Chart("chartContainer7", {
        animationEnabled: true,
        theme: "light2",
        title:{
            text: "Average Age Of Units"
        },
        axisY: {
            title: "Age",
            includeZero: false
        },
        data: [{
            type: "column",
            //yValueFormatString: "#,##0.## years",
            dataPoints: <?php echo json_encode($dataPoints7, JSON_NUMERIC_CHECK); ?>
        }]
    });

var chart9 = new CanvasJS.Chart("chartContainer9", {
	animationEnabled: true,
	theme: "light2",
	title:{
		text: "Frequency of number of Members"
	},
	axisX: {
		//valueFormatString: "DD MMM"
	},
	axisY: {
		title: "Total Number of Events"
	},
	data: [{
		type: "splineArea",
		color: "#6599FF",

		dataPoints: <?php echo json_encode($dataPoints9, JSON_NUMERIC_CHECK); ?>
	}]
});

var chart10 = new CanvasJS.Chart("chartContainer10", {
        animationEnabled: true,
        theme: "light2",
        title:{
            text: "Province Frequency"
        },
        axisY: {
            title: "Number of members",
            includeZero: false
        },
        data: [{
            type: "column",
            //yValueFormatString: "#,##0.## years",
            dataPoints: <?php echo json_encode($dataPoints10, JSON_NUMERIC_CHECK); ?>
        }]
    });

chart1.render();
chart2.render();
chart3.render();
chart4.render();
chart5.render();
chart6.render();
chart7.render();
chart9.render();
chart10.render();
}
</script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
<style type="text/css">
    body>*{
        margin-left: auto;
        margin-right: auto;
    }
</style>
</head>






<body class="text-center pb-3">
<a href="/admin.php">Dashboard</a>
<div id="chartContainer1" style="height: 370px; width: 50%;"></div>
<div id="chartContainer2" style="height: 370px; width: 50%;"></div>
<div id="chartContainer3" style="height: 370px; width: 50%;"></div>
<div id="chartContainer4" style="height: 370px; width: 50%;"></div>
<div id="chartContainer5" style="height: 370px; width: 50%;"></div>
<div id="chartContainer6" style="height: 370px; width: 50%;"></div>
<div id="chartContainer7" style="height: 370px; width: 50%;"></div>
<h1>&emsp;&emsp;&emsp;&emsp;&emsp;Event HeatMap Date-Time</h1>
<div id="chartContainer8" style="height: 370px; width: 50%;"></div>
<div id="chartContainer9" style="height: 370px; width: 50%;"></div>
<div id="chartContainer10" style="height: 370px; width: 50%;"></div>

<?php

echo '<h1>&emsp;Average Purchase</h1>';
$result = mysqli_query($con,"SELECT AVG(total) avgPurchase FROM (SELECT customer_id, SUM(total) total FROM (SELECT SUM(price * amount) total,`order_code`, customer_id FROM `order_product` LEFT JOIN `order` USING(order_code) WHERE `status` != 'FAIL' GROUP BY `order_code`) T_1 GROUP BY customer_id) T_2");
echo "<table border='1'>
<tr>
<th>Average purchase(baht)</th>
</tr>";

while($row = mysqli_fetch_array($result))
{
echo "<tr>";
echo "<td>" . $row['avgPurchase'] . "</td>" ;
echo "</tr>";
}
echo "</table>";

echo '<h1>&emsp;Best Seller</h1>';
$result = mysqli_query($con,"SELECT product_code,Sumamount,total_sales FROM (SELECT SUM(amount) as Sumamount, SUM(amount*price) as total_sales, product_code FROM order_product GROUP BY product_code) T_1 WHERE Sumamount = (SELECT MAX(Sumamount) ms FROM (SELECT SUM(amount) as Sumamount, product_code FROM order_product GROUP BY product_code) T_1)");

echo "<table border='1'>
<tr>
<th>Product code</th>
<th>Highest Amount(baht)</th>
<th>Total Sales(baht)</th>
</tr>";

while($row = mysqli_fetch_array($result))
{
echo "<tr>";
echo "<td>" . $row['product_code'] . "</td>" ;
echo "<td>" . $row['Sumamount'] . "</td>" ;
echo "<td>" . $row['total_sales'] . "</td>" ;
echo "</tr>";
}
echo "</table>";


echo '<h1>&emsp;Price Ranking</h1>';
$result = mysqli_query($con,"SELECT `product_name`,`price` FROM `product` ORDER BY `price` DESC LIMIT 10");

echo "<table border='1'>
<tr>
<th>Product Name</th>
<th>Price(baht)</th>
</tr>";

while($row = mysqli_fetch_array($result))
{
echo "<tr>";
echo "<td>" . $row['product_name'] . "</td>";
echo "<td>" . $row['price'] . "</td>";
echo "</tr>";
}
echo "</table>";
 ?>


<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<script>
var data = [
  {
    z: <?php echo json_encode(array_reverse($dataPoints8), JSON_NUMERIC_CHECK); ?>,
    x: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    y: ['0-5', '6-11', '12-17', '18-23'].reverse(),
    type: 'heatmap'
  }
];

Plotly.newPlot('chartContainer8', data);
</script>
</body>






</html>
