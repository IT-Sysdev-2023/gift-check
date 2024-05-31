<script lang="ts" setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { UserType } from "@/userType";
import {
    PieChartOutlined,
    FileTextOutlined,
    FileOutlined,
    SwapOutlined,
    DollarOutlined,
    LineChartOutlined,
    InfoCircleOutlined,
    SettingOutlined,
    EditOutlined
} from "@ant-design/icons-vue";


const { userType, userRole } = UserType();
const collapsed = ref<boolean>(false);
const selectedKeys = ref<string[]>(["1"]);
const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <a-layout style="min-height: 100vh">
            <a-layout-sider v-model:collapsed="collapsed" collapsible>
                <div class="logo" />
                <a-menu v-model:selectedKeys="selectedKeys" theme="dark" mode="inline">
                    <a-menu-item key="menu-item-dashboard" style="margin-top: 10px;">
                        <pie-chart-outlined />
                        <span>Dashboard</span>
                    </a-menu-item>

                    <a-card hoverable style="width: auto; margin: 20px;">
                        <template #cover>
                            <img alt="example"
                                src="http://172.16.161.34:8080/hrms/images/users/03836-2023=2024-01-25=Profile=14-30-53-PM.jpg" />
                            <span class="font-mono font-bold text-center">Hello, HarTzy</span>
                        </template>
                        <template #actions>
                            <setting-outlined key="card-setting" />
                            <edit-outlined key="card-edit" />
                        </template>
                    </a-card>

                    <div v-if="userType('2') && !userRole(2)">
                        <a-sub-menu key="menu-sub-masterfile">
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

                        <a-sub-menu key="menu-sub-transactions">
                            <template #title>
                                <span>
                                    <DollarOutlined />
                                    <span>Transactions</span>
                                </span>
                            </template>
                            <a-menu-item key="menu-item-budget-request">Budget Request</a-menu-item>
                            <a-sub-menu key="menu-sub-adjustments-production">
                                <template #title>
                                    <span>
                                        <span>Production Request</span>
                                    </span>
                                </template>
                                <a-menu-item key="menu-item-gift-check">Gift Check</a-menu-item>
                                <a-menu-item key="menu-item-envelope">Envelope</a-menu-item>
                            </a-sub-menu>
                            <a-menu-item key="menu-item-gc-allocation">GC Allocation</a-menu-item>
                            <a-menu-item key="menu-item-gc-releasing-retail-store">GC Releasing (Retail Store)</a-menu-item>
                            <a-menu-item key="menu-item-promo-gc-releasing">Promo GC Releasing</a-menu-item>
                            <a-menu-item key="menu-item-institution-gc-sales">Institution GC Sales</a-menu-item>
                            <a-menu-item key="menu-item-institution-gc-refund">Institution GC Refund</a-menu-item>
                            <a-menu-item key="menu-item-special-gc-payment">Special GC Payment</a-menu-item>
                            <a-menu-item key="menu-item-gc-sales-report-eod">GC Sales Report (EOD)</a-menu-item>
                        </a-sub-menu>

                        <a-sub-menu key="menu-sub-adjustments">
                            <template #title>
                                <span>
                                    <SwapOutlined />
                                    <span>Adjustments</span>
                                </span>
                            </template>
                            <a-menu-item key="menu-item-budget-adjustments">Budget Adjustments</a-menu-item>
                            <a-menu-item key="menu-item-allocation">Allocation</a-menu-item>
                        </a-sub-menu>

                        <a-menu-item key="menu-item-budget-ledger">
                            <file-outlined />
                            <span>Budget Ledger</span>
                        </a-menu-item>
                        <a-menu-item key="menu-item-gc-ledger">
                            <file-outlined />
                            <span>GC Ledger</span>
                        </a-menu-item>

                        <a-sub-menu key="menu-sub-reports">
                            <template #title>
                                <span>
                                    <LineChartOutlined />
                                    <span>Reports</span>
                                </span>
                            </template>
                            <a-menu-item key="menu-item-gc-report">GC Report</a-menu-item>
                        </a-sub-menu>
                    </div>

                    <a-menu-item key="menu-item-user-guide">
                        <SettingOutlined />
                        <span>User Guide</span>
                    </a-menu-item>
                    <a-menu-item key="menu-item-about-us" >
                        <InfoCircleOutlined />
                        <span>About Us</span>
                    </a-menu-item>
                </a-menu>
            </a-layout-sider>
            <a-layout class="layout">
                <a-layout-header>
                    <div class="logo" />
                    <a-menu v-model:selectedKeys="selectedKeys" theme="dark" mode="horizontal"
                        :style="{ lineHeight: '64px' }">
                        <a-menu-item key="nav-item-1">nav 1</a-menu-item>
                        <a-menu-item key="nav-item-2">nav 2</a-menu-item>
                        <a-menu-item key="nav-item-3">
                            <Link :href="route('logout')" method="post">Logout</Link>
                        </a-menu-item>
                    </a-menu>
                </a-layout-header>
                <a-layout-content style="padding: 0 50px">
                    <a-breadcrumb style="margin: 16px 0">
                        <a-breadcrumb-item>Home</a-breadcrumb-item>
                        <a-breadcrumb-item>List</a-breadcrumb-item>
                        <a-breadcrumb-item>App</a-breadcrumb-item>
                    </a-breadcrumb>
                    <div :style="{
                        background: '#fff',
                        padding: '24px',

                    }">
                        <!-- Page Content -->
                        <main>
                            <slot />
                        </main>
                    </div>
                </a-layout-content>
            </a-layout>
        </a-layout>
    </div>
</template>

<style scoped>
#components-layout-demo-side .logo {
    height: 32px;
    margin: 16px;
    background: rgba(255, 255, 255, 0.3);
}

.site-layout .site-layout-background {
    background: #fff;
}

[data-theme="dark"] .site-layout .site-layout-background {
    background: #141414;
}
</style>
