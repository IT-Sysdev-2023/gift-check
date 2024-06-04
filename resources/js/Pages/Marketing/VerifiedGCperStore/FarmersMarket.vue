<template>
    <a-card>
        <a-card class="mb-2" title="Farmers Market - Verified GC"></a-card>
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
                    barcode: '1310000000015',
                    dateVerified_Reverified: '2017-09-01',
                    verified_ReverifiedBy: 'Jennelyn Socorin',
                    customer: 'Jezza Marie Mangmang',
                    balance: '0.00',
                },
                {
                    key: '2',
                    barcode: '1310000000015',
                    dateVerified_Reverified: '2017-09-01',
                    verified_ReverifiedBy: 'Jennelyn Socorin',
                    customer: 'Jezza Marie Mangmang',
                    balance: '0.00',
                }
            ],

            columns: [
                {
                    title: 'Barcode #	',
                    dataIndex: 'barcode',
                    key: 'barcode',
                },
                {
                    title: 'Date Verified/Reverified',
                    dataIndex: 'dateVerified_Reverified',
                    key: 'dateVerified_Reverified',
                },
                {
                    title: 'Verified/Reverified By',
                    dataIndex: 'verified_ReverifiedBy',
                    key: 'verified_ReverifiedBy',
                },
                {
                    title: 'Customer',
                    dataIndex: 'customer',
                    key: 'customer',
                },
                {
                    title: 'Balance',
                    dataIndex: 'balance',
                    key: 'balance',
                }
            ],
        };
    },
}
</script>

