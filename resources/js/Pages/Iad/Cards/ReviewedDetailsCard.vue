<template>
    <a-row :gutter="[16, 16]">
        {{ record }}
        <a-col :span="12">
            <a-descriptions size="small" layout="vertical" bordered>
                <a-descriptions-item label="Date  Requested">{{ record.spexgc_datereq }}</a-descriptions-item>
                <a-descriptions-item label="Date Needed">{{ record.spexgc_dateneed }}</a-descriptions-item>
                <a-descriptions-item label="Requested By">{{ record.user.full_name }}</a-descriptions-item>
                <a-descriptions-item label="Payment Tyoe">{{ record.spexgc_paymentype ? 'Cash' : 'Check'
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
                        <a-typography-text strong></a-typography-text>
                    </a-descriptions-item>
                    <a-descriptions-item label="Approved Date"><a-typography-text strong></a-typography-text></a-descriptions-item>
                    <a-descriptions-item :span="3" label="Prepared By"><a-typography-text strong></a-typography-text></a-descriptions-item>
                    <a-descriptions-item label="Approved By" :span="3">
                        <a-badge status="processing" :text="Running" />
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
        </a-col>
    </a-row>
</template>
<script setup>
defineProps({
    record: Object,
    doc: String,
})
</script>
