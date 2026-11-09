<script setup></script>

<template>
	<section id="featured">
		<div class="container">
			<div class="row align-items-center">
				<div
					class="col-md-12 col-lg-4 mb-5 mb-lg-0 order-lg-1 order-2 mt-5 mt-lg-0"
				>
					<h2 class="mb-4 section-title">Crafted with excellent material.</h2>
					<p class="mb-4">
						Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet
						velit. Aliquam vulputate velit imperdiet dolor tempor tristique.
					</p>
					<router-link
						:to="{ name: 'products.index' }"
						class="btn btn-warning rounded-pill px-4"
						>{{ $t("navigation.explore") }}</router-link
					>
				</div>
				<div class="col-lg-8 order-lg-2 order-1">
					<div class="row">
						<div class="col-12 mt-5 col-md-4">
							<router-link class="product-item" :to="{ name: 'cart.index' }">
								<img
									src="/src/assets/images/product-1.png"
									class="img-fluid product-thumbnail"
								/>
								<h3 class="product-title">Nordic Chair</h3>
								<strong class="product-price">Rp.240.000</strong>

								<span class="icon-cross">
									<img src="/src/assets/images/cross.svg" class="img-fluid" />
								</span>
							</router-link>
						</div>
						<div class="col-12 mt-5 col-md-4">
							<router-link class="product-item" :to="{ name: 'cart.index' }">
								<img
									src="/src/assets/images/product-2.png"
									class="img-fluid product-thumbnail"
								/>
								<h3 class="product-title">Kruzo Aero Chair</h3>
								<strong class="product-price">Rp.180.000</strong>

								<span class="icon-cross">
									<img src="/src/assets/images/cross.svg" class="img-fluid" />
								</span>
							</router-link>
						</div>
						<div class="col-12 mt-5 col-md-4">
							<router-link class="product-item" :to="{ name: 'cart.index' }">
								<img
									src="/src/assets/images/product-3.png"
									class="img-fluid product-thumbnail"
								/>
								<h3 class="product-title">Ergonomic Chair</h3>
								<strong class="product-price">Rp.660.000</strong>

								<span class="icon-cross">
									<img src="/src/assets/images/cross.svg" class="img-fluid" />
								</span>
							</router-link>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<style scoped>
	#featured {
		padding: 6rem 0;
	}
	.product-item {
		text-align: center;
		text-decoration: none;
		display: block;
		position: relative;
		padding-bottom: 50px;
		cursor: pointer;
	}
	#featured .product-item .product-thumbnail {
		margin-bottom: 30px;
		position: relative;
		top: 0;
		-webkit-transition: 0.3s all ease;
		-o-transition: 0.3s all ease;
		transition: 0.3s all ease;
	}
	#featured .product-item h3 {
		font-weight: 600;
		font-size: 16px;
	}
	#featured .product-item strong {
		font-weight: 800 !important;
		font-size: 18px !important;
	}
	#featured .product-item h3,
	#featured .product-item strong {
		color: var(--bs-body-color);
		text-decoration: none;
	}

	#featured .product-item:hover h3,
	#featured .product-item:hover strong {
		color: var(--bs-gray-600);
	}
	#featured .product-item .icon-cross {
		position: absolute;
		width: 35px;
		height: 35px;
		display: inline-block;
		background: #2f2f2f;
		bottom: 15px;
		left: 50%;
		-webkit-transform: translateX(-50%);
		-ms-transform: translateX(-50%);
		transform: translateX(-50%);
		margin-bottom: -17.5px;
		border-radius: 50%;
		opacity: 0;
		visibility: hidden;
		-webkit-transition: 0.3s all ease;
		-o-transition: 0.3s all ease;
		transition: 0.3s all ease;
	}
	#featured .product-item .icon-cross img {
		position: absolute;
		left: 50%;
		top: 50%;
		-webkit-transform: translate(-50%, -50%);
		-ms-transform: translate(-50%, -50%);
		transform: translate(-50%, -50%);
	}
	#featured .product-item:before {
		bottom: 0;
		left: 0;
		right: 0;
		position: absolute;
		content: "";
		background: #dce5e4;
		height: 0%;
		z-index: -1;
		border-radius: 10px;
		-webkit-transition: 0.3s all ease;
		-o-transition: 0.3s all ease;
		transition: 0.3s all ease;
	}
	#featured .product-item:hover .product-thumbnail {
		top: -25px;
	}
	#featured .product-item:hover .icon-cross {
		bottom: 0;
		opacity: 1;
		visibility: visible;
	}
	#featured .product-item:hover:before {
		height: 70%;
	}
</style>
