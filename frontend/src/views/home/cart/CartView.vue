<script setup>
	import api from "@/services/api.js";
</script>

<template>
	<div class="big-banner">
		<div class="container-lg">
			<h3 class="display-3">{{ $t("cart.title") }}</h3>
		</div>
	</div>
	<div class="container-lg">
		<div class="row">
			<div class="col-12">
				<div class="card hover-lifted">
					<div class="card-body">
						<div
							class="d-flex text-center py-5 flex-column align-items;center justify-content-center"
						>
							<i class="bi bi-cart4 fs-1 mb-3"></i>
							<h6 class="mb-1">{{ $t("cart.empty") }}</h6>
							<p class="small mb-0">{{ $t("cart.startShopping") }}!</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
