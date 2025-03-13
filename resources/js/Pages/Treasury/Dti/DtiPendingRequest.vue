<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb>
            <a-breadcrumb-item>
                <a :href="route('treasury.dashboard')">Home</a>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card class="mt-5">
            <a-table size="small" :dataSource="props.records" :columns="columns">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'reqby'">
                        <span class="uppercase">{{ record.firstname }}</span>, <span class="uppercase">{{
                            record.lastname }}</span>
                    </template>
                    <template v-if="column.key === 'action'">
                        <a-button type="primary" @click="edit(record.dti_num)">
                            Edit
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { router } from '@inertiajs/core';

interface Record {
    records: {
        dti_num: number,
        dateNeed: string,
        dateReq: string,
        firstname: string,
        lastname: string,
        spcus_companyname: string,
        total: number,
    }
}
const props = defineProps<{
    records: Record,
    title: string
}>()
const columns = [
    {
        title: 'RFSEGC#',
        dataIndex: 'dti_num',
        key: 'name',
    },
    {
        title: 'Date Requested',
        dataIndex: 'dateReq',
        key: 'age',
    },
    {
        title: 'Date Needed',
        dataIndex: 'dateNeed',
        key: 'address',
    },
    {
        title: 'Total Denomination',
        dataIndex: 'total',
        key: 'address',
    },
    {
        title: 'Customer',
        dataIndex: 'spcus_companyname',
        key: 'customer',
    },
    {
        title: 'Requested By',

        key: 'reqby',
    },
    {
        title: 'Action',

        key: 'action',
    },
];

const edit = (id: number) => {
    router.get(route('treasury.transactions.dti.dti-edit-request', id))
}
</script>
