<script setup></script>

<template>
	<section id="services">
		<div class="container">
			<div class="row justify-content-between">
				<div class="col-lg-6">
					<h2 class="section-title">{{ $t("footer.whyUs") }}</h2>
					<p>
						Lorem ipsum, dolor sit amet consectetur adipisicing elit. Aliquam
						alias nemo distinctio est reprehenderit ipsam vitae placeat enim
						quaerat quia, repudiandae hic molestias consequatur.
					</p>

					<div class="row my-5">
						<div class="col-md-6">
							<div class="feature">
								<h3>
									<i class="bi bi-truck me-3"></i> Fast &amp; Free Shipping
								</h3>
								<p class="text-muted">
									Donec vitae odio quis nisl dapibus malesuada. Nullam ac
									aliquet velit. Aliquam vulputate.
								</p>
							</div>
						</div>

						<div class="col-md-6">
							<div class="feature">
								<h3>
									<i class="bi bi-basket me-3"></i>
									Easy to Shop
								</h3>
								<p class="text-muted">
									Donec vitae odio quis nisl dapibus malesuada. Nullam ac
									aliquet velit. Aliquam vulputate.
								</p>
							</div>
						</div>

						<div class="col-md-6">
							<div class="feature">
								<h3>
									<i class="bi bi-headset me-3"></i>
									24/7 Support
								</h3>
								<p class="text-muted">
									Donec vitae odio quis nisl dapibus malesuada. Nullam ac
									aliquet velit. Aliquam vulputate.
								</p>
							</div>
						</div>

						<div class="col-md-6">
							<div class="feature">
								<h3>
									<i class="bi bi-recycle me-3"></i>
									Hassle Free Returns
								</h3>
								<p class="text-muted">
									Donec vitae odio quis nisl dapibus malesuada. Nullam ac
									aliquet velit. Aliquam vulputate.
								</p>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-6">
					<div class="img-wrap">
						<img
							src="/src/assets/images/why.jpg"
							alt="Image"
							class="img-fluid"
						/>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<style scoped>
	#services {
		padding: 8rem 0 4rem 0;
	}
	#services .img-wrap {
		position: relative;
	}
	#services .img-wrap:before {
		position: absolute;
		content: "";
		top: 2rem;
		width: 230px;
		height: 186px;
		background-image: url("../../assets/images/dots-yellow.svg");
		background-repeat: no-repeat;
		background-size: contain;
		-webkit-transform: translate(-40%, -40%);
		-ms-transform: translate(-40%, -40%);
		transform: translate(-40%, -40%);
		z-index: -1;
	}
	#services .img-wrap img {
		border-radius: 20px;
	}

	.feature {
		margin-bottom: 30px;
	}
	.feature .icon {
		display: inline-block;
		position: relative;
		margin-bottom: 20px;
	}
	.feature .icon:before {
		content: "";
		width: 33px;
		height: 33px;
		position: absolute;
		background: rgba(59, 93, 80, 0.2);
		border-radius: 50%;
		right: -8px;
		bottom: 0;
	}
	.feature h3 {
		font-size: 14px;
	}
	.feature p {
		font-size: 14px;
		line-height: 22px;
	}
</style>
