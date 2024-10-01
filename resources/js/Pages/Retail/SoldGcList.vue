<template>
    <a-card title="Sold GC's">
        <div>
            <div class="flex justify-end">
                <a-input-search class="mb-2" v-model:value="value" placeholder="Search Barcode" style="width: 400px"
                    @search="onSearch" />
            </div>
        </div>
        <a-table :pagination="false" bordered size="small" :dataSource="data.data" :columns="columns" />
    </a-card>
    <Pagination :datarecords="data" class="mt-5" />
</template>

<script>
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object
    },
    data() {
        return {
            columns: [
                {
                    title: 'Barcode',
                    dataIndex: 'strec_barcode',
                },
                {
                    title: 'Denomination',
                    dataIndex: 'denomination',
                },
                {
                    title: 'GC Request No.',
                    dataIndex: 'strec_recnum',
                },
                {
                    title: 'Date Sold',
                    dataIndex: 'dateSold',
                },
                {
                    title: 'Transaction Number',
                    dataIndex: 'trans_number',
                },
                {
                    title: 'Transaction Type',
                    dataIndex: 'paymentType',
                },
                {
                    title: 'Store Verified',
                    dataIndex: 'store_name',
                },
            ],
        }
    },
    methods: {
        onSearch(e) {

            
            this.$inertia.get(route('retail.soldGc'),{
                barcode: e
            });
        }
    }
}
</script>