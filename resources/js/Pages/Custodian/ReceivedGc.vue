<template>
    <a-breadcrumb class="mb-3">
        <a-breadcrumb-item>Dashboard</a-breadcrumb-item>
        <a-breadcrumb-item>Received Gift Check</a-breadcrumb-item>
    </a-breadcrumb>
    <a-card>
        <a-table size="small" :data-source="record.data" :columns="columns" :pagination="false"></a-table>
        <pagination-resource :datarecords="record" />
    </a-card>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        record: Object,
        columns: Array
    }
}
</script>
