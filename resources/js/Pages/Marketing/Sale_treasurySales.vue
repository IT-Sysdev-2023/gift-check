<template>
    <Head title="Treasury Sales" />
    <a-card>
        <a-card class="mb-2" title="Treasury Sales"></a-card>
        <a-table :dataSource="dataSource" size="small" bordered :columns="columns" />
    </a-card>
   
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
export default {
    layout: AuthenticatedLayout,
    

    data() {
        return {
            dataSource: [
                {
                    key: '1',
                    transaction_number: '1823',
                    gc_type: 'Regular GC',
                    customer: 'Alturas Mall',
                    date: 'June 03, 2024',
                    time: '	3:07 pm',
                    GC_pcs: '52',
                    total_denom: '26,000.00',
                    payment_type: 'Cash',
                }
            ],

            columns: [
                {
                    title: 'Transaction #	',
                    dataIndex: 'transaction_number',
                    key: 'transaction_number',
                },
                {
                    title: 'GC Type',
                    dataIndex: 'gc_type',
                    key: 'gc_type',
                },
                {
                    title: 'Customer',
                    dataIndex: 'customer',
                    key: 'customer',
                },
                {
                    title: 'Date',
                    dataIndex: 'date',
                    key: 'date',
                },
                {
                    title: 'Time',
                    dataIndex: 'time',
                    key: 'time',
                },
                {
                    title: 'GC pc(s)',
                    dataIndex: 'GC_pcs',
                    key: 'GC_pcs',
                },
                {
                    title: 'Total Denom',
                    dataIndex: 'total_denom',
                    key: 'total_denom',
                },
                {
                    title: 'Payment Type',
                    dataIndex: 'payment_type',
                    key: 'payment_type',
                },
            ],
        };
    },
}
</script>

