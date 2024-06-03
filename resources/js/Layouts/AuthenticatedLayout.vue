<script lang="ts" setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { UserType } from "@/userType";
import {PageWithSharedProps} from "@/types/index"

const page = usePage<PageWithSharedProps>().props;
const { userType, userRole } = UserType();
const collapsed = ref<boolean>(false);
const selectedKeys = ref<string[]>(["1"]);
const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <a-layout style="min-height: 100vh">
            <a-layout-sider v-model:collapsed="collapsed" collapsible width="250px">
                <div class="logo" />
                <a-menu v-model:selectedKeys="selectedKeys" theme="dark" mode="inline">

                    <a-card hoverable style="width: auto; margin: 20px;">
                        <template #cover>
                            <img alt="example"
                                src="https://hips.hearstapps.com/hmg-prod/images/edc-taylor-swift-homes1-6463a3cf7da66.jpg?crop=0.391xw:0.782xh;0.609xw,0.0769xh&resize=1200:*" />
                            <span class="font-mono font-bold text-center">
                                Hello, {{  page.auth.user.format_firstname }}
                            </span>

                        </template>
                        <template #actions>
                            <setting-outlined key="card-setting" />
                            <edit-outlined key="card-edit" />
                        </template>
                    </a-card>

                    <TreasurySideBar v-if="userType('2') && !userRole(2)" />
                    <RetailSidebar v-if="userType('7') && !userRole(7)" />
                    <AccountingSideBar v-if="userType('9') && !userRole(9)" />
                    <FinanceSideBar v-if="userType('3') && !userRole(3)" />
                    <CustodianSideBar v-if="userType('4') && !userRole(4)" />
                    <MarketingSideBar  v-if="userType('6') && !userRole(6)"/>


                    <a-menu-item key="menu-item-user-guide">
                        <SettingOutlined />
                        <span>User Guide</span>
                    </a-menu-item>
                    <a-menu-item key="menu-item-about-us">
                        <InfoCircleOutlined />
                        <span>About Us</span>
                    </a-menu-item>
                </a-menu>
            </a-layout-sider>
            <a-layout class="layout">
                <a-layout-header>
                    <div class="logo" />
                    <a-menu v-model:selectedKeys="selectedKeys" class="w-full" theme="dark" mode="horizontal"
                        :style="{ lineHeight: '64px' }">
                        <div class="flex justify-between w-full">
                        <a-menu-item key="dashboard" class="flex items-center">
                            <Link :href="route('dashboard')"><HomeFilled /></Link>
                        </a-menu-item>
                       
                            <a-menu-item key="nav-item-3">
                                 <Link :href="route('logout')" method="post" class="flex items-center"><LogoutOutlined/> &nbsp; Logout</Link>
                            </a-menu-item>
                        </div>
                    </a-menu>
                </a-layout-header>
                <a-layout-content style="padding: 0 50px">
                    <a-breadcrumb style="margin: 16px 0">
                        <a-breadcrumb-item>Home</a-breadcrumb-item>
                        <a-breadcrumb-item>List</a-breadcrumb-item>
                        <a-breadcrumb-item>App</a-breadcrumb-item>
                    </a-breadcrumb>
                    <div :style="{
                      
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
