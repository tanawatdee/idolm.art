<?php

$og_img = $_GET['og_img'] ? 'https://idolm.art/assets/img/hs/'.$_GET['og_img'].'.png' : 'https://idolm.art/assets/img/nav/handshake_og_wide.jpg' ;

?>
<!DOCTYPE html>
<html>
<head>
	<title>Handshake</title>
	<meta property="og:url"                content="https://idolm.art<?=$_SERVER['REQUEST_URI']?>" />
	<meta property="og:type"               content="article" />
	<meta property="og:title"              content="ตารางจับมือ BNK48 เดือนสิงหาคม" />
	<meta property="og:description"        content="1. เลือกเมมเบอร์ 2. แคปตารางไปใช้เลย! สร้างสรรค์โดย Idolm.art - fb.me/page.idolm.art" />
	<meta property="og:image"              content="<?= $og_img ?>" />
	<meta property="og:image:width"        content="536" />
	<meta property="og:image:height"       content="536" />
</head>
<body>
	<form id="hs" action="/handshake/" method="post">
		<input type="hidden" name="s">
	</form>
	<script type="text/javascript">
		document.getElementById('hs').elements['s'].value = "<?=$_SERVER['REQUEST_URI']?>".split('/')[2].split('').map(x=>x.charCodeAt(0)).map(x=>('000000'+(x>96?x-71:(x>64?x-65:(x>63?62:(x>47?x+4:63)))).toString(2)).slice(-6)).join('')
		document.getElementById('hs').submit()
	</script>
</body>
</html>