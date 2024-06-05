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
                    promoNo: '006',
                    PromoName: 'Rewards Card Raffle Promo',
                    dateNotified: 'January 04, 2020',
                    expiryDate: 'Expiration Date',
                    Group: '1',
                    createdBy: 'Ritchel Mutia',
                    status: 'Released',
                    dateReleased: 'Apr 26 2019 03:36 PM',
                    ReleasedBy: 'Roy Jaictin',
                }
            ],

            columns: [
                {
                    title: 'Promo No.',
                    dataIndex: 'promoNo',
                    key: 'promoNo',
                },
                {
                    title: 'Promo Name',
                    dataIndex: 'PromoName',
                    key: 'PromoName',
                },
                {
                    title: 'Date Notified',
                    dataIndex: 'dateNotified',
                    key: 'dateNotified',
                },
                {
                    title: 'Expiry Date',
                    dataIndex: 'expiryDate',
                    key: 'expiryDate',
                },
                {
                    title: 'Group',
                    dataIndex: 'Group',
                    key: 'Group',
                },
                {
                    title: 'Created By',
                    dataIndex: 'createdBy',
                    key: 'createdBy',
                }
            ],
        };
    },
}
</script>

