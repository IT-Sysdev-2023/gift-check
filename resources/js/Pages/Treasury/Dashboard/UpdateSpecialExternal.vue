<script setup lang="ts">
import AuthenticatedLayout from "../../../Layouts/AuthenticatedLayout.vue";
import { dashboardRoute } from "../../../Mixin/UiUtilities";
import { PageProps } from "../../../types/index";
import { usePage } from "@inertiajs/vue3";
import type { SelectProps } from 'ant-design-vue';
import { reactive, ref } from "vue";
import type { FormInstance } from "ant-design-vue";
import dayjs, { Dayjs } from "dayjs";

const dRoute = dashboardRoute();
const props = defineProps<{
    title: String;
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
            spexgc_company: string;
            spexgc_payment: string;
            spexgc_payment_arnum: string;
            spexgc_paymentype: string;
            spexgc_dateneed: string;
            spexgc_datereq: string;
            spexgc_num: string;
        };
    };
}>();
const page = props.data.data;
const dateRequested = <Dayjs>dayjs(page.spexgc_datereq);
const dateValidity = ref<Dayjs>(dayjs(page.spexgc_dateneed));

const paymentOptions = ref<SelectProps['options']>([
  {
    value: '1',
    label: 'Cash',
  },
  {
    value: '2',
    label: 'Check',
  }
]);
const paymentTypeSelected = ref(page.spexgc_paymentype);
const formRef = ref<FormInstance>();
const formState = reactive({});
const onFinish = (values: any) => {
    console.log("Received values of form: ", values);
    console.log("formState: ", formState);
};
</script>
<template>
    <AuthenticatedLayout>
        <Head title="Update Special External Gc" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route(dRoute)">Home</Link>
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
                        <a-form-item label="Date Validity:">
                            <a-date-picker v-model:value="dateValidity" />
                        </a-form-item>
                        <!-- <a-form-item label="Uploaded Document">
                            <a-space wrap class="ml-2">
                                <a-button
                                    type="primary"
                                    v-if="props.data.br_file_docno"
                                    >
                                    @click="download(props.data.br_file_docno)"
                                    <template #icon>
                                        <DownloadOutlined />
                                    </template>
                                    Download
                                </a-button>
                                <a-tag color="error" v-else>
                                    <template #icon>
                                        <close-circle-outlined />
                                    </template>
                                    NONE
                                </a-tag>
                            </a-space>
                        </a-form-item> -->
                        <!-- <a-form-item label="Upload Document #:">
                            <a-upload
                                v-model:file-list="fileList"
                                name="avatar"
                                list-type="picture-card"
                                class="avatar-uploader"
                                :show-upload-list="false"
                                action="https://www.mocky.io/v2/5cc8019d300000980a055e76"
                                :before-upload="beforeUpload"
                                @change="handleChange"
                            >
                                <img
                                    v-if="imageUrl"
                                    :src="imageUrl"
                                    alt="avatar"
                                />
                                <div v-else>
                                    <loading-outlined
                                        v-if="loading"
                                    ></loading-outlined>
                                    <plus-outlined v-else></plus-outlined>
                                    <div class="ant-upload-text">Upload</div>
                                </div>
                            </a-upload>
                        </a-form-item> -->
                    </a-col>
                    <a-col :span="8">
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
                        <a-form-item label="Search Customer:">
                            <a-input
                                value="formState[`field-${i}`]"
                                placeholder="placeholder"
                            ></a-input>
                        </a-form-item>
                        <a-form-item label="AR Number:">
                            <a-input
                                :value="page.spexgc_payment_arnum"
                                readonly
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
                            type='number'
                                :value="page.spexgc_payment"
                                placeholder="placeholder"
                            ></a-input>
                        </a-form-item>
                        <a-form-item label="Amount in Words:">
                            <a-input
                                value="formState[`field-${i}`]"
                                placeholder="placeholder"
                            ></a-input>
                        </a-form-item>
                    </a-col>
                    <a-col :span="8">
                        <a-form-item label="Remarks:">
                            <a-input
                                value="formState[`field-${i}`]"
                                placeholder="placeholder"
                            ></a-input>
                        </a-form-item>
                        <a-form-item label="Denomination:">
                            <a-input
                                value="formState[`field-${i}`]"
                                placeholder="placeholder"
                            ></a-input>
                        </a-form-item>
                        <a-form-item label="Quantity:">
                            <a-input
                                value="formState[`field-${i}`]"
                                placeholder="placeholder"
                            ></a-input>
                        </a-form-item>
                        <a-form-item label="Total:">
                            <a-input
                                value="formState[`field-${i}`]"
                                placeholder="placeholder"
                            ></a-input>
                        </a-form-item>
                        <a-form-item label="Updated By:">
                            <a-input
                                value="formState[`field-${i}`]"
                                placeholder="placeholder"
                            ></a-input>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row>
                    <a-col :span="24" style="text-align: right">
                        <a-button type="primary" html-type="submit"
                            >Search</a-button
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
