<script type="text/javascript" src="/assets/js/jquery.js"></script>
<script type="text/javascript" src="/assets/js/fblogin.js"></script>
<script type="text/javascript">
	hash = window.location.hash.split('#')[1].split('&').map(function(x){return x.split('=')});
	access_token = null;
	state = null;
	for(i=0;i<hash.length;i++){
		if(hash[i][0]=='access_token'){
		    access_token = hash[i][1];
		}
		if(hash[i][0]=='state'){
			state = decodeURI(hash[i][1]);
		}
	}
	$.post('/login/', {token: access_token}, function(data){
    	window.location.replace(state);
  	});
</script>