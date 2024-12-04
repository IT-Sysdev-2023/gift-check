<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table bordered size="small" :data-source="records" :columns="columns">
                <template #bodyCell="{column, record}">
                    <template v-if="column.key === 'details'">
                        <a-button @click="pending(record.id)">
                            <AreaChartOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { router } from '@inertiajs/core';
import { ref } from 'vue';

defineProps < {
    records: Record
} >();

interface Record {
    id: number,
    request: string,
    requestAt: string,
    reqno: number,
    type: string,
    reqby: string,
}

const columns = ref([
    {
        title: 'Adjustment No',
        dataIndex: 'reqno',
    },
    {
        title: 'Date Requested',
        dataIndex: 'requestAt',
    },
    {
        title: 'Adjustment Requested',
        dataIndex: 'request',
    },
    {
        title: 'Adjustment Type',
        dataIndex: 'type',
    },
    {
        title: 'Prepared By',
        dataIndex: 'reqby',
    },
    {
        title: 'Action',
        key: 'details',
    },
]);


const pending = (id: number) => {
    router.get(route('finance.budgetad.approval', id))
}

</script>
