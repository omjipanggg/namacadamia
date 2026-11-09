<script setup>
	import { useSidebarStore } from "@/stores/sidebar.js";

	import BaseButton from "@/components/base/BaseButton.vue";
	import AuthDropdown from "@/components/home/partials/auth/AuthDropdown.vue";

	const sidebar = useSidebarStore();
</script>

<template>
	<header class="topbar-primary border-bottom p-3 bg-body-tertiary">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div
						class="d-flex align-items-center justify-content-between gap-3 flex-wrap"
					>
						<div class="d-flex gap-3">
							<div id="btnMenuToggler">
								<BaseButton
									type="button"
									variant="outline-secondary"
									@click="sidebar.toggle()"
								>
									<i v-if="sidebar.collapsed" class="bi bi-chevron-right"></i>
									<i v-else class="bi bi-list"></i>
								</BaseButton>
							</div>
							<div id="btnAddOnNavbar"></div>
						</div>
						<AuthDropdown />
					</div>
				</div>
			</div>
		</div>
	</header>
</template>

<style scoped>
	li.nav-item {
		list-style-type: none;
	}
</style>
