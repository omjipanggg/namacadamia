<script setup>
	const appName = import.meta.env.VITE_APP_TITLE;

	const alertSoon = () => {
		alert("Soon...");
	};
</script>

<template>
	<footer class="border-top px-4 py-3 text-secondary small">
		<div class="d-flex flex-wrap align-items-center justify-content-between">
			<div class="left-side-footer">
				{{ appName }} &copy; {{ new Date().getFullYear() }}
			</div>
			<div class="right-side-footer d-flex gap-3">
				<a href="#" @click="alertSoon">{{ $t("navigation.feedback") }}</a>
			</div>
		</div>
	</footer>
</template>

<style scoped>
	footer a {
		text-decoration: none;
		color: var(--bs-body-color);
	}

	footer a:hover,
	footer a:focus {
		text-decoration: underline;
		text-decoration-style: dotted;
	}
</style>
