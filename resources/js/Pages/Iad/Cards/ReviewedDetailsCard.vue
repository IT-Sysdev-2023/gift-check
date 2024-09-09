<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="12">
            <a-descriptions size="small" layout="vertical" bordered>
                <a-descriptions-item label="Date  Requested">{{  dayjs( record.spexgc_datereq ).format('MMM DD, YY')}}</a-descriptions-item>
                <a-descriptions-item label="Date Needed">{{ dayjs( record.spexgc_dateneed ).format('MMM DD, YY') }}</a-descriptions-item>
                <a-descriptions-item label="Requested By">{{ record.user.full_name }}</a-descriptions-item>
                <a-descriptions-item label="Payment Type">{{ record.spexgc_paymentype == '1' ? 'Cash' : 'Check'
                    }}</a-descriptions-item>
                <a-descriptions-item label="Amount" :span="2">{{ record.spexgc_payment }}</a-descriptions-item>
                <a-descriptions-item label="Remarks" :span="3">
                    <a-badge status="processing" :text="record.spexgc_remarks" />
                </a-descriptions-item>
                <a-descriptions-item label="Documents">
                    <div v-if="doc" class="flex justify-center">
                        <a-image :src="'storage/' + doc">
                        </a-image>
                    </div>
                    <div v-else>
                        <a-empty />
                    </div>
                </a-descriptions-item>
            </a-descriptions>
        </a-col>
        <a-col :span="12">
            <a-card>
                <a-descriptions size="small">
                    <a-descriptions-item :span="2" label="Checked By">
                        <a-typography-text strong>{{ record.approved_request1.reqap_approvedby }}</a-typography-text>
                    </a-descriptions-item>
                    <a-descriptions-item label="Approved Date"><a-typography-text strong>{{
                        dayjs(record.spexgc_datereq).format('MMM DD, YY')
                            }}</a-typography-text></a-descriptions-item>
                    <a-descriptions-item :span="3" label="Prepared By"><a-typography-text strong>{{
                        record.approved_request1.reqap_checkedby }}</a-typography-text></a-descriptions-item>
                    <a-descriptions-item :span="3" label="Approved By"><a-typography-text strong>{{
                        record.approved_request1.user.full_name }}</a-typography-text></a-descriptions-item>
                    <a-descriptions-item label="Remarks" :span="3">
                        <a-badge status="processing" :text="record.spexgc_remarks" />
                    </a-descriptions-item>
                    <a-descriptions-item label="Documents">
                        <div v-if="record.approved_request.reqap_doc">
                            <a-image>

                            </a-image>
                        </div>
                        <div v-else>
                            <a-empty />
                        </div>
                    </a-descriptions-item>
                </a-descriptions>
            </a-card>

            <a-card class="mt-4" v-if="approved">
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%" label="Date Reviewed">{{ dayjs(approved.reqap_date).format('MMM DD, YY') }}</a-descriptions-item>
                </a-descriptions>
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%" label="Remarks">{{ approved.reqap_remarks }}</a-descriptions-item>
                </a-descriptions>
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%" label="Reviewed by">{{ approved.user.full_name }}</a-descriptions-item>
                </a-descriptions>
            </a-card>
        </a-col>
    </a-row>
</template>
<script setup>
import dayjs from 'dayjs';

defineProps({
    record: Object,
    doc: String,
    approved: Object,
})
</script>
