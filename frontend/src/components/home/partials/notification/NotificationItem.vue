<script setup>
	import { useTimeAgoIntl } from "@vueuse/core";
	import { useI18n } from "vue-i18n";

	const { locale } = useI18n();

	defineProps({
		notifications: {
			type: Array,
			required: true,
		},
	});
</script>

<template>
	<li v-for="notify in notifications" :key="notify.id" class="border-bottom">
		<a
			:href="notify.url ?? '#'"
			class="dropdown-item d-flex align-items-center justify-content-start gap-3"
		>
			<div class="left-side">
				<i
					:class="[
						'bi',
						'notification-icon',
						`bi-${notify.icon}`,
						'rounded-pill',
					]"
				></i>
			</div>
			<div class="right-side text-truncate">
				<small class="small">{{
					useTimeAgoIntl(notify.created_at, { locale })
				}}</small>
				<div class="notification-title text-truncate">
					{{ notify.title }}
				</div>
			</div>
		</a>
	</li>
	<li
		v-if="!notifications.length"
		class="border-bottom d-flex flex-column align-items-center justify-content-center py-5 text-body-secondary"
	>
		<i class="bi bi-bell-slash fs-1 mb-3"></i>

		<h6 class="mb-1">{{ $t("notifications.empty") }}</h6>

		<p class="small mb-0">
			{{ $t("notifications.allCaughtUp") }}
		</p>
	</li>
</template>

<style scoped>
	.dropdown-item .small {
		font-size: 11px !important;
	}
	.dropdown-item .notification-title {
		font-size: 13px;
	}
</style>
