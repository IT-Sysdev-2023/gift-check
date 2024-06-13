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
                            <!-- <img alt="example"
                                src="data:image/jpeg;base64,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" /> -->

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
                                 <Link :href="route('logout')" method="post" as="button" class="flex items-center"><LogoutOutlined/> &nbsp; Logout</Link>
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
                    <div>
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
