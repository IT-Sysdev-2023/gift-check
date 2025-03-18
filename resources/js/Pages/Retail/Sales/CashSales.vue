<template>
    <AuthenticatedLayout>
        <a-card title="Cash Sales">
            <div>
                <div class="flex justify-end mb-3">
                    <a-input-search v-model:value="searchValue" placeholder="input search text" style="width: 400px"
                        @change="handleSearch" />
                </div>
            </div>
            <a-table :pagination="false" :dataSource="data.data" :columns="columns">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button type="primary">
                            <PicLeftOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
        <Pagination :datarecords="data" class="mt-5" />
    </AuthenticatedLayout>
</template>

<script setup>
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/core';
import { ref } from 'vue';

defineProps({
    data: Object
})

const searchValue = ref('')

const handleSearch = () => {
    router.get(route('retail.sales.cashSales'), {
        search: searchValue.value
    }, {
        preserveState: true
    })
}

const columns = [
    {
        title: 'Date',
        dataIndex: 'transDate',
        key: 'transDate',
    },
    {
        title: 'Transaction #',
        dataIndex: 'trans_number',
        key: 'trans_number',
    },
    {
        title: 'Total GC',
        dataIndex: 'totalPayment',
        key: 'totalPayment',
    },
    {
        title: 'Total Line Discount',
        dataIndex: 'lineDiscount',
        key: 'lineDiscount',
    },
    {
        title: 'Subtotal Discount',
        dataIndex: 'stotalDis',
        key: 'stotalDis',
    },
    {
        title: 'Amount Due',
        dataIndex: 'amountDue',
        key: 'amountDue',
    },
    {
        title: 'View',
        dataIndex: 'view',
        key: 'view',
    },
]

</script>