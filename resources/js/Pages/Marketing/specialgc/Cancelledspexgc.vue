<template>
    <AuthenticatedLayout>
        <a-card title="Cancelled Special GC Request">
            <a-table size="small" bordered :dataSource="data" :columns="columns" />
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineProps({ data: Object });

const columns = [
    {
        title: "Name",
        dataIndex: "spexgc_num",
        width: "10%",
    },
    {
        title: "Date Request",
        dataIndex: "spexgc_datereq",
    },
    {
        title: "Customer",
        dataIndex: "spcus_companyname",
        ellipsis: "true",
    },
    {
        title: "Date Cancelled",
        dataIndex: "cancelledDate",
    },
    {
        title: "Cancelled By",
        dataIndex: "cancelledBy",
    },
];
</script>
