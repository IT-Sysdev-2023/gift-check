<template>
    <AuthenticatedLayout>
        <Head title="Update Special External Gc" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route(dRoute)">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                <Link :href="route('treasury.special.gc.pending')"
                    >Pending Request</Link
                >
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>

        <a-card title="Update Special Gc Request">
            <a-form
                ref="formRef"
                name="advanced_search"
                class="ant-advanced-search-form"
                :model="form"
                @finish="onFinish"
            >
                <a-row :gutter="24">
                    <a-col :span="8">
                        <a-form-item label="Gc Request #:">
                            <a-input
                                :value="page.spexgc_num"
                                readonly
                            ></a-input>
                        </a-form-item>
                        <a-form-item label="Date Requested:">
                            <a-date-picker disabled :value="dateRequested" />
                        </a-form-item>
                        <a-form-item label="Date Validity:">
                            <a-date-picker v-model:value="form.dateValidity" />
                        </a-form-item>

                        <a-form-item label="Uploaded Document">
                            <a-space wrap class="ml-2">
                                <ant-image-preview
                                    :images="page?.document"
                                    v-if="page.document"
                                />

                                <a-tag color="error" v-else>
                                    <template #icon>
                                        <close-circle-outlined />
                                    </template>
                                    NONE
                                </a-tag>
                            </a-space>
                        </a-form-item>
                        <a-form-item label="Upload Scan Copy">
                            <ant-upload-multi-image
                                @handle-change="handleUploadChange"
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :span="16">
                        <a-card class="mb-10">
                            <a-row>
                                <a-col :span="12">
                                    <a-statistic
                                        title="Total Denomination"
                                        :value="totalDenomination"
                                        style="margin-right: 50px"
                                    />
                                </a-col>
                                <a-col :span="12">
                                    <a-statistic
                                        title="Updated By:"
                                        :precision="2"
                                        :value="pageProps.auth.user.full_name"
                                    />
                                </a-col>
                            </a-row>
                        </a-card>
                        <a-row :gutter="24">
                            <a-col :span="12">
                                <a-form-item label="Company Name:">
                                    <a-input
                                        :value="form.customer.company"
                                        readonly
                                    ></a-input>
                                </a-form-item>
                                <a-form-item label="Account Name:">
                                    <a-input
                                        :value="form.customer.account"
                                        readonly
                                    ></a-input>
                                </a-form-item>
                                <a-form-item label="Search Customer:">
                                    <ant-select
                                        :options="props.options"
                                        @handle-change="handleCustomer"
                                    />
                                </a-form-item>
                                <a-form-item label="AR Number:">
                                    <a-input
                                        v-model:value="form.arNo"
                                    ></a-input>
                                </a-form-item>

                                <a-form-item label="Payment Type:">
                                    <!-- {{form.paymentType}} -->
                                    <a-space>
                                        <a-select
                                            ref="select"
                                            v-model:value="
                                                form.paymentType.type
                                            "
                                            style="width: 120px"
                                            :options="paymentOptions"
                                        ></a-select>
                                    </a-space>
                                </a-form-item>
                                <payment-type :form="form" />
                                <a-form-item label="Remarks:">
                                    <a-textarea
                                        v-model:value="form.remarks"
                                        placeholder="placeholder"
                                    ></a-textarea>
                                </a-form-item>
                            </a-col>
                            <a-col :span="12">
                                <a-form-item>
                                    <ant-form-nest-denom :form="form" />
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>
                <a-row>
                    <a-col :span="24" style="text-align: right">
                        <a-button
                            type="primary"
                            html-type="submit"
                            :disabled="!form.isDirty"
                            >Submit</a-button
                        >
                    </a-col>
                </a-row>
            </a-form>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { route } from 'ziggy-js';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { dashboardRoute } from "@/Mixin/UiUtilities";
