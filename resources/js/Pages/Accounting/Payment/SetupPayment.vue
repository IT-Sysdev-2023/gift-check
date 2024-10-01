<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="10">
                <a-descriptions size="small" layout="vertical" bordered>
                    <a-descriptions-item label="RFSEGC #">{{ record.spexgc_num }}</a-descriptions-item>
                    <a-descriptions-item label="Department">{{ record.title }}</a-descriptions-item>
                    <a-descriptions-item label="Date Requested">{{ record.datereq }}</a-descriptions-item>
                    <a-descriptions-item label="Time Requested">{{ record.timereq }}
                    </a-descriptions-item>
                    <a-descriptions-item label="Date Needed">{{ record.dateneeded }}</a-descriptions-item>
                    <a-descriptions-item label="Date Approved">{{ record.dateapp }}</a-descriptions-item>
                    <a-descriptions-item :span="2" label="Payment Amount">{{ Number(record.spexgc_payment).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</a-descriptions-item>
                    <a-descriptions-item label="Total Denomination">{{ total }}</a-descriptions-item>
                    <a-descriptions-item :span="3" label="Request Remarks">
                        <a-badge status="processing" :text="record.spexgc_remarks" />
                    </a-descriptions-item>
                    <a-descriptions-item :span="3" label="Approved Remarks">
                        <a-badge status="processing" :text="record.reqap_remarks" />
                    </a-descriptions-item>
                </a-descriptions>
                <a-divider class="mt-2"><span style="font-size: 12px;">Committee</span></a-divider>
                <a-descriptions class="mt-5" size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Requested by">{{ record.reqby }}</a-descriptions-item>
                </a-descriptions>
                <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Checked By">{{ record.reqap_checkedby }}</a-descriptions-item>
                </a-descriptions>
                <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Approved By">{{ record.reqap_approvedby }}</a-descriptions-item>
                </a-descriptions>
                <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Prepared By ">{{ record.prepby }}</a-descriptions-item>
                </a-descriptions>
            </a-col>
            <a-col :span="14">
                <a-card>
                    <payment-setup-form :id="record.spexgc_id" :accname="record.spcus_acctname" :balance="record.spexgc_balance" @updated-counts="handleCount" />
                </a-card>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>
<script setup>
import { ref } from 'vue';

const props = defineProps({
    record: Object,
});

const total = ref(0);

const handleCount = (value) => {
    total.value = Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}




</script>
