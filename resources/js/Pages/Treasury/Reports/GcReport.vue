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
                @finish="onSubmit"
            >
                <a-form-item label="Store">
                    <ant-select :options="store" @handle-change="handleStore" />
                </a-form-item>
                <a-form-item label="Report Type">
                    <a-checkbox-group v-model:value="formState.reportType">
                        <a-checkbox value="gcSales" name="type"
                            >Gc Sales</a-checkbox
                        >
                        <a-checkbox value="gcRevalidation" name="type"
                            >Gc Revalidation</a-checkbox
                        >
                        <a-checkbox value="refund" name="type"
                            >Gc Refund</a-checkbox
                        >
                    </a-checkbox-group>
                </a-form-item>
                <a-form-item label="Transaction Date">
                    <a-radio-group v-model:value="formState.transactionDate">
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
                >
                    <a-range-picker v-model:value="formState.date" />
                </a-form-item>
                <a-form-item :wrapper-col="{ span: 14, offset: 4 }">
                    <a-button
                        type="primary"
                        html-type="submit"
                        :loading="state.isGenerateVisible"
                        >Generate</a-button
                    >
                </a-form-item>
            </a-form>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios, { AxiosResponse } from "axios";
import { Dayjs } from "dayjs";
import { useForm } from "laravel-precognition-vue";
import { PageWithSharedProps } from "@/types/index";
import { usePage } from "@inertiajs/vue3";
import { onBeforeUnmount, onMounted, ref } from "vue";
import { notification } from "ant-design-vue";
import { useQueueState } from "@/stores/queue-state";

const page = usePage<PageWithSharedProps>().props;
defineProps<{
    title: string;
    store: {
        label: string;
        value: string;
    }[];
}>();

// let eventReceived; // Holds the resolve function of the promise
// const waitForEvent = new Promise((resolve) => {
//     eventReceived = resolve; // Set the resolve function for later
// });

const handleStore = (val) => {
    formState.value.store = val;
};

const formState = ref({
    reportType: [],
    transactionDate: "",
    store: null,
    date: null,
});

const state = useQueueState();

const onSubmit = async () => {
    await axios
        .get(route("treasury.reports.generate.gc"), {
            params: {
                ...formState.value,
            },
        })
        .then((e) => {
            state.setGenerateButton(true);
            state.setFloatButton(true);
            state.setOpenFloat(true);
        })
        .catch((e) => {
            let message = "please check all the fields";
            if (e.status === 404) {
                message = "there was no transaction on this selected date!";
            }
            notification.error({
                message: "Error",
                description: `Something Went wrong,  ${message}`,
            });
        });
};

// onBeforeUnmount(() => {
//     leaveChannel();
// });

const leaveChannel = () => {
    window.Echo.leaveChannel(`treasury-report.${page.auth.user.user_id}`);
};

const labelCol = { style: { width: "150px" } };
const wrapperCol = { span: 14 };
</script>
