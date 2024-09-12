<script lang="ts" setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { UserType } from "@/userType";
import { PageWithSharedProps } from "@/types/index";
import { computed } from "vue";
import IadSideBar from "@/Components/IadSideBar.vue";
import AdminSidebar from "@/Components/AdminSidebar.vue";
import RetailSidebar from "@/Components/RetailSidebar.vue";
import { theme } from "ant-design-vue";

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
    retailgroup,
    eod,
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
        <a-layout style="min-height: 100vh"  class="dark-layout">
            <a-layout-sider v-model:collapsed="collapsed" collapsible width="250px">
                <div class="logo" />
                <a-menu v-model:selectedKeys="selectedKeys" theme="dark" mode="inline">
                    <a-card class="mb-3" v-if="!collapsed" hoverable style="
                            width: auto;
                            background: transparent;
                            border-left: none;
                            border-right: none;
                            border-top: none;
                            border-radius: 0 0 0 0px;
                        ">
                        <div class="flex justify-center">
                            <div v-if="page.auth.user.user_id == 322">
                                <img style="
                                        height: 80px;
                                        width: 80px;
                                        border-radius: 50%;
                                    "
                                    src="https://media.themoviedb.org/t/p/w500/3z8iHWk3yi0ViLWY2uE4608b3Ds.jpg"
                                    alt="usersimage" />
                            </div>
                            <div v-else>
                                <img style="
                                        height: 100px;
                                        width: 100px;
                                        border-radius: 50%;
                                        object-fit: cover;
                                        object-position: center;
                                    "
                                src="https://avatars.githubusercontent.com/u/463230?v=4"
                                    alt="usersimage" />
                            </div>
                        </div>

                        <p class="text-white font-bold text-center mt-4">
                            Hello, {{ page.auth.user.full_name }}
                        </p>
                    </a-card>
                    <div v-else>
                        <div class="flex justify-center mt-3 mb-5">
                            <img style="
                                    width: 50px;
                                    height: 50px;
                                    border-radius: 50%;
                                "
                                src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSRBlr9nmDwG7kYOIKpEVLwj-99AUlYoiohLA&s"
                                alt="logo" />
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
                    <eod-sidebar v-if="eod" />

                    <RetailGroupSidebar v-if="retailgroup" />

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
                    <a-layout-header theme="dark" style="display: flex; justify-content: space-between">
                        <p>
                            <menu-unfold-outlined v-if="collapsed" class="trigger mr-5 text-white"
                                @click="() => (collapsed = !collapsed)" />
                            <menu-fold-outlined v-else class="trigger mr-5 text-white"
                                @click="() => (collapsed = !collapsed)" />
                        </p>
                        <p>
                            <Link class="text-white " :href="route(dashboardRoute)">
                            <HomeOutlined />
                            Home
                            </Link>
                            <a-button class="text-white" type="ghost" @click="() => $inertia.post(route('logout'))"><PoweroffOutlined />Logout</a-button>
                        </p>
                    </a-layout-header>
                    <a-layout-content :style="{
                        padding: '24px',
                        background: '#fff',
                        minHeight: '280px',

                    }" >
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
