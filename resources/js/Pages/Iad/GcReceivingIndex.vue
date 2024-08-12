<template>
    <a-breadcrumb class="mb-4">
        <a-breadcrumb-item>Dashboard</a-breadcrumb-item>
        <a-breadcrumb-item>Fad Textfiles Details</a-breadcrumb-item>
    </a-breadcrumb>
    <a-card>
        <a-table bordered size="small" :data-source="record" :columns="columns">
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex == 'setup'">
                    <a-button @click="setup(record)">
                        <template #icon>
                            <ToolFilled />
                        </template>
                        Setup Textfile
                    </a-button>
                </template>
            </template>
        </a-table>
    </a-card>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,

    props: {
        record: Object,
        columns: Array,
    },
    methods: {
        setup(data){
            this.$inertia.get(route('iad.setup.receiving'), {
                requisId: data.req_no,
            });
        }
    }
}
</script>