import { usePage, useForm } from "@inertiajs/vue3";
import type { SelectProps, UploadChangeParam } from "ant-design-vue";
import { ref, computed } from "vue";
import type { FormInstance } from "ant-design-vue";
import dayjs, { Dayjs } from "dayjs";
import { notification } from "ant-design-vue";
import { PageWithSharedProps } from "@/types";

const dRoute = dashboardRoute();
const pageProps = usePage<PageWithSharedProps>().props;
const props = defineProps<{
    title: string;
    options: {
        value: number;
        label: string;
    }[];
    // assignedCustomer: {};
    data: {
        data: {
            spexgc_id: number;
            spexgc_type: string;
            specialExternalCustomer: {
                spcus_acctname: string;
                spcus_address: string;
                spcus_at: string;
                spcus_by: number;
                spcus_cnumber: string;
                spcus_companyname: string;
                spcus_cperson: string;
                spcus_id: number;
                spcus_type: number;
            };
            // specialExternalGcrequestEmpAssign: {
            //     denomination: number;
            //     id: number;
            //     qty: number;
            //     primary_id: number;
            // }[];
            specialExternalGcrequestItems: {
                denomination: number;
                id: number;
                qty: number;
                primary_id: number;
            }[];
            specialExternalBankPaymentInfo: any;
            spexgc_company: string;
            spexgc_payment: string;
            spexgc_payment_arnum: string;
            spexgc_paymentype: string;
            spexgc_dateneed: string;
            spexgc_datereq: string;
            spexgc_num: string;
            spexgc_remarks: string;
            document: string;
        };
    };
}>();

const page = props.data.data;
const dateRequested = <Dayjs>dayjs(page.spexgc_datereq);

const form = useForm({
    file: null,
    dateValidity: dayjs(page.spexgc_dateneed),
    // denom: props.assignedCustomer,
    // defaultAssigned: page.specialExternalGcrequestEmpAssign,
    customer: {
        company: page.specialExternalCustomer.spcus_companyname,
        account: page.specialExternalCustomer.spcus_acctname,
        value: page.specialExternalCustomer.spcus_id,
    },
    arNo: page.spexgc_payment_arnum,
    paymentType: {
        bankName: page.specialExternalBankPaymentInfo?.spexgcbi_bankname ?? "",
        accountNumber:
            page.specialExternalBankPaymentInfo?.spexgcbi_bankaccountnum ?? "",
        checkNumber:
            page.specialExternalBankPaymentInfo?.spexgcbi_checknumber ?? "",
        type: page.spexgc_paymentype,
        amount: page.spexgc_payment,
    },
    denomination: page.specialExternalGcrequestItems,
    remarks: page.spexgc_remarks,
});

const totalDenomination = computed(() => {
    return form.denomination.reduce((acc, item) => {
        return acc + item.denomination * item.qty;
    }, 0);
});

const paymentOptions = ref<SelectProps["options"]>([
    {
        value: "1",
        label: "Cash",
    },
    {
        value: "2",
        label: "Check",
    },
]);
const formRef = ref<FormInstance>();
const onFinish = () => {
    form.transform((data) => ({
        ...data,
        file: data.file?.map((item) => item.originFileObj),
        companyId: page.spexgc_company,
        type: page.spexgc_type,
        reqid: page.spexgc_id,
        totalDenom: totalDenomination.value,
        dateValidity: dayjs(data.dateValidity).format("YYYY-MM-DD"),
    })).post(route("treasury.special.gc.update.special"), {
        onSuccess: ({ props }) => {
            if (props.flash.success) {
                notification.success({
                    message: "Success!",
                    description: props.flash.success,
                });
            }
            if (props.flash.error) {
                notification.error({
                    message: "Error!",
                    description: props.flash.error,
                });
            }
        },
    });
};

const handleUploadChange = (info: UploadChangeParam) => {
    form.file = info.fileList;
};

const handleCustomer = (value, obj) => {
    form.customer = {
        account: obj.account_name,
        company: obj.label,
        value: obj.value,
    };
};
</script>
