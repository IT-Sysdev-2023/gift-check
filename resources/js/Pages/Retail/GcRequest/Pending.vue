<template>
    <a-card title="Pending GC Request">
        <a-table :dataSource="data" :columns="columns" bordered :pagination="false">
            <template #bodyCell="{ text, record, column }">
                <div v-if="column.dataIndex === 'action'">
                    <a-button @click="view(record.sgc_id)" type="primary">
                        <PicLeftOutlined /> View
                    </a-button>
                </div>
            </template>
        </a-table>
    </a-card>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        columns: Object
    },
    methods: {
        view(data) {
            this.$inertia.get(route('retail.gcrequest.pending.detail'), {
                id: data
            })
        }
    }
}
</script>