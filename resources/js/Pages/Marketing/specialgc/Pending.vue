<template>
    <AuthenticatedLayout>
        <a-card>
            <a-tabs v-model:activeKey="activeKey">
                <a-tab-pane key="1" tab="Special Internal GC">
                    <a-table size="small" bordered :dataSource="internal" :columns="columns">
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'view'">
                                <a-button size="small" block type="primary" @click="view(record.spexgc_id, 'internal')">
                                    <PicRightOutlined />Details
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </a-tab-pane>
                <a-tab-pane key="2" tab="Special External GC" force-render>
                    <a-table bordered size="small" :dataSource="external" :columns="columns">
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'view'">
                                <a-button size="small" block type="primary" @click="view(record.spexgc_id, 'external')">
                                    <PicRightOutlined />Details
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </a-tab-pane>
            </a-tabs>
        </a-card>
        <a-modal v-model:open="open">
            
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    internal: Object,
    external: Object
})
const open = ref(false)

const activeKey = ref('1')

const columns = [
    {
        title: 'RFSEGC #',
        dataIndex: 'spexgc_num',
        width: '14%',
    },
    {
        title: 'Date Requested',
        dataIndex: 'dateReq',
        width: '14%'
    },
    {
        title: 'Date Validity',
        dataIndex: 'dateNeed',
        width: '14%'
    },
    {
        title: 'Total Denomination',
        dataIndex: 'totalDenom',
        width: '14%'
    },
    {
        title: 'Customer',
        dataIndex: 'spcus_acctname',
        width: '14%',
        ellipsis: true,
    },
    {
        title: 'Requested By',
        dataIndex: 'requestedBy',
        width: '14%'
    },
    {
        title: 'View',
        dataIndex: 'view',
        width: '14%'
    },
]

const view = (e, type) => {
    open.value = true
}


</script>