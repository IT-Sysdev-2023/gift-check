<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, Table } from 'ant-design-vue';



const props = defineProps<{
    data: {
        seodtt_barcode: string;
    };
}>();


const columns = [
    {
        title: 'Textfile Line',
        dataIndex: 'seodtt_eod_id',
        key: 'seodtt_eod_id',
    },
    {
        title: 'Credit Limit',
        dataIndex: 'seodtt_creditlimit',
        key: 'seodtt_creditlimit',
    },
    {
        title: 'Cred. Pur. Amt + Add-on',
        dataIndex: 'seodtt_credpuramt',
        key: 'seodtt_credpuramt',
    },
    {
        title: 'Add-on Amt',
        dataIndex: 'seodtt_addonamt',
        key: 'seodtt_addonamt',
    },
    {
        title: 'Remaining Balance',
        dataIndex: 'seodtt_balance',
        key: 'seodtt_balance',
    },
    {
        title: 'Transaction #',
        dataIndex: 'seodtt_id',
        key: 'seodtt_id',
    },
    {
        title: 'Time of Cred Tranx',
        dataIndex: 'seodtt_timetrnx',
        key: 'seodtt_timetrnx',
    },
    {
        title: 'Bus. Unit',
        dataIndex: 'seodtt_bu',
        key: 'seodtt_bu',
    },
    {
        title: 'Terminal #',
        dataIndex: 'seodtt_terminalno',
        key: 'seodtt_terminalno',
    },
    {
        title: 'Ackslip #',
        dataIndex: 'seodtt_ackslipno',
        key: 'seodtt_ackslipno',
    },
];

</script>
 
<template>
    <AuthenticatedLayout>
        <Card :title="`GC Barcode ${props.data.seodtt_barcode} POS Transaction`">
            <Table size="small" :pagination="false" :dataSource="[props.data]" :columns="columns" />
        </Card>
    </AuthenticatedLayout>
</template>
