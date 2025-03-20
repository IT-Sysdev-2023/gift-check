<template>
    <div v-if="gctype == 'external'">
        <a-card title="Pending Special External GC Request">
            <a-table :dataSource="external" :columns="columns">
                <template v-slot:bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'View'">
                        <a-button type="primary" @click="ApprovalFormLink(record, 'external')">
                            <PicLeftOutlined />View
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
    </div>
    <div v-else>
        <a-card title="Pending Special External GC Request">
            <a-table :dataSource="internal" :columns="columns">
                <template v-slot:bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'View'">
                        <a-button type="primary" @click="ApprovalFormLink(record, 'internal')">
                            <PicLeftOutlined />View
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
    </div>

</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    layout: AuthenticatedLayout,
    props: {
        external: Object,
        internal: Object,
        columns: Object,
        gctype: Array
    },
    methods: {
        ApprovalFormLink(data, type) {
            console.log(type)
            this.$inertia.get(route('finance.pendingGc.approval.form'), {
                id: data.spexgc_id,
                gcType: type
            })
        },
    }


}
</script>
