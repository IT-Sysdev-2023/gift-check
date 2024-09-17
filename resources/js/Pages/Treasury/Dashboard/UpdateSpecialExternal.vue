<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { dashboardRoute } from "@/Mixin/UiUtilities";
import AntFormNest from "@/Components/Treasury/AntFormNest.vue";
import { PageProps } from "../../../types/index";
import { usePage, useForm } from "@inertiajs/vue3";
import type { SelectProps, UploadChangeParam } from "ant-design-vue";
import { ref } from "vue";
import type { FormInstance } from "ant-design-vue";
import dayjs, { Dayjs } from "dayjs";

const dRoute = dashboardRoute();
const props = defineProps<{
    title: String;
    options: {
        value: number;
        label: string;
    }[];
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
            specialExternalGcrequestEmpAssign: {
                denom: number;
                id: number;
                qty: number;
                primary_id: number
            }[];
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
const dateValidity = ref<Dayjs>(dayjs(page.spexgc_dateneed));

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
const paymentTypeSelected = ref(page.spexgc_paymentype);
const formRef = ref<FormInstance>();
const onFinish = (values: any) => {
    console.log("Received values of form: ", values);
    console.log("formState: ", formState);
};
const formState = useForm({
    file: null,
});

const handleUploadChange = (info: UploadChangeParam) => {
    // formState.file = info.file;
};
</script>
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
                :model="formState"
                @finish="onFinish"
            >
                <a-row :gutter="24">
                    <a-col :span="8">
                        <a-form-item label="Gc Request #:">
                            <a-input :value="page.spexgc_id" readonly></a-input>
                        </a-form-item>
                        <a-form-item label="Date Requested:">
                            <a-date-picker disabled :value="dateRequested" />
                        </a-form-item>

                        <a-form-item label="Company Name:">
                            <a-textarea
                                :value="
                                    page.specialExternalCustomer
                                        .spcus_companyname
                                "
                                readonly
                            ></a-textarea>
                        </a-form-item>
                        <a-form-item label="Account Name:">
                            <a-textarea
                                :value="
                                    page.specialExternalCustomer.spcus_acctname
                                "
                                readonly
                            ></a-textarea>
                        </a-form-item>
                        <a-form-item label="Uploaded Document">
                            <a-space wrap class="ml-2">
                                <ant-image-preview :images="page?.document" v-if="page.document"/>
                               
                                <a-tag color="error" v-else>
                                    <template #icon>
                                        <close-circle-outlined />
                                    </template>
                                    NONE
                                </a-tag>
                            </a-space>
                        </a-form-item>
                        <a-form-item label="Upload Scan Copy">
                            <ant-upload-image />
                        </a-form-item>
                    </a-col>
                    <a-col :span="16">
                        <a-card class="mb-10">
                            <a-row>
                                <a-col :span="12">
                                    <a-statistic
                                        title="Total"
                                        :value="page.spexgc_payment"
                                        style="margin-right: 50px"
                                    />
                                </a-col>
                                <a-col :span="12">
                                    <a-statistic
                                        title="Updated By:"
                                        :precision="2"
                                        :value="$page.props.auth.user.full_name"
                                    />
                                </a-col>
                            </a-row>
                        </a-card>
                        <a-row :gutter="24">
                            <a-col :span="12">
                                <a-form-item label="Date Validity:">
                                    <a-date-picker
                                        v-model:value="dateValidity"
                                    />
                                </a-form-item>
                                <a-form-item label="Search Customer:">
                                    <ant-select :options="props.options" />
                                </a-form-item>
                                <a-form-item label="AR Number:">
                                    <a-input
                                        :value="page.spexgc_payment_arnum"
                                    ></a-input>
                                </a-form-item>

                                <a-form-item label="Payment Type:">
                                    <a-space>
                                        <a-select
                                            ref="select"
                                            v-model:value="paymentTypeSelected"
                                            style="width: 120px"
                                            :options="paymentOptions"
                                        ></a-select>
                                    </a-space>
                                </a-form-item>
                                <!-- <a-form-item label="Bank Name" v-if="page.spexgc_paymentype == 1 ">
                            <a-input
                                :value="page.spexgcbi_bankname"
                                placeholder="placeholder"
                            ></a-input>
                        </a-form-item>
                        <a-form-item label="Amount" v-if="page.spexgc_paymentype == 1 ">
                            <a-input
                                :value="page.spexgcbi_checknumber"
                                placeholder="placeholder"
                            ></a-input>
                        </a-form-item> -->
                                <a-form-item label="Amount">
                                    <a-input
                                        type="number"
                                        :value="page.spexgc_payment"
                                        placeholder="placeholder"
                                    ></a-input>
                                </a-form-item>
                                <a-form-item label="Remarks:">
                                    <a-textarea
                                        :value="page.spexgc_remarks"
                                        placeholder="placeholder"
                                    ></a-textarea>
                                </a-form-item>
                            </a-col>
                            <a-col :span="12">
                                <a-form-item>
                                    <ant-form-nest
                                        :data="page.specialExternalGcrequestEmpAssign"
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>
                <a-row>
                    <a-col :span="24" style="text-align: right">
                        <a-button type="primary" html-type="submit"
                            >Submit</a-button
                        >
                        <a-button
                            style="margin: 0 8px"
                            @click="() => formRef.resetFields()"
                            >Clear</a-button
                        >
                    </a-col>
                </a-row>
            </a-form>
        </a-card>
    </AuthenticatedLayout>
</template>
