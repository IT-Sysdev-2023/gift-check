<template>
    <a-tabs v-model:activeKey="activeKey" type="card">
        <a-tab-pane key="1">
            <template #tab>
                <span>
                    <apple-outlined />
                    Purchase Order List
                </span>
            </template>
            <a-card>
                <a-table :data-source="record.data" :columns="columns" bordered size="small" :pagination="false"
                    :rowKey="record => record.id" expandable="{ expandedRowRender }">
                    <template #expandedRowRender="{ record }">
                        <a-card>
                            <a-row :gutter="[16, 16]">
                                <a-col :span="12">
                                    <a-descriptions size="small" layout="horizontal" bordered>
                                        <a-descriptions-item style="width: 50%;" label="Reference Purchase Order No.">{{
                                            record.ref_po_no
                                            }}</a-descriptions-item>
                                    </a-descriptions>
                                    <a-descriptions size="small" layout="horizontal" bordered>
                                        <a-descriptions-item style="width: 50%;" label="Deparment Code">{{
                                            record.dep_code
                                            }}</a-descriptions-item>
                                    </a-descriptions>
                                    <a-descriptions size="small" layout="horizontal" bordered>
                                        <a-descriptions-item style="width: 50%;" label="Location Code.">{{
                                            record.loc_code
                                            }}</a-descriptions-item>
                                    </a-descriptions>
                                    <a-descriptions size="small" layout="horizontal" bordered>
                                        <a-descriptions-item style="width: 50%;" label="Receiving No.">{{ record.rec_no
                                            }}</a-descriptions-item>
                                    </a-descriptions>
                                    <a-descriptions size="small" layout="horizontal" bordered>
                                        <a-descriptions-item style="width: 50%;" label="Reference No.">{{ record.ref_no
                                            }}</a-descriptions-item>
                                    </a-descriptions>
                                    <a-descriptions size="small" layout="horizontal" bordered>
                                        <a-descriptions-item style="width: 50%;" label="Remarks.">{{ record.remarks
                                            }}</a-descriptions-item>
                                    </a-descriptions>
                                </a-col>
                                <a-col :span="12">
                                    <a-descriptions size="small" layout="horizontal" bordered>
                                        <a-descriptions-item style="width: 50%;" label="Check By">{{ record.check_by
                                            }}</a-descriptions-item>
                                    </a-descriptions>
                                    <a-descriptions size="small" layout="horizontal" bordered>
                                        <a-descriptions-item style="width: 50%;" label="Prepare By">{{ record.prep_by
                                            }}</a-descriptions-item>
                                    </a-descriptions>
                                    <a-table bordered :pagination="false" size="small" class="mt-2"
                                        :data-source="record.requis_form_denom" :columns="[
                                            {
                                                title: 'Fad Item No.',
                                                dataIndex: 'denom_no',
                                            },
                                            {
                                                title: 'Quantity',
                                                dataIndex: 'quantity',
                                            },
                                        ]">

                                    </a-table>
                                </a-col>
                            </a-row>

                        </a-card>
                    </template>
                    <template #expandColumnTitle>
                        <span style="color: #179BAE">Details</span>
                    </template>

                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key === 'action'">
                            <a-button @click="edit(record.id)">
                                <ImportOutlined />
                                Edit
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <pagination class="mt-5" :datarecords="record" />
            </a-card>
        </a-tab-pane>
        <a-tab-pane key="2">
            <template #tab>
                <span>
                    <android-outlined />
                    Add Purchase Order Form
                </span>
            </template>
            <add-order-details :denom="denomination" :supplier="supplier" />
        </a-tab-pane>
    </a-tabs>

    <purchase-order-drawer :open="editDrawer" @close-drawer="onClose"/>

</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

export default {
    layout: AuthenticatedLayout,
    props: {
        record: Object,
        columns: Array,
        denomination: Object,
        supplier: Object
    },
    data() {
        return {
            openmodal: false,
            activeKey: '1',
            editDrawer: false,
        }
    },
    methods: {
        modal() {
            this.openmodal = true;
        },
        edit(id) {
            axios.get(route('admin.edit.po', id))
            // this.editDrawer = true;
        },
        onClose(){
            this.editDrawer = false;
        }

    }
}
</script>
