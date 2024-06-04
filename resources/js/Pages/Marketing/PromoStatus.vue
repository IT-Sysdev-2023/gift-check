<template>
    <a-card>
        <a-card class="mb-2" title="Promo List"></a-card>
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
                    barcode: '1310000002556',
                    denomination: '1,000.00',
                    RetailGroup: '1',
                    PromoName: 'Rewards Card Raffle Promo',
                    customerName: 'Wea Gutierrez',
                    cusAddress: 'Lot 3 Blk 37, P, Capitol Valley, Dao District, Tagbilaran City',
                    status: 'Released',
                    dateReleased: 'Apr 26 2019 03:36 PM',
                    ReleasedBy: 'Roy Jaictin',
                }
            ],

            columns: [
                {
                    title: 'GC Barcode',
                    dataIndex: 'barcode',
                    key: 'barcode',
                },
                {
                    title: 'Denom',
                    dataIndex: 'denomination',
                    key: 'denomination',
                },
                {
                    title: 'Retail Group',
                    dataIndex: 'RetailGroup',
                    key: 'RetailGroup',
                },
                {
                    title: 'Promo Name',
                    dataIndex: 'PromoName',
                    key: 'PromoName',
                },
                {
                    title: 'Customer Name',
                    dataIndex: 'customerName',
                    key: 'customerName',
                },
                {
                    title: 'Customer Address',
                    dataIndex: 'cusAddress',
                    key: 'cusAddress',
                },
                {
                    title: 'Status',
                    dataIndex: 'status',
                    key: 'status',
                },
                {
                    title: 'Date Released',
                    dataIndex: 'dateReleased',
                    key: 'dateReleased',
                },
                {
                    title: 'Released By',
                    dataIndex: 'ReleasedBy',
                    key: 'ReleasedBy',
                }
            ],
        };
    },
}
</script>

