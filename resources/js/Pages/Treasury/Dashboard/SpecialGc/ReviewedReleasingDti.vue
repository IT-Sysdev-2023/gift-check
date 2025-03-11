<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table size="small" bordered :data-source="props.records.data" :columns="columns" :pagination="false" >
                <template #bodyCell="{column, record}">
                    <template v-if="column.key == 'action'">
                        <a-button @click="setup(record.dti_num)">
                            View Setup
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="props.records" class="mt-5"/>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { router } from '@inertiajs/core';
import { ref } from 'vue';

interface Records {
    data: {
        dti_num: number,
        dti_company: number,
        dti_dateneed: string,
        id: number,
        dti_datereq: string,
        recby: string,
        approvedby: string,
        reviewedby: string,
        totalDenom: number,
    }
}
const props = defineProps<{
    records: Records,
}>();

const columns = ref([
    {
        title: 'RFSEGC#',
        dataIndex: 'dti_num',
        key: 'name',
    },
    {
        title: 'Date Requested',
        dataIndex: 'dti_datereq',
        key: 'name',
    },
    {
        title: 'Date Needed',
        dataIndex: 'dti_dateneed',
        key: 'name',
    },
    {
        title: 'Total Denom',
        dataIndex: 'totalDenom',
        key: 'name',
    },
    {
        title: 'Customer',
        dataIndex: 'customer',
        key: 'name',
    },
    {
        title: 'Requested By',
        dataIndex: 'recby',
        key: 'name',
    },
    {
        title: 'Approved By',
        dataIndex: 'approvedby',
        key: 'name',
    },
    {
        title: 'Reviewed By',
        dataIndex: 'reviewedby',
        key: 'name',
    },
    {
        title: 'Action',
        key: 'action',
        align: 'center'
    },
]);
const setup = (id: number) => {
    router.get(route('treasury.special.gc.vReleasingSetup', id));
}

</script>
