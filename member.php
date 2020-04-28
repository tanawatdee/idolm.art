<?php

if($_SERVER['REQUEST_METHOD'] === 'POST'):
try{
	$conn = new PDO('mysql:host=localhost;dbname=idolmart_db', 'root', '');
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $conn->prepare("INSERT INTO member VALUES (?,?,?,?,?,?,?,?)");
	$stmt->execute([$_POST["member_code"],$_POST["nickname"],$_POST["birthdate"],$_POST["height"],$_POST["province"],$_POST["like"],$_POST["hobby"],$_POST["pic_file"]]);
}catch(PDOException $e){echo $e->getMessage();}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<?php
		echo "Member Code : ".$_POST["member_code"].'<br>';
		echo "Nickname : ".$_POST["nickname"].'<br>';
		echo "Date-Of-Birth : ".$_POST["birthdate"].'<br>';
		echo "Height : ".$_POST["height"].'<br>';
		echo "Province : ".$_POST["province"].'<br>';
		echo "Like : ".$_POST["like"].'<br>';
		echo "Hobby : ".$_POST["hobby"].'<br>';
		echo "Picture : ".$_POST["pic_file"].'<br>';
	?>
</body>
</html>


<?php else: ?>


<!DOCTYPE html>
<html>
<head>
	<title>Member form</title>
</head>
<body><br><br>
	<h1 style="text-align: center;color: pink">Member form</h1><br><br>
	<form method="post" action="https://idolm.art/welcome/" style="text-align: center;" >
	<h2>
		Member Code : <br>
		<input type="text" name="member_code"><br>
		Nickname : <br>
		<input type="text" name="nickname"><br>
		Date-Of-Birth : <br>
		<input type="date" name="birthdate"><br>
		Height : <br>
		<input type="number" min="100" max="200" name="height"><br>
		Province : <br>
		<input type="text" name="province"><br>
		Like : <br>
		<input type="text" name="like"><br>
		Hobby : <br>
		<input type="text" name="hobby"><br>
		picture : <br>
		<input type="text" name="pic_file"><br>
	</h2>
		<input type="submit">
	</form>
</body>
</html>

<?php endif; ?>
