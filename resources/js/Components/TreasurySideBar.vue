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
        "reports",
        "special", //add more key sub-menu
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
    <a-sub-menu v-if="page.auth.user.usertype  === '1'" key="treasury-side-bar">
        <template #title>
            <span>
                <SwapOutlined />
                <span>TreasurySideBar</span>
            </span>
        </template>
        <a-menu :openKeys="setActiveTab" v-model:selectedKeys="highlightRoute" theme="dark" mode="inline"
            @click="handleClick">
            <a-menu-item key="treasury.dashboard">
                <file-outlined />
                <span>
                    <Link :href="route('treasury.dashboard')">
                    {{
                        page.auth.user.usertype == "1"
                            ? "Treasury Dashboard"
                            : "Dashboard"
                    }}</Link>
                </span>
            </a-menu-item>
            <a-sub-menu key="masterfile">
                <template #title>
                    <span>
                        <FileTextOutlined />
                        <span>Masterfile</span>
                    </span>
                </template>
                <a-menu-item key="menu-item-customer">Customer</a-menu-item>
                <a-menu-item key="menu-item-special-external-setup">Special External Setup</a-menu-item>
                <a-menu-item key="menu-item-payment-fund-setup">Payment Fund Setup</a-menu-item>
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
                            <Link :href="route('treasury.transactions.production.gc')">
                            Gift Check</Link>
                        </span></a-menu-item>
                    <a-menu-item key="menu-item-envelope">Envelope</a-menu-item>
                </a-sub-menu>
                <a-menu-item key="menu-item-gc-allocation">GC Allocation</a-menu-item>
                <a-menu-item key="menu-item-gc-releasing-retail-store">GC Releasing (Retail Store)</a-menu-item>
                <a-menu-item key="menu-item-promo-gc-releasing">Promo GC Releasing</a-menu-item>
                <a-menu-item key="menu-item-institution-gc-sales">Institution GC Sales</a-menu-item>
                <a-menu-item key="menu-item-institution-gc-refund">Institution GC Refund</a-menu-item>
                <a-sub-menu key="special">
                    <template #title>
                        <span>
                            <span>Special GC Payment</span>
                        </span>
                    </template>
                    <a-menu-item key="treasury.transactions.special.ext">
                        <span>
                            <Link :href="route('treasury.transactions.special.ext')">
                            Special Ext. Gc Payment</Link>
                        </span></a-menu-item>
                    <a-menu-item key="menu-item-envelope">Special Int. Gc Payment</a-menu-item>
                </a-sub-menu>
                <a-menu-item key="menu-item-gc-sales-report-eod">GC Sales Report (EOD)</a-menu-item>
            </a-sub-menu>

            <a-sub-menu key="adjustment">
                <template #title>
                    <span>
                        <SwapOutlined />
                        <span>Adjustments</span>
                    </span>
                </template>
                <a-menu-item key="menu-item-budget-adjustments">Budget Adjustments</a-menu-item>
                <a-menu-item key="menu-item-allocation">Allocation</a-menu-item>
            </a-sub-menu>

            <a-menu-item key="treasury.budget.ledger">
                <file-outlined />
                <span>
                    <Link :href="route('treasury.budget.ledger')">
                    Budget Ledger</Link>
                </span>
            </a-menu-item>

            <a-menu-item key="treasury.gc.ledger">
                <file-outlined />
                <span>
                    <Link :href="route('treasury.gc.ledger')">
                    GC Ledger</Link>
                </span>
            </a-menu-item>

            <a-sub-menu key="reports">
                <template #title>
                    <span>
                        <LineChartOutlined />
                        <span>Reports</span>
                    </span>
                </template>
                <a-menu-item key="menu-item-gc-report">GC Report</a-menu-item>
            </a-sub-menu>
        </a-menu>
    </a-sub-menu>
    <a-menu v-else :openKeys="setActiveTab" v-model:selectedKeys="highlightRoute" theme="dark" mode="inline"
        @click="handleClick">
        <a-menu-item key="treasury.dashboard">
            <file-outlined />
            <span>
                <Link :href="route('treasury.dashboard')">
                {{
                    page.auth.user.usertype == "1"
                        ? "Treasury Dashboard"
                        : "Dashboard"
                }}</Link>
            </span>
        </a-menu-item>
        <a-sub-menu key="masterfile">
            <template #title>
                <span>
                    <FileTextOutlined />
                    <span>Masterfile</span>
                </span>
            </template>
            <a-menu-item key="menu-item-customer">Customer</a-menu-item>
            <a-menu-item key="menu-item-special-external-setup">Special External Setup</a-menu-item>
            <a-menu-item key="menu-item-payment-fund-setup">Payment Fund Setup</a-menu-item>
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
                        <Link :href="route('treasury.transactions.production.gc')">
                        Gift Check</Link>
                    </span></a-menu-item>
                <a-menu-item key="menu-item-envelope">Envelope</a-menu-item>
            </a-sub-menu>
            <a-menu-item key="menu-item-gc-allocation">GC Allocation</a-menu-item>
            <a-menu-item key="menu-item-gc-releasing-retail-store">GC Releasing (Retail Store)</a-menu-item>
            <a-menu-item key="menu-item-promo-gc-releasing">Promo GC Releasing</a-menu-item>
            <a-menu-item key="menu-item-institution-gc-sales">Institution GC Sales</a-menu-item>
            <a-menu-item key="menu-item-institution-gc-refund">Institution GC Refund</a-menu-item>
            <a-sub-menu key="special">
                <template #title>
                    <span>
                        <span>Special GC Payment</span>
                    </span>
                </template>
                <a-menu-item key="treasury.transactions.special.ext">
                    <span>
                        <Link :href="route('treasury.transactions.special.ext')">
                        Special Ext. Gc Payment</Link>
                    </span></a-menu-item>
                <a-menu-item key="menu-item-envelope">Special Int. Gc Payment</a-menu-item>
            </a-sub-menu>
            <a-menu-item key="menu-item-gc-sales-report-eod">GC Sales Report (EOD)</a-menu-item>
        </a-sub-menu>

        <a-sub-menu key="adjustment">
            <template #title>
                <span>
                    <SwapOutlined />
                    <span>Adjustments</span>
                </span>
            </template>
            <a-menu-item key="menu-item-budget-adjustments">Budget Adjustments</a-menu-item>
            <a-menu-item key="menu-item-allocation">Allocation</a-menu-item>
        </a-sub-menu>

        <a-menu-item key="treasury.budget.ledger">
            <file-outlined />
            <span>
                <Link :href="route('treasury.budget.ledger')">
                Budget Ledger</Link>
            </span>
        </a-menu-item>

        <a-menu-item key="treasury.gc.ledger">
            <file-outlined />
            <span>
                <Link :href="route('treasury.gc.ledger')">
                GC Ledger</Link>
            </span>
        </a-menu-item>

        <a-sub-menu key="reports">
            <template #title>
                <span>
                    <LineChartOutlined />
                    <span>Reports</span>
                </span>
            </template>
            <a-menu-item key="menu-item-gc-report">GC Report</a-menu-item>
        </a-sub-menu>
    </a-menu>

</template>
