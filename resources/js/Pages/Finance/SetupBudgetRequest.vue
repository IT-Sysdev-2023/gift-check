<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="12">
                <a-card>
                    <setup-budget-request-form :record="record" :assign="assigny"/>
                </a-card>
            </a-col>
            <a-col :span="12">
                <a-card>
                    <a-divider>
                        <p style="font-size: 14px; font-weight: bold;"> Budget Request Details</p>
                    </a-divider>

                    <a-descriptions layout="horizontal" size="small" class="mt-1" bordered>
                        <a-descriptions-item style="width: 50%;" label="BR No.">{{ record.br_no }}</a-descriptions-item>
                    </a-descriptions>

                    <a-descriptions v-if="record.br_group != 0" layout="horizontal" size="small" class="mt-1" bordered>
                        <a-descriptions-item style="width: 50%;" label="Promo Group">Group {{ record.br_group
                            }}</a-descriptions-item>
                    </a-descriptions>

                    <a-descriptions layout="horizontal" size="small" class="mt-1" bordered>
                        <a-descriptions-item style="width: 50%;" label="Department">{{ record.user.access_page.title
                            }}</a-descriptions-item>
                    </a-descriptions>

                    <a-descriptions layout="horizontal" size="small" class="mt-1" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Request">{{ record.reqdate
                            }}</a-descriptions-item>
                    </a-descriptions>

                    <a-descriptions layout="horizontal" size="small" class="mt-1" bordered>
                        <a-descriptions-item style="width: 50%;" label="Time Request">{{ record.time
                            }}</a-descriptions-item>
                    </a-descriptions>

                    <a-descriptions layout="horizontal" size="small" class="mt-1" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Needed">{{ record.needed
                            }}</a-descriptions-item>
                    </a-descriptions>

                    <a-descriptions layout="horizontal" size="small" class="mt-1" bordered>
                        <a-descriptions-item style="width: 50%;" label="Budget Requested">{{ record.br_request
                            }}</a-descriptions-item>
                    </a-descriptions>

                    <a-descriptions layout="horizontal" size="small" class="mt-1" bordered>
                        <a-descriptions-item style="width: 50%;" label="Remarks">{{ record.br_remarks
                            }}</a-descriptions-item>
                    </a-descriptions>

                    <a-descriptions layout="horizontal" size="small" class="mt-1" bordered>
                        <a-descriptions-item style="width: 50%;" label="Requested By">{{ record.user.full_name
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <!-- {{ record.br_file_docno }} -->
                    <a-descriptions v-if="record.br_file_docno != ''" layout="horizontal" size="small" class="mt-1"
                        bordered>
                        <a-descriptions-item style="width: 50%;" label="Document">
                            <a-image style="height: 100px;" :src="'/storage/BudgetRequestScanCopy/' + record.br_file_docno">
                            </a-image>
                        </a-descriptions-item>
                    </a-descriptions>

                    <div v-if="record.br_group == 1">
                        <div v-if="record.br_preapprovedby == 1">
                            <a-divider>
                                <p style="font-size: 14px; font-weight: bold;"> Budget Recommendation Info</p>
                            </a-divider>
                            <a-descriptions layout="horizontal" size="small" class="mt-1" bordered>
                                <a-descriptions-item style="width: 50%;"
                                    label="Budget Status">Approved</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions layout="horizontal" size="small" class="mt-1" bordered>
                                <a-descriptions-item style="width: 50%;" label="Date Approved">{{ preapp.prapp_at
                                    }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions layout="horizontal" size="small" class="mt-1" bordered>
                                <a-descriptions-item style="width: 50%;" label="Time Approved">{{ preapp.time
                                    }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions v-if="preapp.prapp_doc" layout="horizontal" size="small" class="mt-1"
                                bordered>
                                <a-descriptions-item style="width: 50%;" label="Time Approved">
                                    <a-image :src="'storage/' + preapp.prapp_doc">
                                    </a-image></a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions layout="horizontal" size="small" class="mt-1" bordered>
                                <a-descriptions-item style="width: 50%;" label="Remarks">{{ preapp.prapp_remarks
                                    }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions layout="horizontal" size="small" class="mt-1" bordered>
                                <a-descriptions-item style="width: 50%;" label="Approved By">{{ preapp.user.full_name
                                    }}</a-descriptions-item>
                            </a-descriptions>
                        </div>
                        <div v-else>
                            <a-alert message="Eyes Here!"
                                :description="'Budget Request needs Retail Group '+ record.br_group + ' Approval.'" type="info"
                                show-icon />
                        </div>
                    </div>
                </a-card>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    record: Object,
    preapp: Object,
    assigny: Object,
});



</script>
