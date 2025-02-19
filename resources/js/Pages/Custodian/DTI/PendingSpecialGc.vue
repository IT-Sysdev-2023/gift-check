<template>
    <AuthenticatedLayout>
        {{ pending.data }}
        <a-tabs v-model:activeKey="activeKey">
            <a-tab-pane key="1" tab=" DTI Pending GC List (GC Holder Entry)">
                <a-card title="DTI Special GC">
                    <a-table :pagination="false" size="small" :dataSource="pending.data" :columns="columns">
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'view'">
                                <a-button @click="viewRequest(record.id)" type="primary">View
                                    <EyeOutlined />
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                    <Pagination class="mt-5" :datarecords="pending" />
                </a-card>
            </a-tab-pane>
        </a-tabs>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';

defineProps({
    pending: Object,

})

const activeKey = ref('1')

const viewRequest = (id) => {
    console.log(id)
}



const columns = [
    {
        title: 'RFSEGC #',
        dataIndex: 'id',
        key: 'rfsegc',
    },
    {
        title: 'Date Requested',
        dataIndex: 'dateRequested',
        key: 'datereq',
    },
    {
        title: 'Total Denomination',
        dataIndex: 'totalDenom',
        key: 'totaldenom',
    },
    {
        dataIndex: 'view',
        key: 'view',
        align: 'end'
    },

]


</script>