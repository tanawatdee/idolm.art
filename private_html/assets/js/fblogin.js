FBLogged = false;
FBresponse = null;

window.fbAsyncInit = function() {
  FB.init({
    appId      : '2043330669254065',
    cookie     : true,
    xfbml      : true,
    version    : 'v2.12'
  });
    
  FB.AppEvents.logPageView();

  FB.getLoginStatus(function(response) {
    FBLogged = (response.status === 'connected');
    FBresponse = FBLogged?response:null;
  });
    
};

(function(d, s, id){
   var js, fjs = d.getElementsByTagName(s)[0];
   if (d.getElementById(id)) {return;}
   js = d.createElement(s); js.id = id;
   js.src = "https://connect.facebook.net/en_US/sdk.js";
   fjs.parentNode.insertBefore(js, fjs);
 }(document, 'script', 'facebook-jssdk'));

function loginFB(redirect){
  if (FBLogged){
    var http = new XMLHttpRequest();
    var url = "/api/fblogin/";
    var params = "token="+FBresponse.authResponse.accessToken;
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.onreadystatechange = function() {
        if(http.readyState == 4 && http.status == 200) {
            location.reload(true);
        }
    }
    http.send(params);
  }
  else{
    window.location.replace('https://www.facebook.com/v2.12/dialog/oauth?client_id=2043330669254065&redirect_uri=https://idolm.art/api/fblogin/&response_type=token&scope=public_profile,email&state=' + encodeURIComponent(window.location.pathname));
  }
}

// function sendToken(response, redirect){
//   $.post('/api/login/', {token: response.authResponse.accessToken}, function(data){
//     window.location = redirect;
//   });
// }

// $('.loginFB_click').unbind('click').click(function(){
//   loginFB(window.location);
// });

// $('.logoutFB_click').unbind('click').click(function(){
//   $.get('/api/login/?logout', function(){
//     window.location = '/';
//   });
// });

// $('.fbname_click').unbind('click').click(function(){
//   window.location = '/profile/';
// });

// $('.logo_click').unbind('click').click(function(){
//   window.location = '/';
// });