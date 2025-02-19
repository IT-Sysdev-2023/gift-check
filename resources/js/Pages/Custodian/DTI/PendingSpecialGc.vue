<template>
    <AuthenticatedLayout>
        <a-tabs v-model:activeKey="activeKey">
            <a-tab-pane key="1" tab=" DTI Pending GC List (GC Holder Entry)">
                <a-card title="DTI Special GC">
                    <a-table :dataSource="dataSource" :columns="columns" />
                </a-card>
            </a-tab-pane>
        </a-tabs>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const activeKey = ref('1')

const dataSource = [
    {
        key: '1',
        name: 'Mike',
        age: 32,
        address: '10 Downing Street',
    },
    {
        key: '2',
        name: 'John',
        age: 42,
        address: '10 Downing Street',
    },
]

const columns = [
    {
        title: 'Name',
        dataIndex: 'name',
        key: 'name',
    },
    {
        title: 'Age',
        dataIndex: 'age',
        key: 'age',
    },
    {
        title: 'Address',
        dataIndex: 'address',
        key: 'address',
    },
]


</script>