<template>
    <AuthenticatedLayout>
        <div>
            <a-tabs style="font-weight: bold">
                <a-tab-pane key="1">
                    <template #tab>
                        <span>
                            <VerifiedOutlined />
                            Verified GC Reports (Monthly)
                        </span>
                    </template>
                    <a-card style="width: 30%">
                        <div style="margin-top: 20px">
                            <a-form-item
                                :validate-status="
                                    GCDataTypeMonthly.errors.dataTypeMonthly
                                        ? 'error'
                                        : ''
                                "
                                :help="GCDataTypeMonthly.errors.dataTypeMonthly"
                            >
                                <div>Data Type:</div>

                                <a-select
                                    placeholder="Select"
                                    v-model:value="
                                        GCDataTypeMonthly.dataTypeMonthly
                                    "
                                >
                                    <a-select-option value=""
                                        >---Select---</a-select-option
                                    >
                                    <a-select-option value="verifiedGC"
                                        >Verified GC</a-select-option
                                    >
                                </a-select>
                            </a-form-item>

                            <a-form-item
                                :validate-status="
                                    GCDataTypeMonthly.errors
                                        .selectedStore
                                        ? 'error'
                                        : ''
                                "
                                :help="
                                    GCDataTypeMonthly.errors
                                        .selectedStore
                                "
                            >
                                <div>Store:</div>
                                <a-select
                                    v-model:value="
                                        GCDataTypeMonthly.selectedStore
                                    "
                                    placeholder="Select Store"
                                    :options="stores"
                                />
                            </a-form-item>

                            <a-form-item
                                :validate-status="
                                    GCDataTypeMonthly.errors.month
                                        ? 'error'
                                        : ''
                                "
                                :help="GCDataTypeMonthly.errors.month"
                            >
                                <div>Month:</div>
                                <a-select
                                    placeholder="Select Month"
                                    v-model:value="GCDataTypeMonthly.month"
                                >
                                    <a-select-option value="1"
                                        >January</a-select-option
                                    >
                                    <a-select-option value="2"
                                        >February</a-select-option
                                    >
                                    <a-select-option value="3"
                                        >March</a-select-option
                                    >
                                    <a-select-option value="4"
                                        >April</a-select-option
                                    >
                                    <a-select-option value="5"
                                        >May</a-select-option
                                    >
                                    <a-select-option value="6"
                                        >June</a-select-option
                                    >
                                    <a-select-option value="7"
                                        >July</a-select-option
                                    >
                                    <a-select-option value="8"
                                        >August</a-select-option
                                    >
                                    <a-select-option value="9"
                                        >September</a-select-option
                                    >
                                    <a-select-option value="10"
                                        >October</a-select-option
                                    >
                                    <a-select-option value="11"
                                        >November</a-select-option
                                    >
                                    <a-select-option value="12"
                                        >December</a-select-option
                                    >
                                </a-select>
                            </a-form-item>

                            <a-form-item
                                :validate-status="
                                    GCDataTypeMonthly.errors.year
                                        ? 'error'
                                        : ''
                                "
                                :help="GCDataTypeMonthly.errors.year"
                            >
                                <div>Year:</div>

                                <a-date-picker v-model:value="GCDataTypeMonthly.year" picker="year"  :disabled-date="disabledDate"/>
                               
                            </a-form-item>
                        </div>
                        <a-button
                            @click="submitGCReportsMonthly"
                            style="background-color: #1e90ff; color: white"
                        >
                            <FilePdfOutlined /> Generate
                        </a-button>
                    </a-card>
                </a-tab-pane>

                <a-tab-pane key="2">
                    <template #tab>
                        <span>
                            <VerifiedOutlined />
                            Verified GC Reports (Yearly)
                        </span>
                    </template>
                    <a-card>
                        <div style="margin-top: 20px">
                            <a-form-item
                                :validate-status="
                                    GCDataTypeYearly.errors.GCDataType
                                        ? 'error'
                                        : ''
                                "
                                :help="GCDataTypeYearly.errors.GCDataType"
                            >
                                <div>Data Type:</div>

                                <a-select
                                    style="width: 30%"
                                    placeholder="Select"
                                    v-model:value="GCDataTypeYearly.GCDataType"
                                >
                                    <a-select-option value=""
                                        >---Select---</a-select-option
                                    >
                                    <a-select-option value="verifiedgc"
                                        >Verified GC</a-select-option
                                    >
                                </a-select>
                            </a-form-item>

                            <a-form-item
                                :validate-status="
                                    GCDataTypeYearly.errors.selectedStore
                                        ? 'error'
                                        : ''
                                "
                                :help="GCDataTypeYearly.errors.selectedStore"
                            >
                                <div>Store:</div>
                                <a-select
                                    v-model:value="
                                        GCDataTypeYearly.selectedStore
                                    "
                                    style="width: 30%"
                                    placeholder="Select Store"
                                    :options="stores"
                                />
                            </a-form-item>

                            <a-form-item
                                :validate-status="
                                    GCDataTypeYearly.errors.year ? 'error' : ''
                                "
                                :help="GCDataTypeYearly.errors.year"
                            >
                                <div>Year:</div>
                                <a-date-picker v-model:value="GCDataTypeYearly.year" picker="year"  :disabled-date="disabledDate"/>
                            </a-form-item>
                        </div>
                        <a-button
                            @click="submitGCReportsYearly"
                            style="background-color: #1e90ff; color: white"
                        >
                            <SendOutlined /> Submit
                        </a-button>
                    </a-card>
                </a-tab-pane>
            </a-tabs>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
