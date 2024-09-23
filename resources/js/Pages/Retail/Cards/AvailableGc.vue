<template>
    <div>
        <a-card title="Available GC">
            <a-table size="small" bordered :dataSource="availableGc" :columns="columns" :pagination="false">
                <template v-slot:bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'denomination'">
                        <a-tag color="#87d068">{{ record.denomination }}</a-tag>
                    </template>
                    <template v-if="column.dataIndex === 'count'">
                        <a-tag color="#108ee9">{{ record.count }}</a-tag>
                    </template>
                </template>
            </a-table>
            <div>
                <div class="flex justify-end mt-2">
                    <a-button size="small" @click="() => $inertia.get(route('retail.availableGcList'))" type="primary">
                        <SearchOutlined />View Available GC
                    </a-button>
                </div>
            </div>
        </a-card>
    </div>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
export default {
    layout: AuthenticatedLayout,
    props: {
        availableGc: Object
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
                    dataIndex: 'count',
                },
            ],
        }
    }

}
</script>