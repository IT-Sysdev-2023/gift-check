<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('iad.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                <Link :href="route('iad.special.external.approvedGc')">Approved Gc Review</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card>
            <a-tabs type="card" v-model:activeKey="activeKey">
                <a-tab-pane key="1" tab="Special External Gc Details">
                    <a-descriptions bordered size="small" layout="vertical">
                        <a-descriptions-item label="RFSEGC #" :labelStyle="{ fontWeight: 'bold' }">{{ records.spexgc_num
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Department" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.userAccessPageTitle
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Date Requested" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.spexgc_datereq }}</a-descriptions-item>
                        <a-descriptions-item label="Date Validity" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.spexgc_dateneed }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Customer" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.specialExternalCustomer?.spcus_acctname
                        }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Total Denomination" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.totalGcRequestItems }}
                        </a-descriptions-item>
                        <a-descriptions-item label="AR #" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.spexgc_payment_arnum }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Payment Type" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.spexgc_paymentype
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Payment Amount" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.spexgc_payment }}</a-descriptions-item>

                        <a-descriptions-item label="Request Remarks" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.spexgc_remarks }}</a-descriptions-item>
                        <a-descriptions-item label="Requested by" :labelStyle="{ fontWeight: 'bold' }">{{ records.user
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Date Approved" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.approvedRequest.reqap_date
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Documents" :labelStyle="{ fontWeight: 'bold' }">
                            <a-image :src="records?.approvedRequest.reqap_doc[0]['url']">
                            </a-image>
                        </a-descriptions-item>
                        <a-descriptions-item label="Approved Remarks" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.approvedRequest?.reqap_remarks
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Checked By" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.approvedRequest?.reqap_checkedby
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Approved By" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.approvedRequest?.reqap_approvedby
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Documents" :labelStyle="{ fontWeight: 'bold' }"
                            v-if="records.document">
                            <ant-image-preview :images="records?.document" />
                        </a-descriptions-item>
                        <a-descriptions-item label="Prepared By" :labelStyle="{ fontWeight: 'bold' }">{{
                            records.approvedRequest?.user?.full_name
                        }}</a-descriptions-item>
                    </a-descriptions>


                    <a-card class="mt-10">
                        <a-row :gutter="[16, 16]">
                            <a-col :span="8">
                                <a-button type="primary" block @click="scanGc">
                                    <FastForwardOutlined /> Scan GC
                                </a-button>
                                <a-button type="primary" block @click="reprint" class="mt-2">
                                    <PrinterOutlined /> Reprint Gc
                                </a-button>
                            </a-col>
                            <a-col :span="16">
                                <a-card>
                                    <a-form class="mt-10" :model="formState" name="basic" :label-col="{ span: 8 }"
                                        :wrapper-col="{ span: 16 }" autocomplete="off" @finish="onFinish">
                                        <a-form-item label="Remarks" name="remarks"
                                            :validate-status="formState.errors.remarks ? 'error' : ''"
                                            :help="formState.errors.remarks">
                                            <span class="text-red-500">*Required</span>
                                            <a-textarea v-model:value="formState.remarks" />
                                        </a-form-item>
                                        <a-form-item label="Total Gc Scanned" name="totalGc">
                                            <a-input-number readonly style="width: 100%;" :value="totalCount">
                                            </a-input-number>
                                        </a-form-item>
                                        <a-form-item label="Total Denomination" name="denomination">
                                            <a-input-number style="width: 100%;" readonly :value="totalDenom" />
                                        </a-form-item>

                                        <a-form-item :wrapper-col="{ offset: 8, span: 16 }">
                                            <a-button type="primary" html-type="submit" block>
                                                <FastForwardOutlined /> Submit
                                            </a-button>
                                        </a-form-item>
                                    </a-form>
                                </a-card>
                            </a-col>
                        </a-row>
                    </a-card>
                </a-tab-pane>
                <a-tab-pane key="2" tab="GC Holder" force-render>
                    <a-table size="small" bordered :dataSource="gcHolder" :columns="gcholderCol" :pagination="false" />
                </a-tab-pane>
            </a-tabs>
        </a-card>

        <a-modal v-model:open="openScanGc" title="Scan GC" :footer="null">
            <a-form :model="barcodeForm" name="basic" autocomplete="off" @finish="onFinishBarcode">
                <p class="mt-5 ml-1">Scan Barcode:</p>
                <a-form-item name="barcode" :rules="[
                    {
                        required: true,
                        message: 'Please input the Barcode!',
                    },
                ]" :validate-status="barcodeForm.errors.barcode ? 'error' : ''" :help="barcodeForm.errors.barcode">
                    <a-input-number size="large" v-model:value="barcodeForm.barcode"
                        @change="() => barcodeForm.errors.barcode = ''" style="width: 100%" />
                </a-form-item>

                <a-form-item>
                    <a-button block type="primary" html-type="submit">Scan Barcode</a-button>
                </a-form-item>
            </a-form>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";
import { onProgress } from "@/Mixin/UiUtilities";
import { notification } from "ant-design-vue";
import { PageWithSharedProps } from "@/types";

const page = usePage<PageWithSharedProps>().props;
const props = defineProps<{
    gcHolder: object,
    gcholderCol: object,
    totalBarcode: number,
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
    reviewedBy: string;
}

const barcodeForm = useForm<barcodeFormTypes>({
    barcode: 0,
});
const formState = useForm<formState>({
    remarks: "",
    reviewedBy: "",
});


const reprint = () => {
    const url = route("iad.special.external.reprint", {
        id: records.spexgc_id,
    });

    axios
        .get(url, { responseType: "blob" })
        .then((response) => {
            const file = new Blob([response.data], {
                type: "application/pdf",
            });
            const fileURL = URL.createObjectURL(file);
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            if (error.response && error.response.status === 404) {
                notification.error({
                    message: "File Not Found",
                    description: "Pdf is missing on the server!",
                });
            } else {
                notification.error({
                    message: "Error Occured!",
                    description: "An error occurred while generating the PDF.",
                });
            }
        });
};
const openScanGc = ref<boolean>(false);

const records = props.data.data;

const activeKey = ref("1");

const scanGc = () => {
    openScanGc.value = true;
};
const reprintGc = () => {
    console.log(records.spexgc_id);

    // const url = route("treasury.store.gc.reprint", { id: id });

    // axios
    //     .get(url, { responseType: "blob" })
    //     .then((response) => {
    //         const file = new Blob([response.data], {
    //             type: "application/pdf",
    //         });
    //         const fileURL = URL.createObjectURL(file);
    //         window.open(fileURL, "_blank");
    //     })
    //     .catch((error) => {
    //         if (error.response && error.response.status === 404) {
    //             alert("Pdf Not available");
    //         } else {
    //             console.error(error);
    //             alert("An error occurred while generating the PDF.");
    //         }
    //     });
};
const { openLeftNotification } = onProgress();
const totalBarcode = ref(props.totalBarcode)
const onFinish = () => {
    if (totalCount.value !== totalBarcode.value) {
        notification.warning({
            message: "Oops",
            description: "Please scan all barcodes first before submitting",
        });
        return;
    }
    formState.post(route("iad.special.external.gcreview", records.spexgc_id), {
        onSuccess: ({ props }) => {
            openLeftNotification(props.flash);
            if (props.flash.success) {
                router.visit(route("iad.dashboard"));
            }
        },
    });
};
const totalCount = ref();
const totalDenom = ref();
const onFinishBarcode = () => {
    barcodeForm.post(route("iad.special.external.barcode", records.spexgc_id), {
        onSuccess: ({ props }) => {
            openLeftNotification(props.flash);
            if (props.flash.success) {
                totalCount.value = props.flash.countSession ?? '';
                totalDenom.value = props.flash.denominationSession ?? '';
                openScanGc.value = false;
            }
        },
        preserveState: true,
    });
};
</script>
