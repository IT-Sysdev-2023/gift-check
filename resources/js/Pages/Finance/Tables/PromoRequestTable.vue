<template>
    <a-tabs v-model:activeKey="activeKey" type="card">
        <a-tab-pane>
            <template #tab>
                <span>
                    <CheckCircleFilled />
                    {{ title }}
                </span>
            </template>
            <a-card>
                <div class="flex justify-end">
                    <a-input-search v-model:value="form.search" placeholder="input search text" style="width: 300px"
                        @search="onSearch" class="mb-2" />
                </div>
                <a-table size="small" bordered :data-source="record.data" :columns="columns" :pagination="false">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'open'">
                            <a-button @click="setup(record.req_id)">
                                <template #icon>
                                    <TagFilled />
                                </template>
                                Setup Approval
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <pagination-resource class="mt-4" :datarecords="record" />
            </a-card>
        </a-tab-pane>
            <a-tab-pane key="2" v-if="activeKey === '2'">
                <template #tab>
                    <span>
                        <TagsFilled />
                        Pending Promo Approval Setup
                    </span>
                </template>
                <PromoForApproval  :details="details"/>
            </a-tab-pane>
    </a-tabs>
</template>
<script>
import Authenticatedlayout from "@/Layouts/AuthenticatedLayout.vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import { TagsFilled } from "@ant-design/icons-vue";

export default {
    layout: Authenticatedlayout,
    props: {
        record: Object,
        columns: Object,
        title: String,
        activeKey: String,
        details: Object,
    },
    data() {
        return {
            form: {
                search: ''
            },
            activeKey: this.activeKey,
        }
    },
    methods: {
        setup(id) {
            this.$inertia.get(route('finance.pen.promo.request'), {
                id: id,
                activeKey: '2',
            })
        }
    },

    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get(
                    route(route().current()), {
                    ...pickBy(this.form)
                }, {
                    preserveState: true,
                }
                );
            }, 150),
        },
    }
};
</script>
