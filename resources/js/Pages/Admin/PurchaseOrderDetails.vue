<template>
    <a-card>
        <a-button class="mb-2" @click="modal">
            <InboxOutlined />
            Add Purchase Order
        </a-button>
        <a-table :data-source="record" :columns="columns" size="small" :pagination="false" :rowKey="record => record.id"
            expandable="{ expandedRowRender }">
            <template #expandedRowRender="{ record }">
                <a-card>
                    <a-row :gutter="[16, 16]">
                        <a-col :span="12">
                            <a-descriptions size="small" layout="horizontal" bordered>
                                <a-descriptions-item label="Reference Purchase Order No.">{{ record.ref_po_no
                                    }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions size="small" layout="horizontal" bordered>
                                <a-descriptions-item label="Deparment Code">{{ record.dep_code }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions size="small" layout="horizontal" bordered>
                                <a-descriptions-item label="Location Code.">{{ record.loc_code }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions size="small" layout="horizontal" bordered>
                                <a-descriptions-item label="Receiving No.">{{ record.rec_no }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions size="small" layout="horizontal" bordered>
                                <a-descriptions-item label="Reference No.">{{ record.ref_no }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions size="small" layout="horizontal" bordered>
                                <a-descriptions-item label="Remarks.">{{ record.remarks }}</a-descriptions-item>
                            </a-descriptions>
                        </a-col>
                        <a-col :span="12">
                            <a-descriptions size="small" layout="horizontal" bordered>
                                <a-descriptions-item label="Check By">{{ record.check_by }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions size="small" layout="horizontal" bordered>
                                <a-descriptions-item label="Prepare By">{{ record.prep_by }}</a-descriptions-item>
                            </a-descriptions>
                            <a-table :pagination="false" size="small" class="mt-2" :data-source="record.requis_form_denom" :columns="[
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
        </a-table>
    </a-card>
    <purchase-orders :denom="denomination" v-model:open="openmodal"/>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        record: Object,
        columns: Array,
        denomination: Object,
    },
    data(){
        return {
            openmodal: false,
        }
    },
    methods: {
        modal(){
            this.openmodal = true;
        }
    }
}
</script>
