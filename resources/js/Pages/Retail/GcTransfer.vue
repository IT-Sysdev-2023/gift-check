<template>
    <AuthenticatedLayout>
        <a-tabs v-model:activeKey="activeKey">
            <a-tab-pane key="1" tab="Transfer Request List">
                <a-table :dataSource="dataSource" :columns="columns" />
            </a-tab-pane>
            <a-tab-pane key="2" tab="GC Tranfer Request">asdsad</a-tab-pane>
            <a-tab-pane key="3" tab="Serve Transfer Reqeust(Out)">Serve Transfer Reqeust(Out)</a-tab-pane>
            <a-tab-pane key="3" tab="Serve Transfer Reqeust(In)">Serve Transfer Reqeust(In)</a-tab-pane>
        </a-tabs>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const activeKey = ref('1');

const columns = [
    {
        title: 'Request',
        dataIndex: 'req',
    },
    {
        title: 'From Location',
        dataIndex: 'loc',
    },
    {
        title: 'Date Requested',
        dataIndex: 'DateRequested',
    },
    {
        title: 'Total Amount',
        dataIndex: 'total',
    },
    {
        title: 'Requested By',
        dataIndex: 'reqby',
    },
    {
        title: 'status',
        dataIndex: 'stat',
    },
]

</script>