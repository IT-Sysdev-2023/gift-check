<script lang="ts" setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
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
                    <a-menu-item key="dashboard">
                        <pie-chart-outlined />
                        <span>Dashboard</span>
                    </a-menu-item>


                    <a-card hoverable style="width: auto; margin: 20px;">
                        <template #cover>
                            <img alt="example"
                                src="http://172.16.161.34:8080/hrms/images/users/03836-2023=2024-01-25=Profile=14-30-53-PM.jpg" />
                        </template>
                        <template #actions>
                            <setting-outlined key="setting" />
                            <edit-outlined key="edit" />
                        </template>
                    </a-card>


                    <a-sub-menu key="masterfile" v-if="userType('2') && !userRole(2)">
                        <template #title>
                            <span>
                                <FileTextOutlined />
                                <span>Masterfile</span>
                            </span>
                        </template>
                        <a-menu-item key="customer">Customer</a-menu-item>
                        <a-menu-item key="special-external-setup">Special External Setup</a-menu-item>
                        <a-menu-item key="payment-fund-setup">Payment Fund Setup</a-menu-item>
                    </a-sub-menu>

                    <a-sub-menu key="transactions" v-if="userType('2') && !userRole(2)">
                        <template #title>
                            <span>
                                <DollarOutlined />
                                <span>Transactions</span>
                            </span>
                        </template>
                        <a-menu-item key="budget-request">Budget Request</a-menu-item>
                        <a-menu-item key="production-request">Production Request</a-menu-item>
                        <a-menu-item key="gc-allocation">GC Allocation</a-menu-item>
                        <a-menu-item key="gc-releasing-retail-store">GC Releasing (Retail Store)</a-menu-item>
                        <a-menu-item key="promo-gc-releasing">Promo GC Releasing</a-menu-item>
                        <a-menu-item key="institution-gc-sales">Institution GC Sales</a-menu-item>
                        <a-menu-item key="institution-gc-refund">Institution GC Refund</a-menu-item>
                        <a-menu-item key="special-gc-payment">Special GC Payment</a-menu-item>
                        <a-menu-item key="gc-sales-report-eod">GC Sales Report (EOD)</a-menu-item>
                    </a-sub-menu>

                    <a-sub-menu key="adjustments">
                        <template #title>
                            <span>
                                <SwapOutlined />
                                <span>Adjustments</span>
                            </span>
                        </template>
                        <a-menu-item key="budget-adjustments">Budget Adjustments</a-menu-item>
                        <a-menu-item key="allocation">Allocation</a-menu-item>
                    </a-sub-menu>

                    <a-menu-item key="budget-ledger">
                        <file-outlined />
                        <span>Budget Ledger</span>
                    </a-menu-item>
                    <a-menu-item key="gc-ledger">
                        <file-outlined />
                        <span>GC Ledger</span>
                    </a-menu-item>

                    <a-sub-menu key="reports">
                        <template #title>
                            <span>
                                <LineChartOutlined />
                                <span>Reports</span>
                            </span>
                        </template>
                        <a-menu-item key="gc-report">GC Report</a-menu-item>
                    </a-sub-menu>
                    <a-menu-item key="user-guide">
                        <SettingOutlined />
                        <span>User Guide</span>
                    </a-menu-item>
                    <a-menu-item key="about-us">
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
                        <a-menu-item key="nav-1">nav 1</a-menu-item>
                        <a-menu-item key="nav-2">nav 2</a-menu-item>
                        <!-- <a-menu-item key="nav-3">
                            <Link :href="route('logout')" method="post">Logout</Link>
                        </a-menu-item> -->
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
