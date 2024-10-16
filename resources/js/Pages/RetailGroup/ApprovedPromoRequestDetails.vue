<template>
    <AuthenticatedLayout>
        <a-tabs v-model:activeKey="activeKey" type="card">
            <a-tab-pane key="1" :tab="'Promo Gc Request # ' + records.data.pgcreq_reqnum + ''">
                <a-descriptions size="small" layout="vertical" bordered>
                    <a-descriptions-item label="Date Requested">{{ records.data.pgcreq_datereq }}</a-descriptions-item>
                    <a-descriptions-item label="Date Needed">{{ records.data.pgcreq_dateneeded }}</a-descriptions-item>
                    <a-descriptions-item label="Document">
                        <a-image :src="'storage/' + records.data.pgcreq_doc">

                        </a-image>
                    </a-descriptions-item>
                    <a-descriptions-item label="Promo Group">{{ records.data.pgcreq_group }}</a-descriptions-item>
                    <a-descriptions-item label="Requested By" :span="2">{{ records.data.user_reqby.full_name
                        }}</a-descriptions-item>
                    <a-descriptions-item label="Remarks" :span="2">{{ records.data.pgcreq_remarks
                        }}</a-descriptions-item>
                </a-descriptions>
                <a-divider></a-divider>
                <a-card title="Request Computation">
                    <a-table :pagination="false" size="small" bordered :data-source="records.denom.denomdata" :columns="[
                        {
                            title: 'Denomination',
                            dataIndex: 'denomination',
                        },
                        {
                            title: 'Qty',
                            dataIndex: 'pgcreqi_qty',
                        },
                        {
                            title: 'Subtotal',
                            dataIndex: 'sub',
                        },
                    ]">
                    </a-table>
                    <a-divider>Total: {{ records.denom.total }}</a-divider>
                </a-card>
            </a-tab-pane>
            <a-tab-pane key="2" tab="Promo Request Details">
                <a-row :gutter="[16, 16]">
                    <a-col :span="12">
                        <a-card title="Recommendation Details">
                            <a-descriptions size="small" layout="vertical" bordered>
                                <a-descriptions-item label="Date Recommended">{{ records.data.reqdate
                                    }}</a-descriptions-item>
                                <a-descriptions-item label="Time Recommended">{{ records.data.reqtime
                                    }}</a-descriptions-item>

                                <a-descriptions-item label="Recommended By">{{ records.data.approved_req.user.full_name }}</a-descriptions-item>
                                <a-descriptions-item label="Remarks" :span="3">{{ records.data.approved_req.reqap_remarks }}</a-descriptions-item>
                                <a-descriptions-item :span="3" label="Document">
                                    <a-image :src="'storage/'  + records.data.approved_req.reqap_doc" alt="image"></a-image>
                                </a-descriptions-item>
                            </a-descriptions>
                        </a-card>
                    </a-col>
                    <a-col :span="12">
                        <a-card title="Approved Details">
                            <a-descriptions size="small" layout="vertical" bordered>
                                <a-descriptions-item label="Date Approved">{{ records.approved.reqdate }}</a-descriptions-item>
                                <a-descriptions-item label="Time Approved">{{records.approved.reqtime}}</a-descriptions-item>
                                <a-descriptions-item label="Checked By ">{{records.approved.reqap_checkedby}}</a-descriptions-item>
                                <a-descriptions-item label="Approved By ">{{ records.approved.reqap_approvedby }}</a-descriptions-item>
                                <a-descriptions-item label="Prepared By" :span="2">{{ records.approved.user.full_name }}</a-descriptions-item>
                                    <a-descriptions-item label="Document">
                                        <a-image :src="'storage/'+ records.approved.reqap_doc"></a-image>
                                    </a-descriptions-item>
                                <a-descriptions-item label="Remarks" :span="2">{{ records.approved.reqap_remarks }}</a-descriptions-item>
                            </a-descriptions>
                        </a-card>
                    </a-col>
                </a-row>
            </a-tab-pane>
        </a-tabs>
    </AuthenticatedLayout>
</template>
<script setup>

import { ref } from 'vue';

const props = defineProps({
    records: Object
});

const activeKey = ref('1');
</script>
