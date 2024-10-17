<template>
    <AuthenticatedLayout>
        <a-card>
            <a-list item-layout="horizontal" :data-source="record">
                <template #renderItem="{ item }">
                    <a-list-item>
                        <a-list-item-meta description="This is gc textfile comming from fad">
                            <template #title>
                                <span style="cursor: pointer;" @click="setup(item)">{{ item }}</span>
                            </template>
                            <template #avatar>
                                <a-avatar src="https://joeschmoe.io/api/v1/random" />
                            </template>
                        </a-list-item-meta>
                    </a-list-item>
                </template>
            </a-list>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import { router } from '@inertiajs/core';

defineProps({
    record: Object,
});

const setup = (name) => {
    router.get(route('admin.setup', name));
}
</script>
