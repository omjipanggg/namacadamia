<script setup>
	import { ref, onMounted, watch } from "vue";

	const searchQuery = ref("");

	const handleSearch = () => {
		console.log("Query:", searchQuery.value);
	};
</script>

<template>
	<div class="container">
		<div class="row align-items-center justify-content-center">
			<div class="col-md-8">
				<form class="d-flex" role="search" @submit.prevent="handleSearch">
					<div class="input-group mb-3">
						<input
							v-model="searchQuery"
							class="form-control me-2 rounded-start-pill"
							type="search"
							:placeholder="$t('search.placeholder')"
							aria-label="Search"
							aria-describedby="button-addon2"
							required
						/>
						<button
							id="button-addon2"
							class="btn btn-warning"
							role="button"
							type="submit"
						>
							<i class="bi bi-search"></i>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>

<style scoped>
	form {
		position: relative;
	}

	form input {
		padding-left: 2rem;
		padding-top: 1rem;
		padding-bottom: 1rem;
		white-space: nowrap;
		text-overflow: ellipsis;
	}

	form button {
		position: absolute;
		right: 9px;
		top: 50%;
		transform: translateY(-50%);
		padding-top: 5px;
		padding-bottom: 5px;
		padding-left: 1.7rem;
		padding-right: 2rem;
		border-top-right-radius: var(--bs-border-radius-pill);
		border-bottom-right-radius: var(--bs-border-radius-pill);
		border-top-left-radius: 0;
		border-bottom-left-radius: 0;
	}
</style>
