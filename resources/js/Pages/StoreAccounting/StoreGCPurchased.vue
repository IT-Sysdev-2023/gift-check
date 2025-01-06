<template>
    <AuthenticatedLayout>
        <a-tabs style="font-weight: bold">
            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <MoneyCollectOutlined />
                        Billing Between Stores and BU (Monthly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px">
                        <a-form-item
                            :validate-status="
                                billMonthly.errors.StoreDataType ? 'error' : ''
                            "
                            :help="billMonthly.errors.StoreDataType"
                        >
                            <div>Data Type:</div>

                            <a-select
                                style="width: 30%"
                                placeholder="Select"
                                v-model:value="billMonthly.StoreDataType"
                            >
                                <a-select-option value=""
                                    >---Select---</a-select-option
                                >
                                <a-select-option value="store-sales"
                                    >Store Sales</a-select-option
                                >
                            </a-select>
                        </a-form-item>

                        <a-form-item
                            :validate-status="
                                billMonthly.errors.selectedStore ? 'error' : ''
                            "
                            :help="billMonthly.errors.selectedStore"
                        >
                            <div>Store:</div>
                            <a-select
                                v-model:value="billMonthly.selectedStore"
                                style="width: 30%"
                                placeholder="Select Store"
                                :options="stores"
                            />
                        </a-form-item>

                        <a-form-item
                            :validate-status="
                                billMonthly.errors.month ? 'error' : ''
                            "
                            :help="billMonthly.errors.month"
                        >
                            <div>Month:</div>
                            <a-select
                                style="width: 30%"
                                placeholder="Select Month"
                                v-model:value="billMonthly.month"
                            >
                                <a-select-option value="January"
                                    >January</a-select-option
                                >
                                <a-select-option value="February"
                                    >February</a-select-option
                                >
                                <a-select-option value="March"
                                    >March</a-select-option
                                >
                                <a-select-option value="April"
                                    >April</a-select-option
                                >
                                <a-select-option value="May"
                                    >May</a-select-option
                                >
                                <a-select-option value="June"
                                    >June</a-select-option
                                >
                                <a-select-option value="July"
                                    >July</a-select-option
                                >
                                <a-select-option value="August"
                                    >August</a-select-option
                                >
                                <a-select-option value="September"
                                    >September</a-select-option
                                >
                                <a-select-option value="October"
                                    >October</a-select-option
                                >
                                <a-select-option value="November"
                                    >November</a-select-option
                                >
                                <a-select-option value="December"
                                    >December</a-select-option
                                >
                            </a-select>
                        </a-form-item>

                        <a-form-item
                            :validate-status="
                                billMonthly.errors.year ? 'error' : ''
                            "
                            :help="billMonthly.errors.year"
                        >
                            <div>Year:</div>
                            <a-date-picker
                                v-model:value="billMonthly.year"
                                picker="year"
                                :disabled-date="disabledDate"
                            />
                        </a-form-item>
                    </div>
                    <div>
                        <a-button
                            @click="monthlySubmitButton"
                            style="background-color: #1e90ff; color: white"
                        >
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
                        <a-form-item
                            :validate-status="
                                billYearly.errors.StoreDataType ? 'error' : ''
                            "
                            :help="billYearly.errors.StoreDataType"
                        >
                            <div>Data Type:</div>

                            <a-select
                                style="width: 30%"
                                placeholder="Select"
                                v-model:value="billYearly.StoreDataType"
                            >
                                <a-select-option value=""
                                    >---Select---</a-select-option
                                >
                                <a-select-option value="store-sales"
                                    >Store Sales</a-select-option
                                >
                            </a-select>
                        </a-form-item>

                        <a-form-item
                            :validate-status="
                                billYearly.errors.selectedStore ? 'error' : ''
                            "
                            :help="billYearly.errors.selectedStore"
                        >
                            <div>Store:</div>
                            <a-select
                                v-model:value="billYearly.selectedStore"
                                style="width: 30%"
                                placeholder="Select Store"
                                :options="stores"
                            />
                        </a-form-item>

                        <a-form-item
                            :validate-status="
                                billYearly.errors.year ? 'error' : ''
                            "
                            :help="billYearly.errors.year"
                        >
                            <div>Year:</div>
                            <a-date-picker
                                v-model:value="billYearly.year"
                                picker="year"
                                :disabled-date="disabledDate"
                            />
                        </a-form-item>
                    </div>
                    <div>
                        <a-button
                            @click="yearlySubmitButton"
                            style="background-color: #1e90ff; color: white"
                        >
                            <SendOutlined /> Submit
                        </a-button>
                    </div>
                </a-card>
            </a-tab-pane>
        </a-tabs>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useQueueState } from "@/stores/queue-state";
import dayjs from "dayjs";
import { useForm } from "laravel-precognition-vue";

defineProps(["stores"]);
const state = useQueueState();

const billMonthly = useForm(
    "post",
    route("storeaccounting.billingMonthlySubmit"),
    {
        year: "",
        month: "",
        selectedStore: "",
        StoreDataType: "",
        isMonthly: true,
    },
);

const billYearly = useForm(
    "post",
    route("storeaccounting.billingMonthlySubmit"),
    {
        year: "",
        StoreDataType: "",
        selectedStore: "",
    },
);

const monthlySubmitButton = () => {
    billMonthly
        .setData({
            year: dayjs(billMonthly.year).year(),
        })
        .submit({
            onSuccess: () => billMonthly.reset(),
        })
        .then((res) => {
            state.setGenerateButton(true);
            state.setFloatButton(true);
            state.setOpenFloat(true);
        });
};
const disabledDate = (current) => {
    return current && current > dayjs().startOf("day");
};

const yearlySubmitButton = () => {
    billYearly
        .setData({
            year: dayjs(billYearly.year).year(),
        })
        .submit({
            onSuccess: () => billYearly.reset(),
        })
        .then((res) => {
            state.setGenerateButton(true);
            state.setFloatButton(true);
            state.setOpenFloat(true);
        });
};
</script>
