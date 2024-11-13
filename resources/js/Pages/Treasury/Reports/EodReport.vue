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
                <a-form-item label="Transaction Date" has-feedback>
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
                    <a-button type="primary" html-type="submit" :loading="loadingButton"
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
                    <a-typography-title :level="3">
                        Please wait....
                    </a-typography-title>
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
                            title: 'Generating Header',
                        },
                        {
                            title: 'Generating Records',
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
import { PageWithSharedProps } from "@/types/index";
import { usePage } from "@inertiajs/vue3";
import { onBeforeUnmount, onMounted, ref } from "vue";
import { notification } from "ant-design-vue";

const page = usePage<PageWithSharedProps>().props;
defineProps<{
    title: string;
}>();

// interface FormState {
//     reportType: string[];
//     transactionDate: string;
//     store: string;
//     date: Dayjs;
// }
const loadingButton = ref<boolean>(false);
const loadingProgress = ref<boolean>(false);
const items = ref<{
    percentage: string;
    data: {
        active: number;
        isDone: boolean;
        info: {
            description: string;
        }[];
    };
}>({
    percentage: "",
    data: {
        active: 0,
        isDone: false,
        info: [
            {
                description: "Loading Please wait!",
            },
        ],
    },
});

let eventReceived;
const waitForEvent = new Promise((resolve) => {
    eventReceived = resolve;
});

onMounted(() => {
    window.Echo.private(`treasury-report.${page.auth.user.user_id}`).listen(
        "TreasuryReportEvent",
        (e) => {
            items.value = e;
            if (e.percentage === 100 && e.data.active === 2) {
                loadingButton.value = false;
                eventReceived();
            }
        }
    );
});

const formState = ref({
    transactionDate: "",
    date: null,
});

const onSubmit = async () => {
    loadingButton.value = true;
    axios
        .get(route("treasury.reports.generate.eod"), {
            params: { ...formState.value },
            responseType: 'blob'
        })
        .then(async (response: AxiosResponse) => {
            loadingProgress.value = true;

            await waitForEvent;

            const file = new Blob([response.data], { type: "application/pdf" });
            const fileURL = URL.createObjectURL(file);
            window.open(fileURL, "_blank"); // Open the PDF in a new tab
        })
        .catch((e) => {
            let message = 'please check all the fields';
            if(e.status === 404){
                message = 'there was no transaction on this selected date!';
            }
            notification.error({
                message: "Error",
                description:
                    `Something Went wrong,  ${message}`,
            });
        });
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
