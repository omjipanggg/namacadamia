<script setup>
	import { ref } from "vue";
	import { useNotificationStore } from "@/stores/notification.js";
	import { useI18n } from "vue-i18n";
	import { useTimeAgoIntl } from "@vueuse/core";

	const { locale } = useI18n();

	const detail = ref(
		"Lorem, ipsum dolor sit amet consectetur adipisicing elit. Obcaecati quaerat porro labore, modi soluta provident totam quia. Quia accusantium sed cumque velit, doloribus odit non, error facere eligendi totam corrupti?"
	);

	const notifyStore = useNotificationStore();

	const openDetail = (id) => {
		detail.value = notifyStore.list.find((n) => n.id === id);
		notifyStore.markAsRead(id);
	};
</script>

<template>
	<div class="col-12">
		<h2>
			{{ $t("notifications.title") }}
		</h2>
	</div>
	<div class="col-12">
		<div class="row g-4">
			<div :class="notifyStore.hasItem ? 'col-xl-7' : 'col-12'">
				<div v-if="notifyStore.hasItem" class="list-group">
					<div
						v-for="notify in notifyStore.list"
						:key="notify.id"
						class="list-group-item list-group-item-action ribbon-container cursor-pointer"
						@click="openDetail(notify.id)"
					>
						<div class="ribbon" :class="`bg-${notify.variant}`">
							<i class="bi" :class="`bi-${notify.icon}`"></i>
						</div>
						<div class="list-content pe-5">
							<div class="list-description">
								<div class="d-flex gap-2">
									<i v-if="notify.read_at" class="bi bi-check-all"></i>
									<small
										class="mb-2 d-block small text-muted"
										:class="{
											'fw-semibold': !notify.read_at,
										}"
										>{{ useTimeAgoIntl(notify.created_at, { locale }) }}</small
									>
								</div>
								<p
									:class="[
										'notification-title',
										'mb-0',
										{ 'fw-semibold': !notify.read_at },
									]"
								>
									{{ notify.title }}
								</p>
								<p
									:class="[
										'mb-0',
										'notification-subtitle',
										{ 'fw-semibold': !notify.read_at },
										'text-secondary',
									]"
								>
									{{ notify.description }}
								</p>
							</div>
							<button
								class="list-destroy btn btn-sm btn-danger"
								@click.stop="notifyStore.remove(notify.id)"
							>
								<i class="bi bi-trash"></i>
							</button>
						</div>
					</div>
				</div>
				<div v-else class="card hover-lifted">
					<div class="card-body">
						<div
							class="d-flex flex-column align-items-center justify-content-center py-5 text-body-secondary"
						>
							<i class="bi bi-bell-slash fs-1 mb-3"></i>

							<h6 class="mb-1">
								{{ $t("notifications.empty") }}
							</h6>

							<p class="small mb-0">
								{{ $t("notifications.allCaughtUp") }}
							</p>
						</div>
					</div>
				</div>
			</div>
			<div :class="['col-xl-5', { 'd-none': !notifyStore.hasItem }]">
				<div class="card hover-lifted">
					<div class="card-body">
						<span v-if="!detail.title">{{ detail }}</span>
						<div v-else>
							<span
								:class="[
									'badge',
									'rounded-pill',
									`text-bg-${detail.variant}`,
									'mb-3',
								]"
							>
								<i :class="['bi', `bi-${detail.icon}`, 'me-1']"></i>
								{{ detail.category }}</span
							>
							<h5>{{ detail.title }}</h5>
							<p class="m-0">
								{{ detail.content }}
							</p>

							<p class="mb-0 mt-2">
								{{ detail.created_at }}
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
	.list-content {
		position: relative;
		overflow: hidden;
	}

	.list-destroy {
		position: absolute;
		top: 120%;
		left: 50%;
		transform: translate(-50%, -50%);
		transition: all 240ms ease;
		font-size: 11px;
	}

	.list-content:hover .list-destroy {
		top: 50%;
	}
</style>
