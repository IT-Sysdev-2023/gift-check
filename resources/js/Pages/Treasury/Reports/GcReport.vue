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
                    <a-button type="primary" html-type="submit"
                        >Generate</a-button
                    >
                </a-form-item>
            </a-form>
        </a-card>
        <a-modal
            :open="loadingProgress"
            :footer="null"
            centered
            width="700px"
            title="Generating Report"
            :closable="false"
        >
            <div class="flex justify-center flex-col items-center">
                <div class="py-8 flex flex-col items-center space-y-3">
                    <a-progress
                        type="circle"
                        :size="[150, 150]"
                        :stroke-color="{
                            '0%': '#108ee9',
                            '100%': '#87d068',
                        }"
                        :percent="parseFloat(items.percentage)"
                    />
                    <a-typography-title :level="3">{{
                        items.data.store
                    }}</a-typography-title>
                </div>
                <a-steps
                    :current="items.data.active"
                    :percent="items.percentage"
                    label-placement="vertical"
                    :items="[
                        {
                            title: 'Checking Records',
                        },
                        {
                            title: 'Generating Sales Data',
                        },
                        {
                            title: 'Generating Footer Data',
                        },
                        {
                            title: 'Generating Header',
                        },
                    ]"
                />
                <br />
            </div>
        </a-modal>
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

const page = usePage<PageWithSharedProps>().props;
defineProps<{
    title: string;
    store: {
        label: string;
        value: string;
    }[];
}>();

// interface FormState {
//     reportType: string[];
//     transactionDate: string;
//     store: string;
//     date: Dayjs;
// }

const loadingProgress = ref<boolean>(false);
const items = ref<{
    percentage: string;
    data: {
        active: number;

        store: string;
        isDone: boolean;
        info: {
            description: string;
        }[];
    };
}>({
    percentage: "",
    data: {
        active: 0,
        store: "",
        isDone: false,
        info: [
            {
                description: "Loading Please wait!",
            },
        ],
    },
});

let eventReceived; // Holds the resolve function of the promise
const waitForEvent = new Promise((resolve) => {
    eventReceived = resolve; // Set the resolve function for later
});

onMounted(() => {
    window.Echo.private(`treasury-report.${page.auth.user.user_id}`).listen(
        "TreasuryReportEvent",
        (e) => {
            items.value = e;
            if (
                e.percentage === 100 ||
                (formState.value.store !== "all" && e.data.active === 3)
            ) {
                eventReceived();
            }
        }
    );
});

const handleStore = (val) => {
    formState.value.store = val;
};

const formState = ref({
    reportType: [],
    transactionDate: "",
    store: null,
    date: null,
});
const onSubmit = async () => {
    axios
        .get(route("treasury.reports.generate.gc"), {
            params: {
                ...formState.value,
            },
            responseType: "blob",
        })
        .then(async (response: AxiosResponse) => {
            loadingProgress.value = true;
            await waitForEvent;

            const file = new Blob([response.data], { type: "application/pdf" });
            const fileURL = URL.createObjectURL(file);
            window.open(fileURL, "_blank"); // Open the PDF in a new tab
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
    // formState
    //     .submit({
    //         responseType: "blob",
    //     })
    // .then(async (response: AxiosResponse) => {
    //     loadingProgress.value = true;
    //     await waitForEvent;

    //     const file = new Blob([response.data], { type: "application/pdf" });
    //     const fileURL = URL.createObjectURL(file);
    //     window.open(fileURL, "_blank"); // Open the PDF in a new tab
    // })
    // .catch(() => {
    //     notification.error({
    //         message: "Error",
    //         description:
    //             "Something Went wrong, please check all the fields!",
    //     });
    // });
};

onBeforeUnmount(() => {
    leaveChannel();
});

const leaveChannel = () => {
    window.Echo.leaveChannel(`treasury-report.${page.auth.user.user_id}`);
};

const labelCol = { style: { width: "150px" } };
const wrapperCol = { span: 14 };
</script>
