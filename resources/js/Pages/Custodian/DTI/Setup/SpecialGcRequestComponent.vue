<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="10">
            <a-card size="small" class="text-center">
                <a-descriptions size="small" title="Request Details" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Date Requested">{{ datarecords.dti_datereq
                        }}</a-descriptions-item>
                </a-descriptions>
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Request By">{{ datarecords.user.full_name
                        }}</a-descriptions-item>
                </a-descriptions>
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Date Validity">{{ datarecords.dti_dateneed
                        }}</a-descriptions-item>
                </a-descriptions>
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Payment Type">{{ datarecords.dti_paymenttype
                        }}</a-descriptions-item>
                </a-descriptions>
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Amount">Cloud Database</a-descriptions-item>
                </a-descriptions>
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Remarks">
                        <a-textarea readonly :value="datarecords.remarks" placeholder="Basic usage" :rows="2" />
                    </a-descriptions-item>
                </a-descriptions>
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Documents">
                        <div v-if="datarecords.dti_documents.dti_fullpath">
                            <a-image :src="'/storage/' + datarecords.dti_documents.dti_fullpath" />
                        </div>
                        <div v-else>
                            <a-empty />
                        </div>
                    </a-descriptions-item>
                </a-descriptions>
                <a-button class="mt-5 bg-orange-500 text-white" block>
                    Reprint this Request
                </a-button>
            </a-card>
        </a-col>
        <a-col :span="14">
            <a-card>
                <a-row :gutter="[16, 16]">
                    <a-col :span="14">
                        <a-descriptions size="small" title="Approved Request Details" layout="horizontal" bordered>
                            <a-descriptions-item style="width: 50%;" label="Date Approved">{{
                                datarecords.dti_approveddate }}</a-descriptions-item>
                        </a-descriptions>
                        <a-descriptions size="small" layout="horizontal" bordered>
                            <a-descriptions-item style="width: 50%;" label="Checked By">{{ datarecords.dti_checkby
                                }}</a-descriptions-item>
                        </a-descriptions>
                        <a-descriptions size="small" layout="horizontal" bordered>
                            <a-descriptions-item style="width: 50%;" label="Prepared By">{{ datarecords.prepby
                                }}</a-descriptions-item>
                        </a-descriptions>
                        <a-descriptions size="small" layout="horizontal" bordered>
                            <a-descriptions-item style="width: 50%;" label="Approved By">{{ datarecords.dti_approvedby
                                }}</a-descriptions-item>
                        </a-descriptions>
                    </a-col>
                    <a-col :span="10">
                        <a-divider>
                            Documents
                        </a-divider>
                        <a-card>
                            <div v-if="datarecords.dti_doc">
                                <a-image :src="'/storage/' + datarecords.dti_doc" />
                            </div>
                            <a-empty />
                        </a-card>
                    </a-col>
                </a-row>
                <p class="">
                    <a-typography-text keyboard>Remarks</a-typography-text>
                </p>
                <a-textarea readonly :value="datarecords.appremarks" placeholder="Remarks" :rows="2" />
            </a-card>
            <a-divider>
                <a-typography-text keyboard>Action Printable Button</a-typography-text>
            </a-divider>
            <a-row :gutter="[16, 16]" class="mt-5">
                <a-col :span="12">
                    <a-button @click="openManagersKey(1)" type="dashed" block class="pt-5 pb-10 text-center">
                        Print Request By Barocde
                    </a-button>
                </a-col>
                <a-col :span="12">
                    <a-button @click="openManagersKey(2)" type="dashed" block class="pt-5 pb-10 text-center">
                        Print Request By Barcode Range
                    </a-button>
                </a-col>
            </a-row>
        </a-col>
    </a-row>
    <managers-key-modal @closemodal="close" v-model:open="managersKey" routeUrl="custodian.managers.key"/>
</template>
<script setup lang="ts">

import { ref } from 'vue';

interface Record {
    dti_num: number,
    dti_dateneed: string,
    dti_datereq: string,
    dti_doc: string,
    appremarks: string,
    remarks: string,
    dti_paymenttype: string,
    dti_approvedby: string,
    dti_approveddate: string,
    dti_addby: number,
    dti_checkby: string,
    prepby: string,
    dti_documents: {
        dti_fullpath: string,
    },
    user: {
        full_name: string
    }
}

const managersKey = ref<boolean>(false);
const granted = ref<boolean>(false);

defineProps<{
    datarecords: Record
}>();

const openManagersKey = (key: number) => {
    managersKey.value = true;
}
const close = (data: any) => {
    managersKey.value = false;
    if(data.status == 'success'){
        granted.value = true;
    };
}

</script>
