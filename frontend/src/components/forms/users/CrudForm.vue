<script setup>
	import BaseCheckbox from "@/components/base/BaseCheckbox.vue";
	import BaseInput from "@/components/base/BaseInput.vue";
	import BaseSelect from "@/components/base/BaseMultiSelect.vue";

	defineProps({
		lookup: {
			type: Object,
			default: () => ({}),
		},
		loading: {
			type: Boolean,
			default: false,
		},
		errors: {
			type: Object,
			default: () => ({}),
		},
	});

	const form = defineModel({
		type: Object,
		required: true,
	});
</script>

<template>
	<form>
		<div class="d-flex flex-column gap-2">
			<BaseCheckbox v-model="form.is_active" :label="$t('common.active')" />
			<BaseInput
				v-model="form.name"
				:label="$t('common.name')"
				:placeholder="$t('products.inputName')"
				:error="errors.name"
				required
			/>
			<BaseInput
				v-model="form.email"
				type="email"
				:label="$t('common.email')"
				placeholder="admin@namada.com"
				:error="errors.email"
				required
			/>
			<BaseInput
				v-model="form.password"
				:label="$t('auth.password.title')"
				:placeholder="$t('auth.password.placeholder')"
				:error="errors.password"
				type="password"
				required
			/>
			<BaseSelect
				v-model="form.branches"
				:form-label="$t('branches.title')"
				:options="lookup.branches"
				:placeholder="$t('select.more')"
				:error="errors.branches"
				:searchable="true"
				mode="tags"
			></BaseSelect>
			<BaseSelect
				v-model="form.roles"
				:form-label="$t('users.roles')"
				:options="lookup.roles"
				:placeholder="$t('select.more')"
				:error="errors.roles"
				:searchable="true"
				mode="tags"
			></BaseSelect>
		</div>
	</form>
</template>
