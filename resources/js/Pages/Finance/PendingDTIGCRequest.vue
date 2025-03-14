<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    columnsDti: Array,
    externalDti: Object,
    title: String
})

</script>
<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb>
            <a-breadcrumb-item><a :href="route('finance.dashboard')">Home</a></a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card class="mt-5">
            <a-table :columns="props.columnsDti" :data-source="props.externalDti" bordered size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'View'">
                        <a-button type="primary"
                            @click="() => $inertia.get(route('finance.pendingGc.ditPendingRequest'), { id: record.dti_num, gcType: record.dti_promo })">
                            <PicLeftOutlined /> View
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
        <!-- {{ externalDti }} -->
    </AuthenticatedLayout>
</template>
