<script lang="ts" setup>
import { onMounted, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { UserType } from "@/userType";
import { PageWithSharedProps } from "@/types/index";
import IadSideBar from "@/Components/IadSideBar.vue";
import AdminSidebar from "@/Components/AdminSidebar.vue";
import StoreAccountingSidebar from "@/Components/StoreAccountingSidebar.vue";
import FinanceSideBar from "@/Components/FinanceSideBar.vue";
import EodSidebar from "@/Components/EodSidebar.vue";
import CustodianSideBar from "@/Components/CustodianSideBar.vue";
import RetailGroupSidebar from "@/Components/RetailGroupSidebar.vue";
import TreasurySideBar from "@/Components/TreasurySideBar.vue";
import RetailSidebar from "@/Components/RetailSidebar.vue";
import { computed } from "vue";
import { MenuProps } from "ant-design-vue";
import dayjs from "dayjs";

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
    storeaccounting,
} = UserType();

const collapsed = ref<boolean>(false);
const selectedKeys = ref<string[]>(["1"]);
const curr = ref();

const dashboardRoute = computed(() => {
    const webRoute = route().current(); //get current route in page
    const res = webRoute?.split(".")[0]; // split the routes for e.g if the current route is "treasury.ledger", this would get the treasury
    return res;
});

onMounted(() => {
    curr.value = dashboardRoute.value;
});

const selectedPage: MenuProps["onClick"] = (obj) => {
    curr.value = obj.key;
    router.visit(route(obj.key + ".dashboard"));
};
</script>

