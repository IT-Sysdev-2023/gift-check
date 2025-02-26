<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table size="small" bordered :data-source="records" :columns="columns">
                <template #bodyCell="{column, record}">
                    <template  v-if="column.key == 'action'">
                        <a-button @click="setuppayment(record.dti_num)">
                            Setup Payment Dti
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { router } from '@inertiajs/core';

interface Records {
    dti_num: number,
    dti_datereq: string,
    dti_dateneed: string,
    name: string,
    spcus_acctname: string,
    dti_payment_stat: string,
    dti_balance: number,

}
defineProps<{
    records: Records
}>();

const columns = [
    {
        title: 'RFSEGC',
        dataIndex: 'dti_num',
        key: 'dti_num',
    },
    {
        title: 'Date Requested	',
        dataIndex: 'dti_datereq',
        key: 'dti_datereq',
    },
    {
        title: 'Date Validity	',
        dataIndex: 'dti_dateneed',
        key: 'dti_dateneed',
    },
    {
        title: 'Requested By',
        dataIndex: 'name',
        key: 'name',
    },
    {
        title: 'Customer',
        dataIndex: 'spcus_acctname',
        key: 'spcus_acctname',
    },
    {
        title: 'Amount',
        dataIndex: 'dti_balance',
        key: 'dti_balance',
    },
    {
        title: 'Status',
        dataIndex: 'dti_payment_stat',
        key: 'dti_payment_stat',
    },
    {
        title: 'Action',
        key: 'action',
        align: 'center',
    },
];

const setuppayment = (num: number) => {
    router.get(route('accounting.payment.payment.gc.dti.setup', num))
}
</script>
