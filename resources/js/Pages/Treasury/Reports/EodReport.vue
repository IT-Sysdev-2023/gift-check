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

const formState = ref({
    transactionDate: "",
    date: null,
});
const startTimeout = () =>  {
    loadingButton.value = true;
      setTimeout(() => {
        loadingButton.value = false;
      }, 3000); // 2 seconds delay
}

const onSubmit = async () => {
    startTimeout();
    localStorage.setItem('isFloatOpen', 'true');
    axios
        .get(route("treasury.reports.generate.eod"), {
            params: { ...formState.value },
        })
        .then( (e) => {
            console.log('sdsd');
            // loadingProgress.value = true;

        })
        .catch((e) => {
            console.log(e);
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
