<?php
if (!isset($_SESSION['username'])) {
echo "<script>alert('Por Favor, Faça o login primeiro!');</script><script>window.location = 'index.php';</script>";
exit;
}
?>
<div id="left_wrapper">
    <div class="header">
        <h2><span>PB Troll //</span> Comprar Vip</h2>
    </div>
	<link rel="stylesheet" href="css/style.css">
		<div class="pricing-container">
			<div class="pricing-switcher">
				<p class="fieldset">
					<input type="radio" name="duration-1" value="monthly" id="monthly-1" checked>
					<label for="monthly-1">Monthly</label>
					<input type="radio" name="duration-1" value="yearly" id="yearly-1">
					<label for="yearly-1">Yearly</label>
					<span class="switch"></span>
				</p>
			</div>
			<ul class="pricing-list bounce-invert">
				<li>
					<ul class="pricing-wrapper">
						<li data-type="monthly" class="is-visible">
							<header class="pricing-header">
								<h6>Basic</h6>
								<div class="price">
									<span class="value">30.000</span>
								</div>
							</header>
							<div class="pricing-body">
								<ul class="pricing-features">
									<li><em>5</em> Email Accounts</li>
									<li><em>1</em> Template Style</li>
									<li><em>25</em> Products Loaded</li>
									<li><em>1</em> Image per Product</li>
									<li><em>Unlimited</em> Bandwidth</li>
									<li><em>24/7</em> Support</li>
								</ul>
							</div>
							<footer class="pricing-footer">
								<a class="select" href="#">Sign Up</a>
							</footer>
						</li>
						<li data-type="yearly" class="is-hidden">
							<header class="pricing-header">
								<h6>Basic</h6>
								<div class="price">
									<span class="value">320.000</span>
								</div>
							</header>
							<div class="pricing-body">
								<ul class="pricing-features">
									<li><em>5</em> Email Accounts</li>
									<li><em>1</em> Template Style</li>
									<li><em>25</em> Products Loaded</li>
									<li><em>1</em> Image per Product</li>
									<li><em>Unlimited</em> Bandwidth</li>
									<li><em>24/7</em> Support</li>
								</ul>
							</div>
							<footer class="pricing-footer">
								<a class="select" href="#">Sign Up</a>
							</footer>
						</li>
					</ul>
				</li>
				<li class="exclusive">
					<ul class="pricing-wrapper">
						<li data-type="monthly" class="is-visible">
							<header class="pricing-header">
								<h6>Exclusive</h6>
								<div class="price">
									<span class="value">60.000</span>
								</div>
							</header>
							<div class="pricing-body">
								<ul class="pricing-features">
									<li><em>15</em> Email Accounts</li>
									<li><em>3</em> Template Styles</li>
									<li><em>40</em> Products Loaded</li>
									<li><em>7</em> Images per Product</li>
									<li><em>Unlimited</em> Bandwidth</li>
									<li><em>24/7</em> Support</li>
								</ul>
							</div>
							<footer class="pricing-footer">
								<a class="select" href="#">Sign Up</a>
							</footer>
						</li>
						<li data-type="yearly" class="is-hidden">
							<header class="pricing-header">
								<h6>Exclusive</h6>
								<div class="price">
									<span class="value">630.000</span>
								</div>
							</header>
							<div class="pricing-body">
								<ul class="pricing-features">
									<li><em>15</em> Email Accounts</li>
									<li><em>3</em> Template Styles</li>
									<li><em>40</em> Products Loaded</li>
									<li><em>7</em> Images per Product</li>
									<li><em>Unlimited</em> Bandwidth</li>
									<li><em>24/7</em> Support</li>
								</ul>
							</div>
							<footer class="pricing-footer">
								<a class="select" href="#">Sign Up</a>
							</footer>
						</li>
					</ul>
				</li>
				<li>
					<ul class="pricing-wrapper">
						<li data-type="monthly" class="is-visible">
							<header class="pricing-header">
								<h6>Pro</h6>
								<div class="price">
									<span class="value">90.000</span>
								</div>
							</header>
							<div class="pricing-body">
								<ul class="pricing-features">
									<li><em>20</em> Email Accounts</li>
									<li><em>5</em> Template Styles</li>
									<li><em>50</em> Products Loaded</li>
									<li><em>10</em> Images per Product</li>
									<li><em>Unlimited</em> Bandwidth</li>
									<li><em>24/7</em> Support</li>
								</ul>
							</div>
							<footer class="pricing-footer">
								<a class="select" href="#">Sign Up</a>
							</footer>
						</li>
						<li data-type="yearly" class="is-hidden">
							<header class="pricing-header">
								<h6>Pro</h6>
								<div class="price">
									<span class="value">950.000</span>
								</div>
							</header>
							<div class="pricing-body">
								<ul class="pricing-features">
									<li><em>20</em> Email Accounts</li>
									<li><em>5</em> Template Styles</li>
									<li><em>50</em> Products Loaded</li>
									<li><em>10</em> Images per Product</li>
									<li><em>Unlimited</em> Bandwidth</li>
									<li><em>24/7</em> Support</li>
								</ul>
							</div>
							<footer class="pricing-footer">
								<a class="select" href="#">Sign Up</a>
							</footer>
						</li>
					</ul>
				</li>
			</ul>
		</div>
		<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
		<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js'></script>
		<script src="js/index.js"></script>
</div>