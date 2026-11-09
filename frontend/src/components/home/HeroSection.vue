<script setup></script>

<template>
	<section id="hero">
		<div class="container-xl px-4 my-5">
			<div
				class="row p-4 pb-0 pe-lg-0 pt-lg-5 align-items-center rounded-3 border shadow-lg"
			>
				<div class="col-lg-7 p-3 p-lg-5 pt-lg-3">
					<h1 class="display-4 fw-bold lh-1 text-body-emphasis">
						Border hero with cropped image and shadows
					</h1>
					<p class="lead">
						Quickly design and customize responsive mobile-first
						sites with Bootstrap, the world’s most popular front-end
						open source toolkit, featuring Sass variables and
						mixins, responsive grid system, extensive prebuilt
						components, and powerful JavaScript plugins.
					</p>
					<div
						class="d-grid mt-5 gap-2 d-md-flex justify-content-md-start mb-4 mb-lg-3"
					>
						<button
							type="button"
							class="btn btn-warning btn-lg px-5 me-md-2 py-3 rounded-pill fw-bold"
						>
							Primary
						</button>
						<button
							type="button"
							class="btn btn-outline-secondary btn-lg py-3 rounded-pill px-5"
						>
							Secondary
						</button>
					</div>
				</div>
				<div
					class="col-lg-4 offset-lg-1 p-0 overflow-hidden d-none d-lg-block shadow-lg"
				>
					<img
						class="rounded-lg-3"
						src="/src/assets/images/bootstrap-docs.png"
						alt=""
						width="720"
					/>
				</div>
			</div>
		</div>
	</section>
</template>

<style scoped></style>
