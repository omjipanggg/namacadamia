<script setup></script>

<template>
	<div class="auth-layout">
		<div class="auth-layout__content">
			<router-view />
		</div>
	</div>
</template>

<style scoped>
.auth-layout {
	display: flex;
	align-items: center;
	justify-content: center;
}

.auth-layout__content {
	width: 100%;
	height: 100vh;
}
</style>
