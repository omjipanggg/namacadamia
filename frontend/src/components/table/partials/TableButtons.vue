<script setup>
	import Button from "../../base/BaseButton.vue";
	import { useTable } from "../functions/useTable.js";
	const tableState = useTable();
</script>

<template>
	<template v-if="tableState.meta.isMobile">
		<nav class="navbar navbar-expand-lg w-100 p-0">
			<div class="container-fluid p-0">
				<span
					class="navbar-text dropdown-toggle border cursor-pointer rounded d-block w-100"
					style="padding-left: 12px; padding-right: 12px"
					data-bs-toggle="collapse"
					data-bs-target="#navbarNavDarkDropdown"
					aria-controls="navbarNavDarkDropdown"
					aria-expanded="false"
					aria-label="Toggler"
				>
					{{ $t("common.actions") }}
				</span>
				<div id="navbarNavDarkDropdown" class="collapse navbar-collapse">
					<ul class="navbar-nav pt-2">
						<li class="nav-item dropdown">
							<a
								class="nav-link dropdown-toggle px-3"
								href="#"
								role="button"
								data-bs-toggle="dropdown"
								aria-expanded="false"
							>
								<i class="bi bi-cloud-download me-2"></i>
								{{ $t("common.export") }}
							</a>
							<ul class="dropdown-menu">
								<li>
									<a class="dropdown-item" href="#">
										<i class="bi bi-filetype-csv me-2"></i>
										CSV
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="#">
										<i class="bi bi-filetype-pdf me-2"></i>
										PDF</a
									>
								</li>
							</ul>
						</li>
						<li class="nav-item">
							<a class="nav-link px-3" href="#">
								<i class="bi bi-printer me-2"></i>
								{{ $t("common.print") }}
							</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
	</template>

	<template v-else>
		<div class="btn-group" role="group">
			<Button
				class="dropdown-toggle no-caret"
				data-bs-toggle="dropdown"
				aria-expanded="false"
			>
				{{ $t("common.export") }}
				<i class="bi bi-cloud-download ms-2"></i>
			</Button>
			<ul class="dropdown-menu">
				<li>
					<a href="#" class="dropdown-item">
						<i class="bi bi-filetype-csv me-2"></i>
						CSV
					</a>
				</li>
				<li>
					<a href="#" class="dropdown-item">
						<i class="bi bi-filetype-pdf me-2"></i>
						PDF
					</a>
				</li>
			</ul>
		</div>
		<Button>
			{{ $t("common.print") }}
			<i class="bi bi-printer ms-2"></i>
		</Button>
	</template>
</template>
