<template>
    <AuthenticatedLayout>
        <a-tabs style="font-weight: bold" type="card">
            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <PieChartOutlined />
                        SPGC Redeem (Monthly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px">
                        <a-form-item>
                            <div>Data Type:</div>

                            <a-select  style="width: 30%" placeholder="Select Type"
                                v-model:value="monthlyRedeem.SPGCDataType">
                                <a-select-option value="srv">Store Redemption/Verification</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item>
                            <div>Store:</div>
                            <a-select v-model:value="monthlyRedeem.selectedStore" style="width: 30%"
                                placeholder="Select Store" :options="stores" />
                        </a-form-item>

                        <a-form-item>
                            <div>Month and Year:</div>
                            <a-date-picker v-model:value="monthlyRedeem.year" picker="month"
                                :disabled-date="disabledDate" />

                        </a-form-item>
                    </div>
                    <div>
                        <a-button @click="monthlySubmitButton" style="background-color: #1e90ff; color: white">
                            <SendOutlined /> Submit
                        </a-button>
                    </div>
                </a-card>
            </a-tab-pane>

            <a-tab-pane key="2">
                <template #tab>
                    <span>
                        <PieChartOutlined />
                        SPGC Redeem (Yearly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px">
                        <a-form-item>
                            <div>Data Type:</div>

                            <a-select style="width: 30%" placeholder="Select Type" v-model:value="yearlyRedeem.SPGCDataType">

                                <a-select-option value="srv">Store Redemption/Verification</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item>
                            <div>Store:</div>
                            <a-select v-model:value="yearlyRedeem.selectedStore" style="width: 30%"
                                placeholder="Select Store" :options="stores" />
                        </a-form-item>

                        <a-form-item>
                            <div>Year:</div>
                            <a-date-picker v-model:value="yearlyRedeem.year" picker="year"
                                :disabled-date="disabledDate" />
                        </a-form-item>
                    </div>
                    <div>
                        <a-button @click="yearlyRedeemButton" style="background-color: #1e90ff; color: white">
                            <SendOutlined /> Submit
                        </a-button>
                    </div>
                </a-card>
            </a-tab-pane>
            <a-tab-pane key="3">
                <template #tab>
                    <span>
                        <PieChartOutlined />
                       Per Day Redeem
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px">
                        <a-form-item>
                            <div>Data Type:</div>

                            <a-select style="width: 30%" placeholder="Select" v-model:value="daily.SPGCDataType">
                                <a-select-option value="srv">Store Redemption/Verification</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item>
                            <div>Store:</div>
                            <a-select v-model:value="daily.selectedStore" style="width: 30%"
                                placeholder="Select Store" :options="stores" />
                        </a-form-item>

                        <a-form-item>
                            <div>Year:</div>
                            <a-date-picker size="large" v-model:value="daily.year"
                                 />
                        </a-form-item>
                    </div>
                    <div>
                        <a-button @click="dailyRedeemButton" style="background-color: #1e90ff; color: white">
                            <SendOutlined /> Submit
                        </a-button>
                    </div>
                </a-card>
            </a-tab-pane>
        </a-tabs>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">


import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import axios from "axios";
import { ref } from "vue";
import { notification } from "ant-design-vue";
import { useQueueState } from "@/stores/queue-state";

defineProps(["stores"]);
const monthlyRedeem = ref({
    year: null,
    selectedStore: null,
    SPGCDataType: null,
});

const yearlyRedeem = ref({
    SPGCDataType: null,
    selectedStore: null,
    year: null,
});
const daily = ref({
    SPGCDataType: null,
    selectedStore: null,
    year: null,
});

const disabledDate = (current) => {
    return current && current > dayjs().startOf("day");
};
const state = useQueueState();
const monthlySubmitButton = async () => {
    await axios
        .post(route("storeaccounting.redeemReportSubmit"), {
            month: dayjs(monthlyRedeem.value.year).month() + 1, // cause in Dayjs January returns 0 in month
            year: dayjs(monthlyRedeem.value.year).year(),
            selectedStore: monthlyRedeem.value.selectedStore,
            SPGCDataType: monthlyRedeem.value.SPGCDataType,
        })
        .then(() => {
            state.setGenerateButton(true);
            state.setFloatButton(true);
            state.setOpenFloat(true);
        })
        .catch(({ response }) => {
            // console.log(response.data.message);
            if (response.status === 422) {
                notification.error({
                    message: "Fields are Required!",
                    description: response.data.message,
                });
            } else {
                notification.error({
                    message: "Error!",
                    description: "No record Found on this date.",
                });
            }
        });
};

const yearlyRedeemButton = async () => {
    await axios
        .post(route("storeaccounting.redeemReportSubmit"), {
            year: dayjs(yearlyRedeem.value.year).year(),
            selectedStore: yearlyRedeem.value.selectedStore,
            SPGCDataType: yearlyRedeem.value.SPGCDataType,
        })
        .then(() => {
            state.setGenerateButton(true);
            state.setFloatButton(true);
            state.setOpenFloat(true);
        })
        .catch(({ response }) => {
            if (response.status === 422) {
                notification.error({
                    message: "Fields are Required!",
                    description: response.data.message,
                });
            } else {
                notification.error({
                    message: "Error!",
                    description: "No record Found on this date.",
                });
            }
        });
};
const dailyRedeemButton = async () => {
    await axios
        .post(route("storeaccounting.redeemReportSubmit"), {
            day: dayjs(daily.value.year).format('YYYY-MM-DD'),
            selectedStore: daily.value.selectedStore,
            SPGCDataType: daily.value.SPGCDataType,
        })
        .then(() => {
            state.setGenerateButton(true);
            state.setFloatButton(true);
            state.setOpenFloat(true);
        })
        .catch(({ response }) => {
            if (response.status === 422) {
                notification.error({
                    message: "Fields are Required!",
                    description: response.data.message,
                });
            } else {
                notification.error({
                    message: "Error!",
                    description: "No record Found on this date.",
                });
            }
        });
};
</script>
