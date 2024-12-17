<template>
    <AuthenticatedLayout>
        <div>
            <a-row class="mb-2">
                <a-col :span="12">
                    <a-button type="primary">
                        <PlusOutlined />Add New Product
                    </a-button>
                </a-col>
                <a-col :span="12">
                    <div class="flex justify-end">
                        <a-input-search style="width: 300px" v-model:value="value" placeholder="input search text"
                            enter-button @search="onSearch" />
                    </div>
                </a-col>
            </a-row>
        </div>
        <a-card title="Supplier GC Product Setup">
            <a-table size="small" bordered :dataSource="dataSource" :columns="columns" />
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';


const columns = [
    {
        title: 'Product Name',
        dataIndex: ''
    },
    {
        title: 'GC # / Ctrl #',
        dataIndex: ''
    },
    {
        title: 'Description',
        dataIndex: ''
    },
    {
        title: 'GC Worth',
        dataIndex: ''
    },
    {
        title: 'Company Name',
        dataIndex: ''
    },
    {
        title: 'Created By',
        dataIndex: ''
    },
    {
        title: 'Date Created',
        dataIndex: ''
    },
]
</script>