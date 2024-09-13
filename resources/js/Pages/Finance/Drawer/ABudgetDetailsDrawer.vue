<template>
    <a-drawer style="height: 550px;" title="View Approved Budget Details" :placement="'top'">
        <a-card>
            <a-row :gutter="[16, 16]">
                <a-col :span="12">
                    <a-descriptions size="small" layout="vertical" bordered>
                        <a-descriptions-item label="Br No.">{{ selected.br_no }}</a-descriptions-item>
                        <a-descriptions-item label="Date Requested">{{ selected.reqat }}</a-descriptions-item>
                        <a-descriptions-item label="Time Requested">{{ selected.time }}</a-descriptions-item>
                        <a-descriptions-item label="Date Needed" :span="3">{{ selected.needed }}</a-descriptions-item>
                        <a-descriptions-item label="Budget Requested" :span="3">
                            <a-badge status="processing" :text="selected.budgetReq" />
                        </a-descriptions-item>
                        <a-descriptions-item label="Request Remarks">{{ selected.br_remarks }}</a-descriptions-item>
                        <a-descriptions-item label="Prepared By" :span="3">{{ selected.user.full_name
                            }}</a-descriptions-item>
                        <a-descriptions-item label="Documents" :span="3">
                            <div class="flex justify-center">
                                <a-card v-if="selected.br_file_docno">
                                    <a-image style="width: 300px; height: 200px; border-radius: 10px;"
                                        :src="'/storage/BudgetRequestScanCopy/' + selected.br_file_docno">

                                    </a-image>
                                </a-card>
                                <a-empty v-else>
                                    <template #description>
                                        Oops It seems like there is no Document Uploaded!
                                    </template>
                                </a-empty>
                            </div>
                        </a-descriptions-item>
                    </a-descriptions>
                </a-col>
                <a-col :span="12">
                    <a-descriptions-item label="Config Info">
                        <a-card>
                            <a-descriptions title="Other Info" size="small" layout="vertical" bordered>
                                <a-descriptions-item :span="3" label="Date Approved">{{ selected.apreqat
                                    }}</a-descriptions-item>
                                <a-descriptions-item label="Approved Remarks" :span="3">
                                    <a-badge status="processing" :text="selected.approved_budget_remark" />
                                </a-descriptions-item>
                                <a-descriptions-item label="Approved By">{{ selected.abr_approved_by
                                    }}</a-descriptions-item>
                                <a-descriptions-item label="Checked By">{{ selected.abr_checked_by
                                    }}</a-descriptions-item>
                                <a-descriptions-item label="Prepared By">{{ selected.firstname }}, {{ selected.lastname
                                    }}</a-descriptions-item>
                                <a-descriptions-item label="Documents">
                                    <div class="flex justify-center">
                                        <a-card v-if="selected.abr_file_doc_no">
                                            <a-image style="width: 300px; height: 200px; border-radius: 10px;"
                                                :src="'/storage/financeUpload/' + selected.abr_file_doc_no">
                                            </a-image>
                                        </a-card>
                                        <a-empty v-else>
                                            <template #description>
                                                Oops It seems like there is no Document Uploaded!
                                            </template>
                                        </a-empty>
                                    </div>

                                </a-descriptions-item>
                            </a-descriptions> </a-card>
                    </a-descriptions-item>
                </a-col>
            </a-row>
        </a-card>
    </a-drawer>
</template>
<script setup>
defineProps({
    selected: Object
})
</script>
