<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('iad.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                <Link :href="route('iad.special.external.approvedGc')"
                    >Approved Gc Review</Link
                >
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card>
            <a-tabs v-model:activeKey="activeKey">
                <a-tab-pane key="1" tab="Special External Gc Details">
                    <a-descriptions title="GC Info">
                        <a-descriptions-item
                            label="RFSEGC #"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{ records.spexgc_num }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Department"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{
                                records.userAccessPageTitle
                            }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Date Requested"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{ records.spexgc_datereq }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Date Validity"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{ records.spexgc_dateneed }}
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Customer"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{
                                records.specialExternalCustomer.spcus_acctname
                            }}
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Total Denomination"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{ records.totalGcRequestItems }}
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="AR #"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{ records.spexgc_payment_arnum }}
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Payment Type"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{
                                records.spexgc_paymentype
                            }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Payment Amount"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{ records.spexgc_payment }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Documents"
                            :labelStyle="{ fontWeight: 'bold' }"
                            v-if="records.document"
                        >
                            <ant-image-preview :images="records.document" />
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Request Remarks"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{ records.spexgc_remarks }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Requested by"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{ records.user }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Date Approved"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{
                                records.approvedRequest.reqap_date
                            }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Approved Document"
                            v-if="records.approvedRequest.reqap_doc"
                        >
                            <ant-image-preview
                                :images="records.approvedRequest.reqap_doc"
                            />
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Approved Remarks"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{
                                records.approvedRequest.reqap_remarks
                            }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Checked By"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{
                                records.approvedRequest.reqap_checkedby
                            }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Approved By"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{
                                records.approvedRequest.reqap_approvedby
                            }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Prepared By"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{
                                records.approvedRequest.user.full_name
                            }}</a-descriptions-item
                        >
                    </a-descriptions>
                    <a-button @click="scanGc"> Scan GC</a-button>

                    <a-card class="mt-10">
                        <a-form
                            :model="formState"
                            name="basic"
                            :label-col="{ span: 8 }"
                            :wrapper-col="{ span: 16 }"
                            autocomplete="off"
                            @finish="onFinish"
                        >
                            <a-form-item label="Remarks" name="remarks">
                                <a-input v-model:value="formState.remarks" />
                            </a-form-item>
                            <a-form-item
                                label="Total Gc Scanned"
                                name="totalGc"
                            >
                                <a-input-number
                                    v-model:value="formState.totalGcScanned"
                                />
                            </a-form-item>
                            <a-form-item
                                label="Total Denomination"
                                name="denomination"
                            >
                                <a-input-number
                                    v-model:value="formState.totalDenomination"
                                />
                            </a-form-item>

                            <a-form-item :wrapper-col="{ offset: 8, span: 16 }">
                                <a-button type="primary" html-type="submit"
                                    >Submit</a-button
                                >
                            </a-form-item>
                        </a-form>
                    </a-card>
                </a-tab-pane>
                <a-tab-pane key="2" tab="GC Holder" force-render
                    >GC Holder</a-tab-pane
                >
            </a-tabs>
        </a-card>

        <a-modal v-model:open="openScanGc" title="Scan GC" :footer="null">
            <a-form
                :model="barcodeForm"
                name="basic"
                autocomplete="off"
                @finish="onFinishBarcode"
            >
                <a-form-item
                    label="Barcode"
                    name="barcode"
                    :rules="[
                        {
                            required: true,
                            message: 'Please input the Barcode!',
                        },
                    ]"
                >
                    <a-input-number
                        v-model:value="barcodeForm.barcode"
                        style="width: 100%"
                    />
                </a-form-item>

                <a-form-item :wrapper-col="{ offset: 8, span: 16 }">
                    <a-button type="primary" html-type="submit"
                        >Submit</a-button
                    >
                </a-form-item>
            </a-form>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { onProgress } from "@/Mixin/UiUtilities";

const props = defineProps<{
    title: string;
    data: {
        data: {
            spexgc_id: number;
            spexgc_num: string;
            userAccessPageTitle: string;
            spexgc_datereq: string;
            spexgc_dateneed: string;
            spexgc_remarks: string;
            user: string;
            approvedRequest: {
                reqap_date: string;
                reqap_doc: string;
                reqap_remarks: string;
                reqap_checkedby: string;
                reqap_approvedby: string;
                user: {
                    full_name: string;
                };
            };

            specialExternalCustomer: {
                spcus_acctname: string;
            };
            totalGcRequestItems: string;
            spexgc_payment_arnum: string;
            spexgc_paymentype: number;
            spexgc_payment: string;
            document: any[];
        };
    };
}>();

interface barcodeFormTypes {
    barcode: number;
}
interface formState {
    remarks: string;
    totalGcScanned: number;
    totalDenomination: number;
    reviewedBy: string;
}

const barcodeForm = useForm<barcodeFormTypes>({
    barcode: 0,
});
const formState = useForm<formState>({
    remarks: "",
    totalGcScanned: 0,
    totalDenomination: 0,
    reviewedBy: "",
});
const openScanGc = ref<boolean>(false);

const records = props.data.data;

const activeKey = ref("1");

const scanGc = () => {
    openScanGc.value = true;
};
const { openLeftNotification } = onProgress();
const onFinish = () => {
    formState.post(route("iad.special.external.gcreview", records.spexgc_id), {
        onSuccess: ({ props }) => {
            openLeftNotification(props.flash);
            if (props.flash.success) {
                router.visit(route("iad.dashboard"));
            }
        },
    });
};

const onFinishBarcode = () => {
    barcodeForm.post(route("iad.special.external.barcode", records.spexgc_id), {
        onSuccess: ({ props }) => {
            openLeftNotification(props.flash);
            if (props.flash.success) {
                router.visit(route("iad.dashboard"));
            }
        },
    });
};
</script>
