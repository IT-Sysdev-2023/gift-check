<template>
    <AuthenticatedLayout>
        <a-tabs style="font-weight: bold" type="card">
            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <MoneyCollectOutlined />
                        Billing Between Stores and BU (Monthly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px">
                        <a-form-item>
                            <div>Data Type:</div>

                            <a-select style="width: 30%" placeholder="Select" v-model:value="billMonthly.StoreDataType">
                                <a-select-option value="">---Select---</a-select-option>
                                <a-select-option value="store-sales">Store Sales</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item>
                            <div>Store:</div>
                            <a-select v-model:value="billMonthly.selectedStore" style="width: 30%"
                                placeholder="Select Store" :options="stores" />
                        </a-form-item>

                        <a-form-item>
                            <div>Month & Year:</div>
                            <a-date-picker v-model:value="billMonthly.month" picker="month" />
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
                        <MoneyCollectOutlined />
                        Billing Between Stores and BU (Yearly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px">
                        <a-form-item>
                            <div>Data Type:</div>

                            <a-select style="width: 30%" placeholder="Select" v-model:value="billYearly.StoreDataType">
                                <a-select-option value="">---Select---</a-select-option>
                                <a-select-option value="store-sales">Store Sales</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item>
                            <div>Store:</div>
                            <a-select v-model:value="billYearly.selectedStore" style="width: 30%"
                                placeholder="Select Store" :options="stores" />
                        </a-form-item>

                        <a-form-item>
                            <div>Year:</div>
                            <a-date-picker v-model:value="billYearly.year" picker="year"
                                :disabled-date="disabledDate" />
                        </a-form-item>
                    </div>
                    <div>
                        <a-button @click="yearlySubmitButton" style="background-color: #1e90ff; color: white">
                            <SendOutlined /> Submit
                        </a-button>
                    </div>
                </a-card>
            </a-tab-pane>
            <a-tab-pane key="3">
                <template #tab>
                    <span>
                        <MoneyCollectOutlined />
                        Billing Between Stores and BU (Per Day)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px">
                        <a-form-item>
                            <div>Data Type:</div>
                            <a-select style="width: 30%" placeholder="Select" v-model:value="form.dataType">
                                <a-select-option value="">---Select---</a-select-option>
                                <a-select-option value="store-sales">Store Sales</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item>
                            <div>Store:</div>
                            <a-select v-model:value="form.storeSelected" style="width: 30%" placeholder="Select Store"
                                :options="stores" />
                        </a-form-item>

                        <a-form-item>
                            <div>Selected Date:</div>
                            <a-date-picker v-model:value="form.dateSelected" />
                        </a-form-item>
                    </div>
                    <div>
                        <a-button @click="perDaySubmitButton" style="background-color: #1e90ff; color: white">
                            <SendOutlined /> Submit
                        </a-button>
                    </div>
                </a-card>
            </a-tab-pane>
        </a-tabs>
        <!-- {{ data }} -->
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useQueueState } from "@/stores/queue-state";
import dayjs from "dayjs";
import { notification } from "ant-design-vue";
import axios from "axios";
import { reactive, ref } from "vue";

defineProps(["stores"]);
const state = useQueueState();

const billMonthly = ref({
    year: "",
    month: "",
    selectedStore: "",
    StoreDataType: "",
});

const billYearly = ref({
    year: "",
    StoreDataType: "",
    selectedStore: "",
});

const disabledDate = (current) => {
    return current && current > dayjs().startOf("day");
};

const monthlySubmitButton = async () => {
    await axios
        .post(route("storeaccounting.generateStorePurchasedReport"), {
            month: dayjs(billMonthly.value.month).month() + 1, // cause in Dayjs January returns indexed 0
            year: dayjs(billMonthly.value.month).year(),
            selectedStore: billMonthly.value.selectedStore,
            StoreDataType: billMonthly.value.StoreDataType,
            isMonthly: true,
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

const yearlySubmitButton = async () => {
    await axios
        .post(route("storeaccounting.generateStorePurchasedReport"), {
            year: dayjs(billYearly.value.year).year(),
            StoreDataType: billYearly.value.StoreDataType,
            selectedStore: billYearly.value.selectedStore,
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

// Per Day functions
const form = reactive({
    dateSelected: "",
    storeSelected: "",
    dataType: "",
});

const data = ref<(string | number)[]>([]);
const perDaySubmitButton = async () => {
    try {
        const formattedDate = dayjs(form.dateSelected).format("YYYY-MM-DD");
        const response = await axios.post(route('storeaccounting.billingReportPerDay'), {
            dataType: form.dataType,
            storeSelected: form.storeSelected,
            dateSelected: formattedDate
        });
        if (response.data.success) {
            data.value = response.data.data;
            if (data.value.length > 0) {
                generateExcel();
            }
        }
        else if (response.data.error) {
            notification.error({
                message: "Error",
                description: response.data.message,
            });
        }
        console.log(response);
    } catch (error) {
        console.log(error);
        if (error.response) {
            notification.error({
                message: "Error",
                description: error.response.data.message,
            });
        }
    }
}

const generateExcel = async () => {
    if (data.value.length > 0) {
        try {
            const formatted = dayjs(form.dateSelected).format('MMMM D, YYYY');
            const response = await axios.post(route('storeaccounting.reports.generateBillingPerDayReport'), {
                date: formatted,
                data: data.value
            }, {
                responseType: 'blob'
            });

            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'Billing Report Per Day.xlsx');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            notification.success({
                message: 'Success',
                description: 'Report generated successfully'
            });
        } catch (error) {
            console.log(error);
        }
    }
}
</script>
