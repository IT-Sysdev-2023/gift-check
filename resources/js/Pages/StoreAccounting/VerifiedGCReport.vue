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
                                        .selectedStoreMonthly
                                        ? 'error'
                                        : ''
                                "
                                :help="
                                    GCDataTypeMonthly.errors
                                        .selectedStoreMonthly
                                "
                            >
                                <div>Store:</div>
                                <a-select
                                    v-model:value="
                                        GCDataTypeMonthly.selectedStoreMonthly
                                    "
                                    placeholder="Select Store"
                                >
                                    <a-select-option
                                        v-if="
                                            GCDataTypeMonthly.dataTypeMonthly ===
                                            'verifiedGC'
                                        "
                                        value="1"
                                        >Alturas Mall</a-select-option
                                    >
                                    <a-select-option
                                        v-if="
                                            GCDataTypeMonthly.dataTypeMonthly ===
                                            'verifiedGC'
                                        "
                                        value="2"
                                        >Alturas Talibon</a-select-option
                                    >
                                    <a-select-option
                                        v-if="
                                            GCDataTypeMonthly.dataTypeMonthly ===
                                            'verifiedGC'
                                        "
                                        value="3"
                                        >Island City Mall</a-select-option
                                    >
                                    <a-select-option
                                        v-if="
                                            GCDataTypeMonthly.dataTypeMonthly ===
                                            'verifiedGC'
                                        "
                                        value="4"
                                        >Plaza Marcela</a-select-option
                                    >
                                    <a-select-option
                                        v-if="
                                            GCDataTypeMonthly.dataTypeMonthly ===
                                            'verifiedGC'
                                        "
                                        value="5"
                                        >Alturas Tubigon</a-select-option
                                    >
                                    <a-select-option
                                        v-if="
                                            GCDataTypeMonthly.dataTypeMonthly ===
                                            'verifiedGC'
                                        "
                                        value="6"
                                        >Colonade Colon</a-select-option
                                    >
                                    <a-select-option
                                        v-if="
                                            GCDataTypeMonthly.dataTypeMonthly ===
                                            'verifiedGC'
                                        "
                                        value="7"
                                        >Colonade Mandaue</a-select-option
                                    >
                                    <a-select-option
                                        v-if="
                                            GCDataTypeMonthly.dataTypeMonthly ===
                                            'verifiedGC'
                                        "
                                        value="8"
                                        >Alta Citta</a-select-option
                                    >
                                    <a-select-option
                                        v-if="
                                            GCDataTypeMonthly.dataTypeMonthly ===
                                            'verifiedGC'
                                        "
                                        value="9"
                                        >Farmers Market</a-select-option
                                    >
                                    <a-select-option
                                        v-if="
                                            GCDataTypeMonthly.dataTypeMonthly ===
                                            'verifiedGC'
                                        "
                                        value="10"
                                        >Ubay Distribution
                                        Center</a-select-option
                                    >
                                    <a-select-option
                                        v-if="
                                            GCDataTypeMonthly.dataTypeMonthly ===
                                            'verifiedGC'
                                        "
                                        value="11"
                                        >Screenville</a-select-option
                                    >
                                    <a-select-option
                                        v-if="
                                            GCDataTypeMonthly.dataTypeMonthly ===
                                            'verifiedGC'
                                        "
                                        value="12"
                                        >Asc Tech</a-select-option
                                    >
                                </a-select>
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
                                    GCDataTypeMonthly.errors.yearMonthly
                                        ? 'error'
                                        : ''
                                "
                                :help="GCDataTypeMonthly.errors.yearMonthly"
                            >
                                <div>Year:</div>

                                <a-select
                                    placeholder="Select Year"
                                    v-model:value="
                                        GCDataTypeMonthly.yearMonthly
                                    "
                                >
                                    <a-select-option value="2017"
                                        >2017</a-select-option
                                    >
                                    <a-select-option value="2018"
                                        >2018</a-select-option
                                    >
                                    <a-select-option value="2019"
                                        >2019</a-select-option
                                    >
                                    <a-select-option value="2020"
                                        >2020</a-select-option
                                    >
                                    <a-select-option value="2021"
                                        >2021</a-select-option
                                    >
                                    <a-select-option value="2022"
                                        >2022</a-select-option
                                    >
                                    <a-select-option value="2023"
                                        >2023</a-select-option
                                    >
                                    <a-select-option value="2024"
                                        >2024</a-select-option
                                    >
                                </a-select>
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
                                <div>Store121:</div>
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
                                <a-select
                                    style="width: 30%"
                                    placeholder="Select Year"
                                    v-model:value="GCDataTypeYearly.year"
                                >
                                    <a-select-option value="2017"
                                        >2017</a-select-option
                                    >
                                    <a-select-option value="2018"
                                        >2018</a-select-option
                                    >
                                    <a-select-option value="2019"
                                        >2019</a-select-option
                                    >
                                    <a-select-option value="2020"
                                        >2020</a-select-option
                                    >
                                    <a-select-option value="2021"
                                        >2021</a-select-option
                                    >
                                    <a-select-option value="2022"
                                        >2022</a-select-option
                                    >
                                    <a-select-option value="2023"
                                        >2023</a-select-option
                                    >
                                    <a-select-option value="2024"
                                        >2024</a-select-option
                                    >
                                </a-select>
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
import { router } from "@inertiajs/core";

defineProps(["stores"]);
const GCDataTypeMonthly = ref({
    dataTypeMonthly: "",
    yearMonthly: "",
    month: "",
    selectedStoreMonthly: "",
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
    const { dataTypeMonthly, yearMonthly, month, selectedStoreMonthly } =
        GCDataTypeMonthly.value;
    if (!dataTypeMonthly)
        GCDataTypeMonthly.value.errors.dataTypeMonthly =
            " Data type field is required";
    if (!selectedStoreMonthly)
        GCDataTypeMonthly.value.errors.selectedStoreMonthly =
            "Store field is required";
    if (!yearMonthly)
        GCDataTypeMonthly.value.errors.yearMonthly = "Year field is required";
    if (!month)
        GCDataTypeMonthly.value.errors.month = "Month field is required";

    if (
        GCDataTypeMonthly.value.errors.dataTypeMonthly ||
        GCDataTypeMonthly.value.errors.yearMonthly ||
        GCDataTypeMonthly.value.errors.month ||
        GCDataTypeMonthly.value.errors.selectedStoreMonthly
    ) {
        return;
    }
    const monthlyData = {
        dataTypeMonthly: dataTypeMonthly,
        yearMonthly: yearMonthly,
        month: month,
        selectedStoreMonthly: selectedStoreMonthly,
    };
    console.log(monthlyData);
    Modal.confirm({
        title: "Confirmation",
        content: "Are you sure you want to generate?",
        okText: "Yes",
        okType: "danger",
        cancelText: "No",
        onOk: () => {
            window.location.href = route(
                "storeaccounting.verifiedGcSubmit",
                monthlyData,
            );
        },
        onCancel() {
            console.log("Cancel");
        },
    });
};

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

    const yearlyData = {
        GCDataType: GCDataType,
        selectedStore: selectedStore,
        year: year,
    };
    console.log(yearlyData);
    router.get(route("storeaccounting.verifiedGcYearlySubmit"), yearlyData);
};
</script>
