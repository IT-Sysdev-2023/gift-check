<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { UserType } from "@/userType";
import Card from "@/Components/Card.vue";
import Badge from "@/Components/Badge.vue";

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
    };
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- hwllo -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-0">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <a-row :gutter="16">
                            <Card
                                use-default
                                title="Budget Request"
                                :pending="data?.budgetRequest.pending"
                                :approved="data?.budgetRequest.approved"
                                :cancelled="data?.budgetRequest.cancelled"
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
                                <Badge
                                    :count="data?.specialGcRequest.reviewed"
                                    title="Reviewed GC for Releasing"
                                />
                                <Badge
                                    :count="data?.specialGcRequest.released"
                                    title="Released GC"
                                />
                            </Card>
                            <Card title="Adjustment">
                                <template #badge>
                                    <Badge
                                        :count="data?.adjustment.budget"
                                        title="Budget Adjustment"
                                    />
                                    <Badge
                                        :count="data?.adjustment.allocation"
                                        title="Allocation Adjustment"
                                    />
                                </template>
                            </Card>
                            <Card title="Promo GC Released">
                                <template #badge>
                                    <Badge
                                        :count="data?.adjustment.budget"
                                        title="Released Gc"
                                    />
                                </template>
                            </Card>
                            <Card title="Institution GC Sales">
                                <template #badge>
                                    <Badge
                                        :count="data?.adjustment.budget"
                                        title="Transactions"
                                    />
                                </template>
                            </Card>
                            <!--  <Card title="Promo GC Released" />
                            <Card title="Institution GC Sales" /> -->
                        </a-row>
                        <p>{{ data }}</p>
                    </div>
                    <!-- <h1 v-if="userType('7') && !userRole(2)"> {{ $page.props.auth.user }}</h1> -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