// import { defineComponent } from '@vue/composition-api'
import { ref } from "vue";
import { Modal } from "ant-design-vue";
import axios from "axios";
import { notification } from "ant-design-vue";
import { useQueueState } from "@/stores/queue-state";
import dayjs from "dayjs";

defineProps(["stores"]);
const GCDataTypeMonthly = ref({
    dataTypeMonthly: "",
    year: "",
    month: "",
    selectedStore: "",
    errors: {},
});

const GCDataTypeYearly = ref({
    GCDataType: "",
    selectedStore: "",
    year: "",
    errors: {},
});

const submitGCReportsMonthly = () => {
    GCDataTypeMonthly.value.errors = {};
    const { dataTypeMonthly, year, month, selectedStore } =
        GCDataTypeMonthly.value;
    if (!dataTypeMonthly)
        GCDataTypeMonthly.value.errors.dataTypeMonthly =
            " Data type field is required";
    if (!selectedStore)
        GCDataTypeMonthly.value.errors.selectedStore =
            "Store field is required";
    if (!year)
        GCDataTypeMonthly.value.errors.year = "Year field is required";
    if (!month)
        GCDataTypeMonthly.value.errors.month = "Month field is required";

    if (
        GCDataTypeMonthly.value.errors.dataTypeMonthly ||
        GCDataTypeMonthly.value.errors.year ||
        GCDataTypeMonthly.value.errors.month ||
        GCDataTypeMonthly.value.errors.selectedStore
    ) {
        return;
    }

    const yearFormat = dayjs(year).year();
    const monthlyData = {
        dataTypeMonthly: dataTypeMonthly,
        year: yearFormat,
        month: month,
        selectedStore: selectedStore,
    };
    // console.log(monthlyData);
    Modal.confirm({
        title: "Confirmation",
        content: "Are you sure you want to generate?",
        okText: "Yes",
        okType: "danger",
        cancelText: "No",
        onOk: () => {
            axios
                .get(route("storeaccounting.verifiedGcYearlySubmit"), {
                    params: { ...monthlyData },
                })
                .then((e) => {
                    state.setGenerateButton(true);
                    state.setFloatButton(true);

                    state.setOpenFloat(true);
                })
                .catch((e) => {
                    let message = "please check all the fields";
                    if (e.status === 404) {
                        message = e.response.data;
                    }
                    notification.error({
                        message: "Opps Something Went wrong",
                        description: `${message}`,
                    });
                });
        },
    });
};

const disabledDate = (current) => {
    return current && current > dayjs().startOf("day");
};

const state = useQueueState();

const submitGCReportsYearly = () => {
    GCDataTypeYearly.value.errors = {};
    const { GCDataType, selectedStore, year } = GCDataTypeYearly.value;

    if (!GCDataType)
        GCDataTypeYearly.value.errors.GCDataType =
            "Data Type field is required";
    if (!selectedStore)
        GCDataTypeYearly.value.errors.selectedStore = "Store field is required";
    if (!year) GCDataTypeYearly.value.errors.year = "Year field is required";

    if (
        GCDataTypeYearly.value.errors.GCDataType ||
        GCDataTypeYearly.value.errors.selectedStore ||
        GCDataTypeYearly.value.errors.year
    ) {
        return;
    }

    const yearFormat = dayjs(year).year();

    const yearlyData = {
        GCDataType: GCDataType,
        selectedStore: selectedStore,
        year: yearFormat,
    };

    axios
        .get(route("storeaccounting.verifiedGcYearlySubmit"), {
            params: { ...yearlyData },
        })
        .then((e) => {
            state.setGenerateButton(true);
            state.setFloatButton(true);

            state.setOpenFloat(true);
        })
        .catch((e) => {
            let message = "please check all the fields";
            if (e.status === 404) {
                message = e.response.data;
            }
            notification.error({
                message: "Opps Something Went wrong",
                description: `${message}`,
            });
        });
};
</script>
