<!-- <div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="error-template">
                <h1>
                    Sorry!</h1>
                <h2>
                    Your session is expired.</h2>
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
	<p>Your session is expired. <br>
    Please login to <a href='http://travel.aspiresys.com'>travel.aspiresys.com</a> to access Travel system.</p>
</div>
<footer class="unauthorized-footer">
	Copyright &copy; <?= date("Y"); ?> Aspire Systems
</footer>

<!-- <style>
body { background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAYAAACpSkzOAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABZ0RVh0Q3JlYXRpb24gVGltZQAxMC8yOS8xMiKqq3kAAAAcdEVYdFNvZnR3YXJlAEFkb2JlIEZpcmV3b3JrcyBDUzVxteM2AAABHklEQVRIib2Vyw6EIAxFW5idr///Qx9sfG3pLEyJ3tAwi5EmBqRo7vHawiEEERHS6x7MTMxMVv6+z3tPMUYSkfTM/R0fEaG2bbMv+Gc4nZzn+dN4HAcREa3r+hi3bcuu68jLskhVIlW073tWaYlQ9+F9IpqmSfq+fwskhdO/AwmUTJXrOuaRQNeRkOd5lq7rXmS5InmERKoER/QMvUAPlZDHcZRhGN4CSeGY+aHMqgcks5RrHv/eeh455x5KrMq2yHQdibDO6ncG/KZWL7M8xDyS1/MIO0NJqdULLS81X6/X6aR0nqBSJcPeZnlZrzN477NKURn2Nus8sjzmEII0TfMiyxUuxphVWjpJkbx0btUnshRihVv70Bv8ItXq6Asoi/ZiCbU6YgAAAABJRU5ErkJggg==);}
.error-template {padding: 40px 15px;text-align: center;}
.error-actions {margin-top:15px;margin-bottom:15px;}
.error-actions .btn { margin-right:10px; }
</style> -->
