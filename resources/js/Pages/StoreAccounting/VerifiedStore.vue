<template>
    <AuthenticatedLayout>
        <h1>NO FUNCTION</h1>
        <!-- <a-tabs style="font-weight: bold">
            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <PieChartOutlined />
                        Verified Store Purchased (Monthly)
                    </span>
                </template>
<a-card>
    <div style="margin-top: 20px">
        <a-form-item>
            <div>Data Type:</div>

            <a-select style="width: 30%" placeholder="Select" v-model:value="verifiedMonthly.type">
                <a-select-option value="">---Select---</a-select-option>
                <a-select-option value="verifiedgc">Store Sales</a-select-option>
            </a-select>
        </a-form-item>

        <a-form-item>
            <div>Store:</div>
            <a-select v-model:value="verifiedMonthly.selectedStore" style="width: 30%" placeholder="Select Store"
                :options="stores" />
        </a-form-item>

        <a-form-item>
            <div>Month and Year:</div>
            <a-date-picker v-model:value="verifiedMonthly.year" picker="month" :disabled-date="disabledDate" />
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
                        Verified Store Purchased (Yearly)
                    </span>
                </template>
    <a-card>
        <div style="margin-top: 20px">
            <a-form-item>
                <div>Data Type:</div>

                <a-select style="width: 30%" placeholder="Select" v-model:value="verifiedYearly.type">
                    <a-select-option value="">---Select---</a-select-option>
                    <a-select-option value="verifiedgc">Store Sales</a-select-option>
                </a-select>
            </a-form-item>

            <a-form-item>
                <div>Store:</div>
                <a-select v-model:value="verifiedYearly.selectedStore" style="width: 30%" placeholder="Select Store"
                    :options="stores" />
            </a-form-item>

            <a-form-item>
                <div>Year:</div>
                <a-date-picker v-model:value="verifiedYearly.year" picker="year" :disabled-date="disabledDate" />
            </a-form-item>
        </div>
        <div>
            <a-button @click="yearlySubmitButton" style="background-color: #1e90ff; color: white">
                <SendOutlined /> Submit
            </a-button>
        </div>
    </a-card>
</a-tab-pane>
</a-tabs> -->
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
// import { defineComponent } from '@vue/composition-api'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from "axios";
import dayjs from "dayjs";
import { ref } from "vue";
import { notification } from "ant-design-vue";
import { useQueueState } from "@/stores/queue-state";

defineProps(["stores"]);

const verifiedMonthly = ref({
    type: "",
    selectedStore: "",
    year: "",
});

const verifiedYearly = ref({
    type: "",
    selectedStore: "",
    year: "",
});

const disabledDate = (current) => {
    return current && current > dayjs().startOf("day");
};

const state = useQueueState();
const monthlySubmitButton = async () => {
    await axios
        .post(route("storeaccounting.verifiedStoreSubmit"), {
            month: dayjs(verifiedMonthly.value.year).month() + 1, // cause in Dayjs January returns 0 in month
            year: dayjs(verifiedMonthly.value.year).year(),
            store: verifiedMonthly.value.selectedStore,
            type: verifiedMonthly.value.type,
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


// this.$inertia.get(
//     route("storeaccounting.puchasedMonthlySubmit"),
//     monthlyData,
// );

const yearlySubmitButton = async () => {

    await axios
        .post(route("storeaccounting.verifiedStoreSubmit"), {
            year: dayjs(verifiedYearly.value.year).year(),
            store: verifiedYearly.value.selectedStore,
            type: verifiedYearly.value.type,
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
    // this.$inertia.get(
    //     route("storeaccounting.purchasedYearlySubmit"),
    //     yearlyData,
    // );
};
</script>
