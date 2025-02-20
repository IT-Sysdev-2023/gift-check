<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table size="small" :dataSource="props.records" :columns="columns" >
                <template #bodyCell="{column, record}">
                    <template v-if="column.key === 'reqby'">
                        <span class="uppercase">{{ record.firstname }}</span>, <span class="uppercase">{{ record.lastname }}</span>
                    </template>
                    <template v-if="column.key === 'action'">
                        <a-button type="dashed" @click="update(record.dti_num)">
                            Update
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
interface Record {
    records: {
        dti_num: number,
        dateNeed: string,
        dateReq:string,
        firstname: string,
        lastname: string,
        spcus_companyname: string,
        total: number,
    }
}
const props = defineProps<{
    records: Record
}>()
const columns = [
    {
        title: 'RFSEGC#',
        dataIndex: 'dti_num',
        key: 'name',
    },
    {
        title: 'Date Requested',
        dataIndex: 'dateReq',
        key: 'age',
    },
    {
        title: 'Date Needed',
        dataIndex: 'dateNeed',
        key: 'address',
    },
    {
        title: 'Total Denomination',
        dataIndex: 'total',
        key: 'address',
    },
    {
        title: 'Customer',
        dataIndex: 'spcus_companyname',
        key: 'customer',
    },
    {
        title: 'Requested By',

        key: 'reqby',
    },
    {
        title: 'Action',

        key: 'action',
    },
];

const update = (id: number) => {

}
</script>
