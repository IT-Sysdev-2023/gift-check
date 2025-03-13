<template>
    <AuthenticatedLayout>
        <a-breadcrumb>
            <a-breadcrumb-item>
                <Link href="/">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                <p class="text-black">DTI Pending GC List (GC HolderEntry)</p>
            </a-breadcrumb-item>
        </a-breadcrumb>
        <a-tabs type="card" v-model:activeKey="activeKey" class="mt-5">
            <a-tab-pane key="1" tab=" DTI Pending GC List (GC Holder Entry)">
                <a-card size="small" title="DTI Special GC">
                    <a-table :pagination="false" size="small" :dataSource="pending.data" :columns="columns">
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'view'">
                                <a-button @click="viewRequest(record.dti_num)" type="primary">View
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
import { router } from '@inertiajs/core';
import { Link } from '@inertiajs/vue3';

defineProps({
    pending: Object,

})




const activeKey = ref('1')

const viewRequest = (id) => {
    router.get(route('custodian.dti_special_gcdti_gc_holder_entry'), {
        id: id
    })
}



const columns = [
    {
        title: 'RFSEGC #',
        dataIndex: 'dti_num',
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
        title: 'Payment Type',
        dataIndex: 'dti_paymenttype',
        key: 'totaldenom',
    },
    {
        title: 'Request By',
        dataIndex: 'reqby',
    },
    {
        dataIndex: 'view',
        key: 'view',
        align: 'end'
    },

]


</script>
