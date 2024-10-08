<template>
    <AuthenticatedLayout>
        <a-card>
            <a-tabs v-model:activeKey="activeKey">
                <a-tab-pane key="1" tab="Special Internal GC">
                    <a-table bordered :dataSource="internal" :columns="columns" />
                </a-tab-pane>
                <a-tab-pane key="2" tab="Special External GC" force-render>
                    <a-table bordered :dataSource="external" :columns="columns" />
                </a-tab-pane>
            </a-tabs>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    internal: Object,
    external: Object
})
const activeKey = ref('1')

const columns = [
    {
        title: 'RFSEGC #',
        dataIndex: 'spexgc_num',
    },
    {
        title: 'Date Requested',
        dataIndex: 'dateReq',
    },
    {
        title: 'Date Validity',
        dataIndex: 'dateNeed',
    },
    {
        title: 'Total Denomination',
        dataIndex: 'totalDenom',
    },
    {
        title: 'Customer',
        dataIndex: 'spcus_acctname',
    },
    {
        title: 'Requested By',
        dataIndex: 'requestedBy',
    },
]


</script>