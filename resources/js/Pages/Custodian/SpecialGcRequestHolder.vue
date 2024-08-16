<template>
    <a-breadcrumb class="mb-4">
        <a-breadcrumb-item>Dashboard</a-breadcrumb-item>
        <a-breadcrumb-item>Pending Gc Holders</a-breadcrumb-item>
    </a-breadcrumb>
    <a-card>
        <a-table :data-source="specExRecord.data" :columns="columns" size="small">
            <template #bodyCell="{ column, record }">
                <template v-if="column.key == 'setup'">
                    <a-button
                        @click="() => $inertia.get(route('custodian.pendings.external.holder.setup'), { id: record.spexgc_id })">
                        Setup
                    </a-button>
                </template>
            </template>
        </a-table>
    </a-card>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        specExRecord: Object,
        columns: Array
    }
}
</script>
