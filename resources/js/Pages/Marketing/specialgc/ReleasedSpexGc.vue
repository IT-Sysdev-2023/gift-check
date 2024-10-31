<template>
    <AuthenticatedLayout>
        <a-card title="Released Special External GC">
            <a-table :dataSource="data.data" :columns="columns">
                <template v-slot:bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button><EyeOutlined /></a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";

defineProps({
    data: Object,
});

const columns = ref([
    {
        title: "RFSEGC#",
        dataIndex: "spexgc_num",
    },
    {
        title: "Date Requested",
        dataIndex: "datereq",
    },
    {
        title: "Requested By",
        dataIndex: "requestedBy",
    },
    {
        title: "Customer",
        dataIndex: "spcus_companyname",
        ellipsis: "true",
    },
    {
        title: "Date Released",
        dataIndex: "reqap_date",
    },
    {
        title: "Released By",
        dataIndex: "releasedBy",
    },
    {
        title: "View",
        dataIndex: "view",
        align : "center"
    },
]);
</script>
