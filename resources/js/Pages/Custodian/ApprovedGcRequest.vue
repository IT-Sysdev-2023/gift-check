<template>
    <a-card>
        <a-tabs v-model:activeKey="activeKey" @change="table" type="card">
            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <apple-outlined />
                        Approved Request External
                    </span>
                </template>

                <a-table size="small" :data-source="record.data" :columns="columns" :pagination="false" bordered>
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key == 'setup'">
                            <a-button
                                @click="() => $inertia.get(route('custodian.approved.setup'), { id: record.spexgc_id })">
                                <template #icon>
                                    <FastForwardOutlined />
                                </template>
                                Setup Gc Request
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <pagination :datarecords="record" class="mt-5" />

            </a-tab-pane>
            <a-tab-pane key="2">
                <template #tab>
                    <span>
                        <android-outlined />
                        Approved Request Internal
                    </span>
                </template>
                <a-table size="small" :data-source="record.data" :columns="columns" :pagination="false" bordered>
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key == 'setup'">
                            <a-button
                                @click="() => $inertia.get(route('custodian.approved.setup'), { id: record.spexgc_id })">
                                <template #icon>
                                    <FastForwardOutlined />
                                </template>
                                Setup Gc Request
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <pagination :datarecords="record" class="mt-5" />
            </a-tab-pane>
        </a-tabs>

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
    data() {
        return {
            activeKey: '1',
        }
    },
    methods: {
        table(key) {

            let promo = '';

            if (key === '1') {
                promo = '0';
            } else {
                promo = '*';

            }
            this.$inertia.get(route('accounting.approved.request'), { promo: promo }, { preserveState: true });
        }
    }
}
</script>
