<script setup></script>

<template>
	<div class="container-lg">
		<div class="row">
			<div class="col-12">
				<div
					class="d-flex align-items-center justify-content-center flex-column gap-5 flex-wrap big-body"
				>
					<h3 class="display-1 animate-right">404</h3>
					<div class="button-group d-flex gap-3">
						<RouterLink
							:to="{ name: 'home' }"
							class="btn btn-outline-secondary animate-left"
						>
							<i class="bi bi-arrow-left me-2"></i>
							{{ $t("common.backToHome") }}
						</RouterLink>
						<RouterLink
							:to="{ name: 'dashboard.index' }"
							class="btn btn-success animate-down"
						>
							{{ $t("common.skip") }}
							<i class="bi bi-arrow-right ms-2"></i>
						</RouterLink>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
	.big-body {
		min-height: 72vh;
	}

	.animate-left {
		animation: goLeft 90s linear infinite !important;
	}
	.animate-right {
		animation: goRight 480s linear infinite !important;
	}
	.animate-down {
		animation: goDown 60s linear infinite !important;
	}

	@keyframes goLeft {
		0% {
			transform: translateX(0);
		}
		50% {
			transform: translateX(-720px) translateY(720px);
		}
		100% {
			transform: translateX(12px);
		}
	}

	@keyframes goRight {
		0% {
			transform: translateX(0);
		}
		50% {
			transform: translateX(720px);
		}
		100% {
			transform: translateX(12px);
		}
	}

	@keyframes goDown {
		0% {
			transform: translateY(0);
		}
		50% {
			transform: translateY(640px) translateX(12px);
		}
		100% {
			transform: translateY(12px);
		}
	}
</style>
