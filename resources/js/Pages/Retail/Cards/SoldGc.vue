<template>
    <a-card title="Sold GC">
        <a-table size="small" :pagination="false" bordered :dataSource="soldGc" :columns="columns">
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'denomination'">
                    <a-tag color="#2db7f5">{{ record.denomination }}</a-tag>
                </template>
                <template v-if="column.dataIndex === 'total'">
                    <a-tag color="#00e1ff">{{ record.total }}</a-tag>
                </template>
            </template>
        </a-table>

        <div>
            <div class="flex justify-end mt-2">
                <a-button type="primary" size="small" @click="() => $inertia.get(route('retail.soldGc'))">
                    <SearchOutlined />View Sold GC's
                </a-button>
            </div>
        </div>
    </a-card>
</template>

<script>
export default {
    props: {
        soldGc: Object
    },
    data() {
        return {
            columns: [
                {
                    title: 'Denomination',
                    dataIndex: 'denomination',
                },
                {
                    title: 'Quantity',
                    dataIndex: 'total',
                }
            ],
        }
    },
}
</script>
