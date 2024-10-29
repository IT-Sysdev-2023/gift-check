<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card>
            <a-form
                :model="formState"
                :label-col="labelCol"
                :wrapper-col="wrapperCol"
            >
                <a-form-item
                    label="Store"
                    has-feedback
                    :validate-status="formState.invalid('store') ? 'error' : ''"
                    :help="formState.errors.store"
                >
                    <ant-select :options="store" @handle-change="handleStore" />
                </a-form-item>
                <a-form-item
                    label="Report Type"
                    has-feedback
                    :validate-status="
                        formState.invalid('reportType') ? 'error' : ''
                    "
                    :help="formState.errors.reportType"
                >
                    <a-checkbox-group
                        v-model:value="formState.reportType"
                        @change="formState.validate('reportType')"
                    >
                        <a-checkbox value="gcsales" name="type"
                            >Gc Sales</a-checkbox
                        >
                        <a-checkbox value="reval" name="type"
                            >Gc Revalidation</a-checkbox
                        >
                        <a-checkbox value="refund" name="type"
                            >Gc Refund</a-checkbox
                        >
                    </a-checkbox-group>
                </a-form-item>
                <a-form-item
                    label="Transaction Date"
                    has-feedback
                    :validate-status="
                        formState.invalid('transactionDate') ? 'error' : ''
                    "
                    :help="formState.errors.transactionDate"
                >
                    <a-radio-group
                        v-model:value="formState.transactionDate"
                        @change="formState.validate('transactionDate')"
                    >
                        <a-radio value="today">Today</a-radio>
                        <a-radio value="yesterday">Yesterday</a-radio>
                        <a-radio value="thisWeek">This Week</a-radio>
                        <a-radio value="currentMonth">Current Month</a-radio>
                        <a-radio value="allTransactions"
                            >All Transacions</a-radio
                        >
                        <a-radio value="dateRange">Date Range</a-radio>
                    </a-radio-group>
                </a-form-item>
                <a-form-item
                    label="Date Range"
                    v-if="formState.transactionDate === 'dateRange'"
                    has-feedback
                    :validate-status="formState.invalid('date') ? 'error' : ''"
                    :help="formState.errors.date"
                >
                    <a-range-picker
                        v-model:value="formState.date"
                        @change="formState.validate('date')"
                    />
                </a-form-item>
                <a-form-item :wrapper-col="{ span: 14, offset: 4 }">
                    <a-button type="primary" @click="onSubmit">Create</a-button>
                    <a-button style="margin-left: 10px">Cancel</a-button>
                </a-form-item>
            </a-form>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { AxiosResponse } from "axios";
import { Dayjs } from "dayjs";
import { useForm } from "laravel-precognition-vue";
import { Form } from "laravel-precognition-vue/dist/types";

defineProps<{
    title: string;
    store: {
        label: string;
        value: string;
    }[];
}>();

interface FormState {
    reportType: string[];
    transactionDate: string;
    store: string;
    date: Dayjs;
}
const formState: Form<FormState> = useForm(
    "get",
    route("treasury.reports.generate.report"),
    {
        reportType: [],
        transactionDate: "",
        store: null,
        date: null,
    }
);
const handleStore = (val) => {
    formState.store = val;
    formState.validate("store");
};
const onSubmit = () => {
    formState
        .submit({
            responseType: "blob",
        })
        .then((response: AxiosResponse) => {
            const file = new Blob([response.data], { type: "application/pdf" });
            const fileURL = URL.createObjectURL(file);
            window.open(fileURL, "_blank"); // Open the PDF in a new tab
        })
        .catch((error) => {
            alert("An error occurred.");
        });
};
const labelCol = { style: { width: "150px" } };
const wrapperCol = { span: 14 };
</script>
