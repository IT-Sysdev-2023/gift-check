<script lang="ts" setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { UserType } from "@/userType";
import { PageWithSharedProps } from "@/types/index";
import { computed } from "vue";
import IadSideBar from "@/Components/IadSideBar.vue";
import AdminSidebar from "@/Components/AdminSidebar.vue";

const page = usePage<PageWithSharedProps>().props;
const {
    treasury,
    retail,
    admin,
    finance,
    accounting,
    iad,
    custodian,
    marketing,
} = UserType();

const collapsed = ref<boolean>(false);
const selectedKeys = ref<string[]>(["1"]);

const dashboardRoute = computed(() => {
    const webRoute = route().current(); //get current route in page
    const res = webRoute?.split(".")[0]; // split the routes for e.g if the current route is "treasury.ledger", this would get the treasury
    return res + ".dashboard"; //this would result 'treasury.dashboard'
});
</script>

<template>
    <div>
        <a-layout style="min-height: 100vh">
            <a-layout-sider
                v-model:collapsed="collapsed"
                collapsible
                width="250px"
            >
                <div class="logo" />
                <a-menu
                    v-model:selectedKeys="selectedKeys"
                    theme="dark"
                    mode="inline"
                >
                    <a-card
                        class="mb-3"
                        v-if="!collapsed"
                        hoverable
                        style="
                            width: auto;
                            background: transparent;
                            border-left: none;
                            border-right: none;
                            border-top: none;
                            border-radius: 0 0 0 0px;
                        "
                    >
                        <div class="flex justify-center">
                            <div v-if="page.auth.user.user_id == 322">
                                <img
                                    style="
                                        height: 80px;
                                        width: 80px;
                                        border-radius: 50%;
                                    "
                                    src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQiKgCygEhMX5cH4hBwMR8YCz3NMfmJ3JdGcw&s"
                                    alt="usersimage"
                                />
                            </div>
                            <div v-else>
                                <img
                                    style="
                                        height: 100px;
                                        width: 100px;
                                        border-radius: 50%;
                                        object-fit: cover;
                                        object-position: center;
                                    "
                                    src="data:image/jpeg;base64,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"
                                    alt="usersimage"
                                />
                            </div>
                        </div>

                        <p class="text-white font-bold text-center mt-4">
                            Hello, {{ page.auth.user.full_name }}
                        </p>
                    </a-card>
                    <div v-else>
                        <div class="flex justify-center mt-3 mb-5">
                            <img
                                style="
                                    width: 50px;
                                    height: 50px;
                                    border-radius: 50%;
                                "
                                src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSRBlr9nmDwG7kYOIKpEVLwj-99AUlYoiohLA&s"
                                alt="logo"
                            />
                        </div>
                    </div>

                    <AdminSidebar v-if="admin" />
                    <TreasurySideBar v-if="treasury" />
                    <RetailSidebar v-if="retail" />
                    <AccountingSideBar v-if="accounting" />
                    <FinanceSideBar v-if="finance" />
                    <CustodianSideBar v-if="custodian" />
                    <MarketingSideBar v-if="marketing" />
                    <IadSideBar v-if="iad" />

                    <a-menu-item key="menu-item-user-guide">
                        <UserOutlined />
                        <span>User Guide</span>
                    </a-menu-item>
                    <a-menu-item key="menu-item-about-us">
                        <SettingOutlined />
                        <span>My Settings</span>
                    </a-menu-item>
                    <a-menu-item key="menu-item-about-us">
                        <InfoCircleOutlined />
                        <span>About Us</span>
                    </a-menu-item>
                </a-menu>
            </a-layout-sider>
            <a-layout class="layout">
                <a-layout>
                    <a-layout-header
                        theme="dark"
                        style="display: flex; justify-content: space-between"
                    >
                        <p>
                            <menu-unfold-outlined
                                v-if="collapsed"
                                class="trigger mr-5 text-white"
                                @click="() => (collapsed = !collapsed)"
                            />
                            <menu-fold-outlined
                                v-else
                                class="trigger mr-5 text-white"
                                @click="() => (collapsed = !collapsed)"
                            />
                        </p>
                        <p>
                            <Link
                                class="text-white mr-5"
                                :href="route(dashboardRoute)"
                            >
                                <HomeOutlined />
                                Home
                            </Link>
                            <Link
                                class="text-white mr-5"
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                <LogoutOutlined />
                                Logout
                            </Link>
                        </p>
                    </a-layout-header>
                    <a-layout-content
                        :style="{
                            margin: '24px 16px',
                            padding: '24px',
                            background: '#fff',
                            minHeight: '280px',
                        }"
                    >
                        <slot />
                    </a-layout-content>
                </a-layout>
            </a-layout>
            <ant-float v-if="treasury && page.pendingPrRequest.length" />
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
