<script setup>
	const appName = import.meta.env.VITE_APP_TITLE;
</script>

<template>
	<section id="footer" class="mt-5">
		<div class="container">
			<!-- <div class="img-absolute"> -->
			<!-- <img src="images/featured.png" alt="Image" class="img-fluid" /> -->
			<!-- </div> -->

			<div class="row g-5">
				<div class="col-12">
					<h3>
						<a href="#" class="footer-logo">{{ appName }}</a>
					</h3>
				</div>

				<div class="col-lg-7 mt-3">
					<p class="m-0">
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Omnis, rem
						recusandae temporibus tenetur impedit qui debitis, reprehenderit
						nihil amet in, consequatur dolore exercitationem fuga voluptatem!
						Qui dignissimos hic maiores eaque. Iste architecto hic odit nam
						atque aspernatur porro laboriosam expedita?
					</p>
				</div>

				<div class="col-lg-5 mt-3">
					<div class="row links-wrap">
						<div class="col-sm-6">
							<ul class="list-unstyled text-start text-lg-end m-0">
								<li><a href="#">Links</a></li>
								<li><a href="#">Links</a></li>
								<li><a href="#">Links</a></li>
								<li><a href="#">Links</a></li>
							</ul>
						</div>

						<div class="col-sm-6">
							<ul class="list-unstyled text-start text-lg-end m-0">
								<li>
									<a href="#">{{ $t("blog.title") }}</a>
								</li>
								<li>
									<a href="#">{{ $t("navigation.career") }}</a>
								</li>
								<li>
									<a href="#">{{ $t("products.title") }}</a>
								</li>
								<li>
									<a href="#">{{ $t("navigation.support") }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<style scoped>
	a {
		text-decoration: none;
		color: var(--bs-body-color);
	}

	a:focus,
	a:hover {
		text-decoration: underline;
		text-decoration-style: dotted;
	}
</style>
