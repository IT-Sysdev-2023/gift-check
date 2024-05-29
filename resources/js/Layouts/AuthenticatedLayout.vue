<script lang="ts" setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";

import {
    PieChartOutlined,
    DesktopOutlined,
    UserOutlined,
    TeamOutlined,
    FileOutlined,
} from "@ant-design/icons-vue";

const collapsed = ref<boolean>(false);
const selectedKeys = ref<string[]>(["1"]);
const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <a-layout style="min-height: 100vh">
            <a-layout-sider v-model:collapsed="collapsed" collapsible>
                <div class="logo" />
                <a-menu
                    v-model:selectedKeys="selectedKeys"
                    theme="dark"
                    mode="inline"
                >
                    <a-menu-item key="1">
                        <pie-chart-outlined />
                        <span>Dashboard</span>
                    </a-menu-item>
                    <!-- <a-menu-item key="2">
                        <desktop-outlined />
                        <span>Option 2</span>
                    </a-menu-item> -->
                    <a-sub-menu key="sub1">
                        <template #title>
                            <span>
                                <user-outlined />
                                <span>Transactions</span>
                            </span>
                        </template>
                        <a-menu-item key="3">Tom</a-menu-item>
                        <a-menu-item key="4">Bill</a-menu-item>
                        <a-menu-item key="5">Alex</a-menu-item>
                    </a-sub-menu>
                    <a-sub-menu key="sub2">
                        <template #title>
                            <span>
                                <team-outlined />
                                <span>Sales</span>
                            </span>
                        </template>
                        <a-menu-item key="6">Team 1</a-menu-item>
                        <a-menu-item key="8">Team 2</a-menu-item>
                    </a-sub-menu>
                    <a-sub-menu key="sub3">
                        <template #title>
                            <span>
                                <user-outlined />
                                <span>Masterfile</span>
                            </span>
                        </template>
                        <a-menu-item key="3">Tom</a-menu-item>
                        <a-menu-item key="4">Bill</a-menu-item>
                        <a-menu-item key="5">Alex</a-menu-item>
                    </a-sub-menu>
                    <a-menu-item key="9">
                        <file-outlined />
                        <span>File</span>
                    </a-menu-item>
                </a-menu>
            </a-layout-sider>
            <a-layout class="layout">
                <a-layout-header>
                    <div class="logo" />
                    <a-menu
                        v-model:selectedKeys="selectedKeys"
                        theme="dark"
                        mode="horizontal"
                        :style="{ lineHeight: '64px' }"
                    >
                        <a-menu-item key="1">nav 1</a-menu-item>
                        <a-menu-item key="2">nav 2</a-menu-item>
                        <a-menu-item key="3">nav 3</a-menu-item>
                    </a-menu>
                </a-layout-header>
                <a-layout-content style="padding: 0 50px">
                    <a-breadcrumb style="margin: 16px 0">
                        <a-breadcrumb-item>Home</a-breadcrumb-item>
                        <a-breadcrumb-item>List</a-breadcrumb-item>
                        <a-breadcrumb-item>App</a-breadcrumb-item>
                    </a-breadcrumb>
                    <div
                        :style="{
                            background: '#fff',
                            padding: '24px',
                            
                        }"
                    >
                        <!-- Page Content -->
                        <main>
                            <slot />
                        </main>
                    </div>
                </a-layout-content>
                <a-layout-footer style="text-align: center">
                    Ant Design ©2018 Created by Ant UED
                </a-layout-footer>
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
