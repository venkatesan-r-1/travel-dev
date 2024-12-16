<!-- <div class="container">
	<div class="row">
        <div class="col-md-12">
            <div class="error-template">
                <h1>
                    Sorry!</h1>
                <h2>
                    Unauthorised Entry</h2>
                <div class="error-details">
                    Please login to <a href='http://systems.aspiresys.com'>systems.aspiresys.com</a> &rarr;Travel to access Travel system.
                </div>
                           </div>
				
        </div>
    </div>
</div> -->
<link rel="stylesheet" href="{{asset('css/visa_process.css')}}">
<nav class="unauthorized-header">
	<img src="{{asset('images/logo-voilet.svg')}}" alt="">
	<p>Travel System</p>
</nav>
<div class="container-fluid error-template">
	<p>You don't have access to this page <br>
	Click <a href="http://travel.aspiresys.com">here</a> to redirect back to homepage</p>
</div>
<footer class="unauthorized-footer">
	Copyright &copy; <?= date("Y"); ?> Aspire Systems
</footer>
<style>
</style>
<!-- <input type="button" id="close" /> -->
	  		 <script type="text/javascript" src="{{ URL::asset('js/jquery.min.js') }}"></script>

<script >
// var myWindow;
// function openWin() {
//     myWindow = window.open("", "_self", "width=200,height=100");
//     myWindow.document.write("<p>This is 'myWindow'</p>");
// }

// function closeWin() {
//     myWindow.close();
// }
$(document).ready(function(){
// 	//var url= window.location.href;
// 	var url= 'http://www.w3schools.com'; 
// 	var win=window.open(url, '_self','');
// 	console.log(win);
//	 window.close();
$("#close").click();
$("#close").on('click',function(){
	window.close();
	console.log('closed');
});
});

</script>
