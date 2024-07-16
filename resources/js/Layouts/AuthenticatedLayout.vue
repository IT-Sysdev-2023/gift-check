<script lang="ts" setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { UserType } from "@/userType";
import { PageWithSharedProps } from "@/types/index";
import { computed } from "vue";
import { HomeOutlined, LogoutOutlined, SettingOutlined, UserOutlined } from "@ant-design/icons-vue";
// import { useRoute } from "../../../vendor/tightenco/ziggy/src/js";

const page = usePage<PageWithSharedProps>().props;
const { userType, userRole } = UserType();
const collapsed = ref<boolean>(false);
const selectedKeys = ref<string[]>(["1"]);
const showingNavigationDropdown = ref(false);

// const route = useRoute();

// const url = route().current();

const dashboardRoute = computed(() => {
    const webRoute = route().current(); //get current route in page
    const res = webRoute?.split(".")[0]; // split the routes for e.g if the current route is "treasury.ledger", this will get the treasury
    return res + ".dashboard"; //this would result 'treasury.dashboard'
});
</script>

<template>
    <div>
        <a-layout style="min-height: 100vh">
            <a-layout-sider v-model:collapsed="collapsed" collapsible width="250px">
                <div class="logo" />
                <a-menu v-model:selectedKeys="selectedKeys" theme="dark" mode="inline">
                    <a-card class="mb-3" v-if="!collapsed" hoverable style="width: auto;  ; background: transparent;
                     border-left: none;
                      border-right: none;
                      border-top: none; border-radius: 0 0 0 0px;">
                        <div class="flex justify-center">
                            <div v-if="page.auth.user.user_id == 322">
                                <img style="height: 80px; width: 80px; border-radius: 50%;"
                                    src="../../../public/images/zenitsu.jpg" alt="usersimage">
                            </div>
                            <div v-else>
                                <img style="height: 80px; width: 80px; border-radius: 50%;"
                                    src="../../../public/images/zenitsu.jpg" alt="usersimage">
                            </div>
                        </div>

                        <p class="text-white font-bold text-center mt-4">
                            Hello, {{ page.auth.user.full_name }}
                        </p>
                    </a-card>
                    <div v-else>
                        <div class="flex justify-center mt-3 mb-5">
                            <img style="width: 50px; height: 50px; border-radius: 50%;"
                                src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSRBlr9nmDwG7kYOIKpEVLwj-99AUlYoiohLA&s"
                                alt="logo">
                        </div>
                    </div>


                    <TreasurySideBar v-if="userType('2') && !userRole(2)" />
                    <RetailSidebar v-if="userType('7') && !userRole(7)" />
                    <AccountingSideBar v-if="userType('9') && !userRole(9)" />
                    <FinanceSideBar v-if="userType('3') && !userRole(3)" />
                    <CustodianSideBar v-if="userType('4') && !userRole(4)" />
                    <MarketingSideBar v-if="userType('6') && !userRole(6)" />

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
                    <a-layout-header theme="dark" style="display: flex; justify-content: space-between;">
                        <p>
                            <menu-unfold-outlined v-if="collapsed" class="trigger mr-5 text-white"
                                @click="() => (collapsed = !collapsed)" />
                            <menu-fold-outlined v-else class="trigger mr-5 text-white"
                                @click="() => (collapsed = !collapsed)" />
                        </p>
                        <p>
                            <Link class="text-white mr-5" :href="route(dashboardRoute)">
                            <HomeOutlined />
                            Home
                            </Link>
                            <Link class="text-white mr-5" :href="route('logout')" method="post">
                            <LogoutOutlined />
                            Logout
                            </Link>
                        </p>
                    </a-layout-header>
                    <a-layout-content
                        :style="{ margin: '24px 16px', padding: '24px', background: '#fff', minHeight: '280px' }">
                        <a-watermark content="Jessan Palban property" style="height: 100%;">
                            <slot />
                        </a-watermark>
                    </a-layout-content>
                </a-layout>
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
