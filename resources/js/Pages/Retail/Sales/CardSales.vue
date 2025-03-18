<template>
    <AuthenticatedLayout>
        <a-card title="Card Sales">
            <a-table :dataSource="data" :columns="columns">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'total_sales'">
                        <p>{{ `₱ ${record.total_sales}`}}</p>
                    </template>
                    <template v-if="column.dataIndex === 'view'">
                        <a-button type="primary">
                            <PicLeftOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    data: Object
})


const columns = [
    {
        title: 'Card Name',
        dataIndex: 'ccard_name',
        key: 'ccard_name',
        align: 'center'
    },
    {
        title: 'Total Sales	',
        dataIndex: 'total_sales',
        key: 'total_sales',
        align: 'center'
    },
    {
        title: 'View Transactions',
        dataIndex: 'view',
        key: 'view',
        align: 'end'
    },
]
</script>