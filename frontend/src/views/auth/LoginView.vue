<script setup>
	import { ref, reactive, onMounted } from "vue";
	import { useRoute, useRouter } from "vue-router";
	import { useToast } from "@/composables/toast.js";
	import { useAuthStore } from "@/stores/auth.js";
	import { useI18n } from "vue-i18n";

	const route = useRoute();
	const router = useRouter();
	const toast = useToast();

	const { t } = useI18n();

	const authStore = useAuthStore();

	const form = reactive({
		data: {
			email: "",
			password: "",
		},
		errors: {},
		processing: false,
	});

	const isPasswordShown = ref(false);

	const redirect = route.query.redirect || { name: "dashboard.index" };

	const handleSubmit = async () => {
		form.errors = {};
		form.processing = true;

		try {
			const response = await authStore.login(form.data);
			if (response.success) {
				const escapedEmail = `<strong>${form.data.email}</strong>`;

				toast.success(
					t("notifications.title"),
					t("auth.loginSuccess", {
						email: escapedEmail,
					})
				);
				router.push(redirect);
			} else {
				form.errors = response.resource.errors;
			}
		} catch (error) {
			if (error.response?.status === 422) {
				form.errors = error.response.data.errors;
			}
		} finally {
			form.processing = false;
		}
	};

	onMounted(() => {});
</script>

<template>
	<div class="container-xl">
		<div class="row">
			<aside class="col-lg-5 d-none d-lg-block"></aside>
			<main class="col-lg-7">
				<div class="main-wrapper">
					<div class="main-header mb-2">
						<h2 class="main-title display-5">
							{{ $t("auth.login") }}
						</h2>
						<p class="main-subtitle text-muted">
							{{ $t("auth.loginSubtitle") }}
						</p>
					</div>
					<form @submit.prevent="handleSubmit">
						<div class="mb-3">
							<label for="email" class="form-label fw-semibold">{{
								$t("common.email")
							}}</label>
							<input
								id="email"
								v-model="form.data.email"
								type="text"
								autofocus
								:class="[
									'form-control',
									{
										'is-invalid': form.errors.email,
									},
								]"
								name="email"
								:disabled="form.processing"
								placeholder="mail@example.com"
								autocomplete="email"
							/>

							<div
								v-if="form.errors.email"
								class="invalid-feedback fw-semibold d-block"
							>
								{{ form.errors.email?.[0] }}
							</div>
						</div>
						<div class="mb-3">
							<label for="password" class="form-label fw-semibold">{{
								$t("auth.password.title")
							}}</label>
							<input
								id="password"
								v-model="form.data.password"
								:type="isPasswordShown ? 'text' : 'password'"
								class="form-control"
								:class="[
									{
										'is-invalid': form.errors.password,
									},
								]"
								autocomplete="current-password"
								name="password"
								:disabled="form.processing"
								:placeholder="$t('auth.password.placeholder')"
								aria-describedby="passwordHelper"
							/>

							<div
								v-if="form.errors.password"
								class="invalid-feedback fw-semibold d-block"
							>
								{{ form.errors.password?.[0] }}
							</div>

							<div class="form-check mt-2">
								<input
									id="passwordToggler"
									v-model="isPasswordShown"
									class="form-check-input"
									type="checkbox"
									value="Password"
								/>
								<label class="form-check-label small" for="passwordToggler">
									{{ $t("auth.password.show") }}
								</label>
							</div>
							<button
								:disabled="form.processing"
								class="btn btn-primary mt-4 d-flex align-items-center justify-content-between w-100 px-4 py-2"
							>
								<div>
									{{ $t("auth.login") }}
									<i class="bi bi-arrow-right-short ms-2"></i>
								</div>
								<span
									v-if="form.processing"
									class="spinner-border spinner-border-sm"
								></span>
							</button>
						</div>
					</form>
					<div class="text-start mt-4">
						<RouterLink :to="{ name: 'home' }" class="text-decoration-none">
							<i class="bi bi-arrow-left-short me-2"></i>
							{{ $t("common.backToHome") }}
						</RouterLink>
					</div>
				</div>
			</main>
		</div>
	</div>
</template>

<style scoped>
	aside {
		height: 100vh;
		background-color: var(--bs-gray);
	}
	.main-wrapper {
		display: flex;
		justify-content: center;
		flex-direction: column;
		width: 100%;
		height: 100vh;
		padding: 5rem;
	}

	@media (max-width: 575.98px) {
		.main-wrapper {
			padding: 1.5rem;
		}
	}
</style>
