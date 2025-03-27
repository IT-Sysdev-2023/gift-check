<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table bordered :data-source="records.data" :columns="columns" size="small" :pagination="false">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'action'">
                        <a-button 
                        type="primary" 
                         style="background-color: #1890ff; border-color: #1890ff; color: white;"
                          @click="setup(record.dti_num)">
                            Setup GC Request DTI
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination class="mt-4"  :datarecords="records"/>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">

import { router } from '@inertiajs/core';
import { ref } from 'vue';

interface Records {
    data: {
        dti_num: number,
        company: string,
        dti_needed: string,
        dti_datereq: string,
        dti_date: string,
        dti_approvedby: string,
    }
}
defineProps<{
    records: Records
}>();

const columns = ref([
    {
        title: 'RFSEGC#',
        dataIndex: 'dti_num',
        key: 'dti',
    },
    {
        title: 'Date Requested',
        dataIndex: 'dti_datereq',
        key: 'datereq',
    },
    {
        title: 'Date Validity',
        dataIndex: 'dti_dateneed',
        key: 'dateneed',
    },
    {
        title: 'Date Approved',
        dataIndex: 'dti_date',
        key: 'dateneed',
    },
    {
        title: 'Approved By',
        dataIndex: 'dti_approvedby',
        key: 'dateneed',
    },
    {
        title: 'Action',
        key: 'action',
        align: 'center',
    },
]);

const setup = (id: number) => {
    router.get(route('custodian.dti.setup.gc-request', id));
}
</script>
