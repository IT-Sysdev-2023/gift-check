<template>
    <a-card title="Pending GC Request">
        <a-table :dataSource="data" :columns="columns" :pagination="false">
            <template #bodyCell="{ text, record, column }">
                <div v-if="column.dataIndex === 'action'">
                    <a-button @click="viewDetails(record)">
                        <template #icon>
                            <EyeOutlined />
                        </template>
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
    }
}
</script>