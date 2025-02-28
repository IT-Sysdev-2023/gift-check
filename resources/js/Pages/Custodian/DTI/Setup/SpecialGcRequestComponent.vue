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
                    <a-button @click="openManagersKey(1)" type="dashed" block class="pt-3 pb-9 text-center">
                        Print Request By Barocde
                    </a-button>
                </a-col>
                <a-col :span="12">
                    <a-button @click="openManagersKey(2)" type="dashed" block class="pt-3 pb-9 text-center">
                        Print Request By Barcode Range
                    </a-button>
                </a-col>
            </a-row>
            <div v-if="grantedByRange">
                <a-card class="mt-2" size="small">
                    <template #title>
                        <p class="text-center font-bold">
                            Print Request By Range
                        </p>
                    </template>
                    <div class="flex justify-between">
                        <div>
                            <a-typography-text keyboard>Barcode Start Here</a-typography-text>
                            <a-input-number v-model:value="formStatebr.barcodestart"
                                class="pt-3 pb-1 text-3xl w-full" />
                        </div>
                        <div>
                            <a-typography-text keyboard>Barcode Start Here</a-typography-text>
                            <a-input-number v-model:value="formStatebr.barcodeend" class="pt-3 pb-1 text-3xl w-full" />
                        </div>
                    </div>
                    <a-button class="mt-2" block type="primary" @click="printByBarcodeRange">
                        Print Barcode By Range
                    </a-button>
                </a-card>
            </div>
            <div v-else-if="grantedByBarcode">
                <a-card class="mt-2" size="small">
                    <template #title>
                        <p class="text-center font-bold">
                            Print Request By Barcode
                        </p>
                    </template>

                    <a-typography-text keyboard>Barcode Start Here</a-typography-text>
                    <a-input-number v-model:value="formStateb.barcode" allow-clear class="pt-3 pb-1 text-3xl w-full" />

                    <a-button @click="printByBarcode" class="mt-2" block type="primary">
                        Print Barcode By Barcode
                    </a-button>
                </a-card>
            </div>
        </a-col>
    </a-row>
    <managers-key-modal :keyValue="keyValue" @closemodal="close" v-model:open="managersKey"
        routeUrl="custodian.managers.key" />

</template>
<script setup lang="ts">

import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { notification } from 'ant-design-vue';

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

const grantedByRange = ref<boolean>(false);
const grantedByBarcode = ref<boolean>(false);

const keyValue = ref<number>(0);

const props = defineProps<{
    datarecords: Record
}>();

const formStatebr = useForm({
    id: props.datarecords.dti_num,
    status: '1',
    barcodestart: null as number | null,
    barcodeend: null as number | null,
});
const formStateb = useForm({
    id: props.datarecords.dti_num,
    status: '2',
    barcode: null as number | null,
});

const openManagersKey = (key: number) => {
    keyValue.value = key;
    managersKey.value = true;
}
const close = (data: any, keyValue: number) => {
    managersKey.value = false;
    if (data.status == 'success') {
        if (keyValue == 2) {
            grantedByRange.value = true;
            grantedByBarcode.value = false;
        } else if (keyValue == 1) {
            grantedByRange.value = false;
            grantedByBarcode.value = true;
        }
    };
}
const printByBarcodeRange = () => {
    formStatebr.get(route('custodian.check.print.barcode.dti'), {
        onSuccess: (res: any) => {
            notification[res.props.flash.status]({
                message: res.props.flash.title,
                description: res.props.flash.msg,
            });
        },
        preserveState: true,
    });
}
const printByBarcode = () => {
    formStateb.get(route('custodian.check.print.barcode.dti'), {
        onSuccess: (res: any) => {
            notification[res.props.flash.status]({
                message: res.props.flash.title,
                description: res.props.flash.msg,
            });
        },
        preserveState: true,
    });
}
</script>
