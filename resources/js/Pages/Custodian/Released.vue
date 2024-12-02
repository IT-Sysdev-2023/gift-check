<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table :columns="columns" bordered :data-source="records.data" size="small" :pagination="false">
                <template #bodyCell="{column, record}">
                    <template v-if="column.key === 'details'" >
                       <a-button @click="details(record.id)">
                        <SnippetsOutlined />
                       </a-button>
                    </template>
                </template>
            </a-table>
            <pagination class="mt-4" :datarecords="records"/>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/core';
import { ref } from 'vue';


interface Records {
    id: number,
    num: number,
    datereq: string,
    dateneed: string,
    reqby: string,
    revby: string,
    acctname: string,
    reqappdate: string,
}

defineProps<{
    records: {
        data: Records
    }
}>();

const columns = ref([
    {
        title: 'RFSEGC',
        dataIndex: 'num',
        align: 'center',
    },
    {
        title: 'DATE REQUESTED',
        dataIndex: 'datereq',
    },
    {
        title: 'REQUESTED BY',
        dataIndex: 'reqby',
    },
    {
        title: 'CUSTOMER',
        dataIndex: 'acctname',
    },
    {
        title: 'DATE RELEASED',
        dataIndex: 'reqappdate',
    },
    {
        title: 'RELEASED BY',
        dataIndex: 'revby',
    },
    {
        title: 'Action',
        key: 'details',
    },
]);

const details = (id: number) => {
    router.get(route('custodian.detail', id))
}
</script>
