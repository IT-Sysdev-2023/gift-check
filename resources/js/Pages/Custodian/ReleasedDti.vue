<template>
    <AuthenticatedLayout>
        <a-card title="Released Dti Special Barcodes">
            <a-table :data-source="records.data" :columns="columns" size="small" bordered>
                <template #bodyCell="{column, record}">
                    <template v-if="column.key === 'action'">
                        <a-button>
                            <MenuUnfoldOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';


interface Records {
    data: {
        id: number,
        num: number,
        datereq: string,
        dateneed: string,
        reqby: string,
        revby: string,
        acctname: string,
        reqappdate: string,
    }
}
defineProps < {
    records: Records
} > ();

const columns = ref([
    {
        title: 'RFSEGC',
        dataIndex: 'num',
    },
    {
        title: 'DATE REQUESTED',
        dataIndex: 'datereq',
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
        title: 'ACTION',
        key: 'action',
        align: 'center',
    },
]);
</script>
