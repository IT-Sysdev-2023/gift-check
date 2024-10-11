<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-0">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <a-row
                            style="
                                background-color: white;
                                padding: 30px;
                                margin-bottom: 30px;
                            "
                            justify="center"
                        >
                            <a-col :span="8">
                                <a-statistic
                                    title="Regular Gc Budget"
                                    :value="data?.budget?.regularBudget"
                                    style="margin-right: 50px"
                                >
                                </a-statistic>
                            </a-col>
                            <a-col :span="8">
                                <a-statistic
                                    title="Special Gc Budget"
                                    :precision="2"
                                    :value="data?.budget?.specialBudget"
                                />
                            </a-col>
                            <a-col :span="8">
                                <a-statistic
                                    title="Total Budget"
                                    :precision="2"
                                    :value="data?.budget?.totalBudget"
                                >
                                    <template #prefix>
                                        <FireOutlined />
                                    </template>
                                </a-statistic>
                            </a-col>
                        </a-row>
                        <a-row :gutter="16">
                            <Card
                                use-default
                                title="Budget Request"
                                :pending="data?.budgetRequest?.pending"
                                :approved="data?.budgetRequest?.approved"
                                :cancelled="data?.budgetRequest?.cancelled"
                                @pending-event="budgetRequestPending"
                                @approved-event="budgetRequestApproved"
                                @cancelled-event="budgetRequestCancelled"
                            />
                            <Card
                                use-default
                                title="Store Gc Request"
                                card2="Released Gc"
                                :pending="data?.storeGcRequest?.pending"
                                :approved="data?.storeGcRequest?.released"
                                :cancelled="data?.storeGcRequest?.cancelled"
                                @pending-event="storeGcPending"
                                @approved-event="storeGcReleased"
                                @cancelled-event="storeGcCancelled"
                            />
                            <Card
                                use-default
                                title="Gc Production Request"
                                :pending="data?.gcProductionRequest?.pending"
                                :approved="data?.gcProductionRequest?.approved"
                                :cancelled="
                                    data?.gcProductionRequest?.cancelled
                                "
                                @pending-event="pendingProductionRequest"
                                @approved-event="approvedProductionRequest"
                            />
                            <Card
                                use-default
                                title="Special GC Requests"
                                :pending="data?.specialGcRequest?.pending"
                                :approved="data?.specialGcRequest?.approved"
                                :cancelled="data?.specialGcRequest?.cancelled"
                                @pending-event="specialGcPending"
                                @approved-event="approvedRequest"
                            >
                                <!-- <CardBadge
                                    :count="data?.specialGcRequest?.reviewed"
                                    title="Reviewed GC for Releasing(External)"
                                    @event="reviewedGcReleasing"
                                /> -->
                                <CardBadge
                                    :count="data?.specialGcRequest?.released"
                                    title="Released GC"
                                     @event="specialReleasedGc"
                                />
                                <CardBadge
                                    :count="data?.specialGcRequest?.internalReviewed"
                                    title="Reviewed GC For Releasing(Internal/ External)"
                                     @event="gcReleasing"
                                />
                            </Card>
                            <Card title="Adjustment">
                                <template #badge>
                                    <CardBadge
                                        :count="data?.adjustment?.budget"
                                        title="Budget Adjustment"
                                    />
                                    <CardBadge
                                        :count="data?.adjustment?.allocation"
                                        title="Allocation Adjustment"
                                    />
                                </template>
                            </Card>
                            <Card title="Promo GC Released">
                                <template #badge>
                                    <CardBadge
                                        :count="data?.adjustment?.budget"
                                        title="Released Gc"
                                    />
                                </template>
                            </Card>
                            <Card title="Institution GC Sales">
                                <template #badge>
                                    <CardBadge
                                        :count="data?.institutionGcSales"
                                        title="Transactions"
                                        @event="institutionGc"
                                    />
                                </template>
                            </Card>
                            <Card title="EOD List">
                                <template #badge>
                                    <CardBadge
                                        :count="data?.eod"
                                        title="Eod List"
                                        @event="eodList"
                                    />
                                </template>
                            </Card>
                        </a-row>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";

const props = defineProps<{
    data?: {
        budgetRequest: {
            pending: number;
            approved: number;
            cancelled: number;
        };
        storeGcRequest: {
            pending: number;
            released: number;
            cancelled: number;
        };
        gcProductionRequest: {
            pending: number;
            approved: number;
            cancelled: number;
        };
        specialGcRequest: {
            pending: number;
            approved: number;
            cancelled: number;
            reviewed: number;
            released: number;
            internalReviewed: number
        };
        adjustment: {
            budget: number;
            allocation: number;
        };
        eod: number;
        budget: {
            totalBudget: number,
            regularBudget: number,
            specialBudget: number
        };
        institutionGcSales: number;
    };
}>();

const routeTo = (type: string, status: string) => {
    router.get(route(`treasury.${type}.${status}`));
};

// Budget request handlers
const budgetRequestPending = () => routeTo("budget.request", "pending");
const budgetRequestApproved = () => routeTo("budget.request", "approved");
const budgetRequestCancelled = () => routeTo("budget.request", "cancelled");

// Store GC request handlers
const storeGcPending = () => routeTo("store.gc", "pending");
const storeGcReleased = () => routeTo("store.gc", "released");
const storeGcCancelled = () => routeTo("store.gc", "cancelled");

// GC Production request handlers
const pendingProductionRequest = () => routeTo("production.request", "pending");
const approvedProductionRequest = () =>
    routeTo("production.request", "approved");

//Special GC Request
const specialGcPending = () => routeTo("special.gc", "pending");
const gcReleasing = () => routeTo("special.gc", "gcReleasing");
const specialReleasedGc = () => routeTo("special.gc", "specialReleasedGc")
const reviewedGcReleasing = () => routeTo("special.gc", "reviewedGcReleasing")
const approvedRequest = () => routeTo("special.gc", "approvedRequest")

const institutionGc = () =>
    routeTo("transactions.institution.gc.sales", "transaction");
const eodList = () => routeTo("transactions.eod", "eodList");
</script>