<script setup lang="ts">
import { PageWithSharedProps } from "@/types";
import { usePage } from "@inertiajs/vue3";

const page = usePage<PageWithSharedProps>().props;
import { ref, computed } from "vue";

const highlightRoute = ref([route().current()]);

const handleClick = (e: any) => {
    highlightRoute.value = [e.key];
};

const setActiveTab = computed(() => {
    const currentR = highlightRoute.value[0].split(".");
    
    const keys = [
        "masterfile",
        "transactions",
        "production",
        "adjustment",
        "reports", //add more key sub-menu
    ];
    const keysToOpen = [];

    keys.forEach(function (item) {
        if (currentR.includes(item)) {
            keysToOpen.push(item);
        }
    });
    return keysToOpen;
});
</script>

<template>
    <a-menu
        :openKeys="setActiveTab"
        v-model:selectedKeys="highlightRoute"
        theme="dark"
        mode="inline"
        @click="handleClick"
    >
        <a-menu-item key="treasury.dashboard">
            <file-outlined />
            <span>
                <Link :href="route('treasury.dashboard')">
                    {{
                        page.auth.user.usertype == "1"
                            ? "Treasury Dashboard"
                            : "Dashboard"
                    }}</Link
                >
            </span>
        </a-menu-item>
        <a-sub-menu key="masterfile">
            <template #title>
                <span>
                    <FileTextOutlined />
                    <span>Masterfile</span>
                </span>
            </template>
            <a-menu-item key="treasury.masterfile.customersetup">
                <Link :href="route('treasury.masterfile.customersetup')">
                    Customer Setup
                </Link>
            </a-menu-item>
            <a-menu-item key="treasury.masterfile.externalSetup">
                <Link :href="route('treasury.masterfile.externalSetup')">
                    Special External Setup
                </Link>
            </a-menu-item>
            <a-menu-item key="treasury.masterfile.paymentFundSetup">
                <Link :href="route('treasury.masterfile.paymentFundSetup')">
                    Payment Fund Setup
                </Link></a-menu-item
            >
        </a-sub-menu>

        <a-sub-menu key="transactions">
            <template #title>
                <span>
                    <DollarOutlined />
                    <span>Transactions</span>
                </span>
            </template>

            <a-menu-item key="treasury.transactions.budgetRequest">
                <Link :href="route('treasury.transactions.budgetRequest')">
                    Budget Request
                </Link>
            </a-menu-item>
            <a-sub-menu key="production">
                <template #title>
                    <span>
                        <span>Production Request</span>
                    </span>
                </template>
                <a-menu-item key="treasury.transactions.production.gc">
                    <span>
                        <Link
                            :href="route('treasury.transactions.production.gc')"
                        >
                            Gift Check</Link
                        >
                    </span></a-menu-item
                >
                <a-menu-item key="treasury.transactions.production.envelope">
                    <Link
                        :href="
                            route('treasury.transactions.production.envelope')
                        "
                    >
                        Envelope</Link
                    ></a-menu-item
                >
            </a-sub-menu>
            <a-menu-item key="treasury.transactions.gcallocation.index"
                ><Link
                    :href="route('treasury.transactions.gcallocation.index')"
                >
                    GC Allocation</Link
                ></a-menu-item
            >
            <a-menu-item key="treasury.transactions.retail.releasing.index">
                <Link
                    :href="
                        route('treasury.transactions.retail.releasing.index')
                    "
                >
                    GC Releasing (Retail Store)</Link
                ></a-menu-item
            >
            <a-menu-item key="treasury.transactions.promo.gc.releasing.index">
                <Link
                    :href="
                        route('treasury.transactions.promo.gc.releasing.index')
                    "
                >
                    Promo GC Releasings</Link
                ></a-menu-item
            >
            <a-menu-item key="treasury.transactions.institution.gc.sales.index">
                <Link
                    :href="
                        route(
                            'treasury.transactions.institution.gc.sales.index'
                        )
                    "
                >
                    Institution GC Sales
                </Link></a-menu-item
            >
            <a-menu-item key="treasury.transactions.intitution.refund.index"
                ><Link
                    :href="
                        route('treasury.transactions.intitution.refund.index')
                    "
                >
                    Institution GC Refund
                </Link></a-menu-item
            >
            <a-menu-item key="treasury.transactions.special.index">
                <Link :href="route('treasury.transactions.special.index')">
                    Special Gc Payment</Link
                ></a-menu-item
            >
            <!-- <a-menu-item key="treasury.coupon.transactions.special.index">
                <Link
                    :href="route('treasury.coupon.transactions.special.index')"
                >
                    Coupon Gc Payment</Link
                ></a-menu-item
            > -->
            <a-menu-item key="treasury.transactions.eod.gcSales">
                <Link :href="route('treasury.transactions.eod.gcSales')">
                    GC Sales Report (EOD)</Link
                ></a-menu-item
            >
        </a-sub-menu>

        <a-sub-menu key="adjustment">
            <template #title>
                <span>
                    <SwapOutlined />
                    <span>Adjustments</span>
                </span>
            </template>
            <!-- <a-menu-item key="treasury.adjustment.budgetAdjustments">
                <Link :href="route('treasury.adjustment.budgetAdjustments')">
                    Budget Adjustments</Link
                ></a-menu-item
            > -->
            <a-menu-item key="treasury.adjustment.allocation.allocationSetup">
                <Link
                    :href="
                        route('treasury.adjustment.allocation.allocationSetup')
                    "
                >
                    Allocation</Link
                ></a-menu-item
            >
        </a-sub-menu>

        <a-menu-item key="treasury.budget.ledger">
            <file-outlined />
            <span>
                <Link :href="route('treasury.budget.ledger')">
                    Budget Ledger</Link
                >
            </span>
        </a-menu-item>

        <a-menu-item key="treasury.gc.ledger">
            <file-outlined />
            <span>
                <Link :href="route('treasury.gc.ledger')"> GC Ledger</Link>
            </span>
        </a-menu-item>

        <a-sub-menu key="reports">
            <template #title>
                <span>
                    <LineChartOutlined />
                    <span>Reports</span>
                </span>
            </template>
            <a-menu-item key="treasury.reports.index">
                <Link :href="route('treasury.reports.index')"> GC Report</Link></a-menu-item
            >
            <a-menu-item key="treasury.reports.eod">
                <Link :href="route('treasury.reports.eod')"> EOD Report</Link></a-menu-item
            >
            <a-menu-item key="treasury.reports.generatedReports">
                <Link :href="route('treasury.reports.generatedReports')"> Generated Reports</Link></a-menu-item
            >
        </a-sub-menu>
    </a-menu>
</template>
