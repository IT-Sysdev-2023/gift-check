<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-0">
            <div class="max-w-auto mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <a-row style="
                                background-color: white;
                                padding: 30px;
                                margin-bottom: 30px;
                            " justify="center">
                            <a-col :span="8">
                                <a-statistic title="Regular Gc Budget" :value="data?.budget?.regularBudget"
                                    style="margin-right: 50px">
                                </a-statistic>
                            </a-col>
                            <a-col :span="8">
                                <a-statistic title="Special Gc Budget" :precision="2"
                                    :value="data?.budget?.specialBudget" />
                            </a-col>
                            <a-col :span="8">
                                <a-statistic title="Total Budget" :precision="2" :value="data?.budget?.totalBudget">
                                    <template #prefix>
                                        <FireOutlined />
                                    </template>
                                </a-statistic>
                            </a-col>
                        </a-row>
                        <a-row :gutter="16">
                            <a-col :span="8">
                                <m-card class="uppercase" title="Budget Request" :pending="data?.budgetRequest?.pending"
                                    :approved="data?.budgetRequest?.approved"
                                    :cancelled="data?.budgetRequest?.cancelled" :pRoute="budgetRequestPending"
                                    :aRoute="budgetRequestApproved" :cRoute="budgetRequestCancelled" />
                                <m-card class="uppercase" title="Special GC Requests"
                                    :pending="data?.specialGcRequest?.pending"
                                    :approved="data?.specialGcRequest?.approved" :cancelled="data?.specialGcRequest?.cancelled
                                        " :pRoute="specialGcPending" :aRoute="approvedRequest"
                                    :cRoute="cancelledSpecialRequest">
                                    <inner-m-card label="Released Gc" :routeTo="specialReleasedGc" :count="data?.specialGcRequest?.released
                                        " />
                                    <inner-m-card label=" Reviewed GC For Releasing(Internal/External)"
                                        :routeTo="gcReleasing" :count="data?.specialGcRequest
                                                ?.internalReviewed
                                            " />
                                </m-card>
                                <m-card class="uppercase" title="DTI SPECIAL GC REQUEST" :pRoute="dtiSpecialGcPending">
                                    <inner-m-card label="Released Gc" :routeTo="specialReleasedGc" :count="data?.specialGcRequest?.released
                                        " />
                                    <inner-m-card label=" Reviewed GC For Releasing(DTI)"
                                        :routeTo="gcReleasingDti" :count="data?.revcount
                                            " />
                                </m-card>
                            </a-col>
                            <a-col :span="8">
                                <m-card class="uppercase" title="Store Gc Request" approvedLabel="Released Gc"
                                    :pending="data?.storeGcRequest?.pending" :approved="data?.storeGcRequest?.released"
                                    :cancelled="data?.storeGcRequest?.cancelled" :pRoute="storeGcPending"
                                    :aRoute="storeGcReleased" :cRoute="storeGcCancelled" />

                                <a-card title="Adjustment" class="mb-5 uppercase">
                                    <inner-m-card label="Budget Adjustment" :routeTo="budgetAdjustments"
                                        :count="data?.adjustment?.budget" />
                                    <inner-m-card label="Allocation Adjustment" :routeTo="allocationAdjustment"
                                        :count="data?.adjustment?.allocation" />
                                </a-card>
                            </a-col>
                            <a-col :span="8">
                                <m-card class="uppercase" title="Gc Production Request" :pending="data?.gcProductionRequest?.pending
                                    " :approved="data?.gcProductionRequest?.approved
                                        " :cancelled="data?.gcProductionRequest?.cancelled
                                        " :pRoute="pendingProductionRequest" :aRoute="approvedProductionRequest"
                                    :cRoute="cancelledProductionRequest" />
                                <a-card title="Promo GC Released" class="mb-5 uppercase">
                                    <inner-m-card label="Released GC" :routeTo="promoGcReleased"
                                        :count="data?.promoGcReleased" />
                                </a-card>

                                <a-card title="Institution GC Released" class="mb-5 uppercase">
                                    <inner-m-card label="Transactions" :routeTo="institutionGc"
                                        :count="data?.institutionGcSales" />
                                </a-card>
                                <a-card title="EOD List" class="mb-5 uppercase">
                                    <inner-m-card label="EOD List" :routeTo="eodList" :count="data?.eod" />
                                </a-card>
                            </a-col>
                        </a-row>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { TreasuryDashboardTypes } from "@/types";
import { computed } from "vue";

defineProps<TreasuryDashboardTypes>();

const routeTo = (type: string, status: string) => {
    return `treasury.${type}.${status}`;
};

// Budget request handlers
const budgetRequestPending = computed(() =>
    routeTo("budget.request", "pending"),
);
const budgetRequestApproved = computed(() =>
    routeTo("budget.request", "approved"),
);
const budgetRequestCancelled = computed(() =>
    routeTo("budget.request", "cancelled"),
);

// Store GC request handlers
const storeGcPending = computed(() => routeTo("store.gc", "pending"));
const storeGcReleased = computed(() => routeTo("store.gc", "released"));
const storeGcCancelled = computed(() => routeTo("store.gc", "cancelled"));

// GC Production request handlers
const pendingProductionRequest = computed(() =>
    routeTo("production.request", "pending"),
);
const approvedProductionRequest = computed(() =>
    routeTo("production.request", "approved"),
);
const cancelledProductionRequest = computed(() =>
    routeTo("production.request", "cancelled"),
);

//Special GC Request
const specialGcPending = computed(() => routeTo("special.gc", "pending"));
const gcReleasing = computed(() => routeTo("special.gc", "gcReleasing"));
const gcReleasingDti = computed(() => routeTo("special.gc", "gcReleasingDti"));
const specialReleasedGc = computed(() =>
    routeTo("special.gc", "specialReleasedGc"),
);
// const reviewedGcReleasing = computed(() =>
//     routeTo("special.gc", "reviewedGcReleasing")
// );
const approvedRequest = computed(() =>
    routeTo("special.gc", "approvedRequest"),
);
const cancelledSpecialRequest = computed(() =>
    routeTo("special.gc", "cancelledRequest"),
);

//Adjustment
const budgetAdjustments = computed(() =>
    routeTo("adjustment", "budgetAdjustmentsUpdate"),
);
const allocationAdjustment = computed(() =>
    routeTo("adjustment", "allocation"),
);

//Promo Gc Released
const promoGcReleased = computed(() => routeTo("promo.gc", "released"));
//Institution Gc Sales
const institutionGc = computed(() =>
    routeTo("transactions.institution.gc.sales", "transaction"),
);
const eodList = computed(() => routeTo("transactions.eod", "eodList"));


//dti
const dtiSpecialGcPending = computed(() => routeTo("transactions.dti", "dtiPendingRequest"));
</script>
