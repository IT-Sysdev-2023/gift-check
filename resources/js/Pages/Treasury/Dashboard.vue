<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { UserType } from "@/userType";
import { router } from "@inertiajs/vue3";

const { userType, userRole } = UserType();

defineProps<{
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
        };
        adjustment: {
            budget: number;
            allocation: number;
        };
        eod: number;
        budget: number;
    };
}>();

const budgetRequestCancelled = () => {
    router.get(route("treasury.budget.request.cancelled"));
};

const budgetRequestPending = () => {
    router.get(route("treasury.budget.request.pending"));
};

const budgetRequestApproved = () => {
    router.get(route("treasury.budget.request.approved"));
};
</script>

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
                            <a-col :span="12">
                                <a-statistic
                                    title="Current Budget"
                                    :value="data?.budget"
                                    style="margin-right: 50px"
                                >
                                    <template #prefix>
                                        <FireOutlined />
                                    </template>
                                </a-statistic>
                            </a-col>
                            <a-col :span="12">
                                <a-statistic
                                    title="Abog sa Kalibotan"
                                    :precision="2"
                                    :value="112893"
                                />
                            </a-col>
                        </a-row>
                        <a-row :gutter="16">
                            <Card
                                use-default
                                title="Budget Request"
                                :pending="data?.budgetRequest.pending"
                                :approved="data?.budgetRequest.approved"
                                :cancelled="data?.budgetRequest.cancelled"
                                @pending-event="budgetRequestPending"
                                @approved-event="budgetRequestApproved"
                                @cancelled-event="budgetRequestCancelled"
                            />
                            <Card
                                use-default
                                title="Store Gc Request"
                                :pending="data?.storeGcRequest.pending"
                                :approved="data?.storeGcRequest.released"
                                :cancelled="data?.storeGcRequest.cancelled"
                            />
                            <Card
                                use-default
                                title="Gc Production Request"
                                :pending="data?.gcProductionRequest.pending"
                                :approved="data?.gcProductionRequest.approved"
                                :cancelled="data?.gcProductionRequest.cancelled"
                            />
                            <Card
                                use-default
                                title="Special GC Requests"
                                :pending="data?.specialGcRequest.pending"
                                :approved="data?.specialGcRequest.approved"
                                :cancelled="data?.specialGcRequest.cancelled"
                            >
                                <CardBadge
                                    :count="data?.specialGcRequest.reviewed"
                                    title="Reviewed GC for Releasing"
                                />
                                <CardBadge
                                    :count="data?.specialGcRequest.released"
                                    title="Released GC"
                                />
                            </Card>
                            <Card title="Adjustment">
                                <template #badge>
                                    <CardBadge
                                        :count="data?.adjustment.budget"
                                        title="Budget Adjustment"
                                    />
                                    <CardBadge
                                        :count="data?.adjustment.allocation"
                                        title="Allocation Adjustment"
                                    />
                                </template>
                            </Card>
                            <Card title="Promo GC Released">
                                <template #badge>
                                    <CardBadge
                                        :count="data?.adjustment.budget"
                                        title="Released Gc"
                                    />
                                </template>
                            </Card>
                            <Card title="Institution GC Sales">
                                <template #badge>
                                    <CardBadge
                                        :count="data?.adjustment.budget"
                                        title="Transactions"
                                    />
                                </template>
                            </Card>
                            <Card title="EOD List">
                                <template #badge>
                                    <CardBadge
                                        :count="data?.eod"
                                        title="Eod List"
                                    />
                                </template>
                            </Card>
                        </a-row>
                    </div>
                    <!-- <h1 v-if="userType('7') && !userRole(2)"> {{ $page.props.auth.user }}</h1> -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
