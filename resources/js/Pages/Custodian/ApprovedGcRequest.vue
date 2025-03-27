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
                <a-input-search allow-clear enter-button v-model:value="externalSearch" placeholder="Input search here..." style="width:25%; margin-left:75%"/>

                <a-table size="small" :data-source="record.data.data" :columns="columns" :pagination="false" bordered style="margin-top:10px; ">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key == 'setup'">
                            <a-button
                             style="background-color: #1890ff; color: white;"
                                @click="() => $inertia.get(route('custodian.approved.setup'), { id: record.spexgc_id })">
                                <template #icon>
                                    <FastForwardOutlined />
                                </template>
                                Setup GC Request
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <pagination :datarecords="record.data" class="mt-5" />

            </a-tab-pane>
            <a-tab-pane key="2">
                <template #tab>
                    <span>
                        <android-outlined />
                        Approved Request Internal
                    </span>
                </template>
                <a-input-search allow-clear enter-button v-model:value="internalSearch" placeholder="Input search here..." style="width:25%; margin-left:75%"/>

                <a-table size="small" :data-source="record.data.data" :columns="columns" :pagination="false" bordered style="margin-top:10px">
                    <template #bodyCell="{ column, record }">
                        <!-- {{record.company}} -->
                        <template v-if="column.key == 'setup'">
                            <a-button
                             style="background-color: #1890ff; color: white;"
                                @click="() => $inertia.get(route('custodian.approved.setup'), { id: record.spexgc_id })">
                                <template #icon>
                                    <FastForwardOutlined />
                                </template>
                                Setup GC Request
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <pagination :datarecords="record.data" class="mt-5" />
            </a-tab-pane>
        </a-tabs>

    </a-card>
    <!-- {{ record }} -->
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
            externalSearch: this.record.search,
            internalSearch: this.record.internalSearch1,
            activeKey: '1',
            promo: ''
        }
    },
    watch: {
        externalSearch(search){
            console.log(search)
            this.$inertia.get(route('custodian.approved.request'),{
                search:search
            },{
                preserveState: true
            });
        },
        internalSearch(search){
            console.log(search)
            const tab = {
               promo: this.promo,
               internalSearch1:search
            }
            this.$inertia.get(route('custodian.approved.request', tab),{

            },{
                preserveState: true
            });
        }
    },
   methods: {
    table(key) {
        this.activeKey = key;
        this.promo = key === '1' ? '0' : '*';

        this.$inertia.get(route('custodian.approved.request'), { promo: this.promo }, { preserveState: true });
    }
}

}
</script>