<template>
    <div>
        <a-layout style="min-height: 100vh" class="dark-layout">
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
                                    src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRec9vI42pnTjYGIpgq9sIzCFrlkZPmRkTCOw&s"
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
                                    src="https://avatars.githubusercontent.com/u/823566?v=4"
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
                    <store-accounting-sidebar v-if="storeaccounting" />
                    <AdminSidebar v-if="admin && curr === 'admin'" />
                    <TreasurySideBar v-if="treasury || curr === 'treasury'" />
                    <FinanceSideBar v-if="finance || curr === 'finance'" />
                    <CustodianSideBar
                        v-if="custodian || curr === 'custodian'"
                    />
                    <RetailSidebar v-if="retail || curr == 'retail'" />
                    <AccountingSideBar
                        v-if="accounting || curr == 'accounting'"
                    />

                    <MarketingSideBar v-if="marketing || curr == 'marketing'" />
                    <IadSideBar v-if="iad || curr == 'iad'" />
                    <eod-sidebar v-if="eod || curr == 'eod'" />

                    <RetailGroupSidebar
                        v-if="retailgroup || curr == 'retailgroup'"
                    />

                    <a-menu-item
                        key="menu-item-user-guide"
                        @click="() => router.get(route('UserGuide'))"
                    >
                        <UserOutlined />
                        <span>User Guide</span>
                    </a-menu-item>

                    <a-menu-item
                        key="menu-item-about-us"
                        @click="() => router.get(route('AboutUs'))"
                    >
                        <InfoCircleOutlined />
                        <span>About Us</span>
                    </a-menu-item>
                </a-menu>
            </a-layout-sider>
            <a-layout class="layout">
                <a-layout>
                    <a-layout-header
                        theme="dark"
                        style="display: flex; justify-content: end"
                    >
                        <a-menu
                            theme="dark"
                            mode="horizontal"
                            :style="{ lineHeight: '64px', display: 'flex' }"
                            @click="selectedPage"
                        >
                            <a-sub-menu
                                key="dashboards"
                                v-if="page.auth.user.usertype == '1'"
                            >
                                <template #icon>
                                    <split-cells-outlined />
                                </template>
                                <template #title>Dashboards</template>

                                <a-menu-item-group title="Dashboards">
                                    <a-menu-item key="admin">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Admin
                                    </a-menu-item>
                                    <a-menu-item key="treasury">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Treasury
                                    </a-menu-item>
                                    <a-menu-item key="finance">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Finance
                                    </a-menu-item>
                                    <a-menu-item key="custodian">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Custodian
                                    </a-menu-item>
                                    <a-menu-item key="marketing">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Marketing
                                    </a-menu-item>
                                    <a-menu-item key="retail">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Retail
                                    </a-menu-item>
                                    <a-menu-item key="retailgroup">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Retail Group
                                    </a-menu-item>
                                    <a-menu-item key="accounting">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Accounting
                                    </a-menu-item>
                                    <a-menu-item key="iad">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Iad
                                    </a-menu-item>
                                    <a-menu-item key="eod">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Eod
                                    </a-menu-item>
                                </a-menu-item-group>
                            </a-sub-menu>
                        </a-menu>
                        <p class="space-x-5">
                            <Link
                                class="text-white"
                                :href="
                                    page.auth.user.usertype == '1'
                                        ? route('admin.dashboard')
                                        : route(dashboardRoute + '.dashboard')
                                "
                            >
                                <HomeOutlined />
                                Home
                            </Link>
                            <a-button
                                class="text-white"
                                type="ghost"
                                @click="() => router.post(route('logout'))"
                            >
                                <PoweroffOutlined />Logout
                            </a-button>
                        </p>
                    </a-layout-header>
                    <a-layout-content
                        :style="{
                            padding: '24px',
                            background: '#fff',
                            minHeight: '280px',
                        }"
                    >
                        <slot />
                    </a-layout-content>
                </a-layout>

                <footer class="bg-white dark:bg-gray-900">
                    <div class="mx-auto w-full max-w-screen max-h-6">
                        <div
                            class="px-4 py-6 bg-gray-100 dark:bg-gray-700 md:flex md:items-center md:justify-between"
                        >
                            <span
                                class="text-sm text-gray-500 dark:text-gray-300 sm:text-center"
                                >© 2024 - {{ dayjs().year() }}
                                <a href="/">Gift Check</a
                                >. All Rights Reserved.
                            </span>
                            <div
                                class="flex mt-4 sm:justify-center md:mt-0 space-x-5 rtl:space-x-reverse"
                            >
                                <a
                                    href="https://github.com/IT-Sysdev-2023/gift-check"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-gray-400 hover:text-gray-900 dark:hover:text-white"
                                >
                                    <svg
                                        class="w-4 h-4"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 .333A9.911 9.911 0 0 0 6.866 19.65c.5.092.678-.215.678-.477 0-.237-.01-1.017-.014-1.845-2.757.6-3.338-1.169-3.338-1.169a2.627 2.627 0 0 0-1.1-1.451c-.9-.615.07-.6.07-.6a2.084 2.084 0 0 1 1.518 1.021 2.11 2.11 0 0 0 2.884.823c.044-.503.268-.973.63-1.325-2.2-.25-4.516-1.1-4.516-4.9A3.832 3.832 0 0 1 4.7 7.068a3.56 3.56 0 0 1 .095-2.623s.832-.266 2.726 1.016a9.409 9.409 0 0 1 4.962 0c1.89-1.282 2.717-1.016 2.717-1.016.366.83.402 1.768.1 2.623a3.827 3.827 0 0 1 1.02 2.659c0 3.807-2.319 4.644-4.525 4.889a2.366 2.366 0 0 1 .673 1.834c0 1.326-.012 2.394-.012 2.72 0 .263.18.572.681.475A9.911 9.911 0 0 0 10 .333Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <span class="sr-only">GitHub account</span>
                                </a>
                                <a
                                    href="https://guthib.com"
                                     target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-gray-400 hover:text-gray-900 dark:hover:text-white"
                                >
                                    <svg
                                        class="w-4 h-4"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 21 16"
                                    >
                                        <path
                                            d="M16.942 1.556a16.3 16.3 0 0 0-4.126-1.3 12.04 12.04 0 0 0-.529 1.1 15.175 15.175 0 0 0-4.573 0 11.585 11.585 0 0 0-.535-1.1 16.274 16.274 0 0 0-4.129 1.3A17.392 17.392 0 0 0 .182 13.218a15.785 15.785 0 0 0 4.963 2.521c.41-.564.773-1.16 1.084-1.785a10.63 10.63 0 0 1-1.706-.83c.143-.106.283-.217.418-.33a11.664 11.664 0 0 0 10.118 0c.137.113.277.224.418.33-.544.328-1.116.606-1.71.832a12.52 12.52 0 0 0 1.084 1.785 16.46 16.46 0 0 0 5.064-2.595 17.286 17.286 0 0 0-2.973-11.59ZM6.678 10.813a1.941 1.941 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.919 1.919 0 0 1 1.8 2.047 1.93 1.93 0 0 1-1.8 2.045Zm6.644 0a1.94 1.94 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.918 1.918 0 0 1 1.8 2.047 1.93 1.93 0 0 1-1.8 2.045Z"
                                        />
                                    </svg>
                                    <span class="sr-only"
                                        >Discord community</span
                                    >
                                </a>
                            </div>
                        </div>
                    </div>
                </footer>
            </a-layout>
            <ant-float v-if="treasury && page.pendingPrRequest.length" />

            <generated-report-float />
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
