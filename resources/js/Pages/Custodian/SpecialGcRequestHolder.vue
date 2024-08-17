<template>
    <a-breadcrumb class="mb-4">
        <a-breadcrumb-item>Dashboard</a-breadcrumb-item>
        <a-breadcrumb-item>Pending Gc Holders</a-breadcrumb-item>
    </a-breadcrumb>
    <a-card>
        <a-tabs v-model:activeKey="activeKey" @change="switchData">
            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <apple-outlined />
                        Pending Special External (GC Holder) List
                    </span>
                </template>
                <a-table :data-source="specExRecord.data" :columns="columns" size="small">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key == 'setup'">
                            <a-button
                                @click="() => $inertia.get(route('custodian.pendings.external.holder.setup'), { id: record.spexgc_id })">
                                <template #icon>
                                    <FastForwardOutlined />
                                </template>
                                Setup Gc Holders
                            </a-button>
                        </template>
                    </template>
                </a-table>
            </a-tab-pane>
            <a-tab-pane key="2">
                <template #tab>
                    <span>
                        <android-outlined />
                        Pending Special Internal (GC Holder) List
                    </span>
                </template>
                <a-table :data-source="specExRecord.data" :columns="columns" size="small">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key == 'setup'">
                            <a-button
                                @click="() => $inertia.get(route('custodian.pendings.external.holder.setup'), { id: record.spexgc_id })">
                                <template #icon>
                                    <FastForwardOutlined />
                                </template>
                                Setup Gc Holders
                            </a-button>
                        </template>
                    </template>
                </a-table>
            </a-tab-pane>
        </a-tabs>
    </a-card>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        specExRecord: Object,
        columns: Array,
        activeKey: String,
    },
    data() {
        return {
            activeKey: this.activeKey,
        }
    },
    methods: {
        switchData(data) {
            this.$inertia.get(route('custodian.pendings.holder.entry'), {
                activeKey: data,
            })
        }
    }
}
</script>
