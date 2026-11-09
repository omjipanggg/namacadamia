<script setup>
	const appName = import.meta.env.VITE_APP_TITLE;
</script>

<template>
	<footer class="footer my-4">
		<div class="container-lg">
			<div class="border-top">
				<div class="row pt-3">
					<div class="col-lg-6 order-2 order-lg-1 text-center text-lg-start">
						<p class="mb-2">
							{{ appName }} &copy;
							{{ new Date().getFullYear() }}
						</p>
					</div>
					<div class="col-lg-6 text-center text-lg-end order-1 order-lg-2">
						<ul class="list-unstyled d-inline-flex ms-auto">
							<li>
								<a href="#">{{ $t("footer.privacyPolicy") }}</a>
							</li>
							<li>
								<a href="#">{{ $t("footer.terms") }}</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</footer>
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

	.list-unstyled li + li::before {
		content: "\2219";
		padding-left: 0.5rem;
		padding-right: 0.5rem;
	}
</style>
