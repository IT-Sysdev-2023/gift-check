<script lang="ts" setup>
import { onMounted, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { UserType } from "@/userType";
import { PageWithSharedProps } from "@/types/index";
import IadSideBar from "@/Components/IadSideBar.vue";
import AdminSidebar from "@/Components/AdminSidebar.vue";
import RetailSidebar from "@/Components/RetailSidebar.vue";
import { computed } from "vue";

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
const curr = ref();

onMounted(() => {
    const webRoute = route().current(); //get current route in page
    const res = webRoute?.split(".")[0]; 
    curr.value = res;
})

const dashboardRoute = computed(() => {
    const webRoute = route().current(); //get current route in page
    const res = webRoute?.split(".")[0]; // split the routes for e.g if the current route is "treasury.ledger", this would get the treasury
    return res;
});
const selectedPage = (obj) => {
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

                    <AdminSidebar v-if="admin && curr === 'admin'" />
                    <TreasurySideBar
                        v-if="treasury || curr === 'treasury'"
                    />
                    <FinanceSideBar
                        v-if="finance || curr === 'finance'"
                    />
                    <CustodianSideBar
                        v-if="custodian || curr === 'custodian'"
                    />
                    <RetailSidebar
                        v-if="retail || curr == 'retail'"
                    />
                    <AccountingSideBar
                        v-if="accounting || curr == 'accounting'"
                    />
                    
                    
                    <MarketingSideBar
                        v-if="marketing || curr == 'marketing'"
                    />
                    <IadSideBar v-if="iad || curr == 'iad'" />
                    <eod-sidebar v-if="eod || curr == 'eod'" />

                    <RetailGroupSidebar
                        v-if="retailgroup || curr == 'retailgroup'"
                    />

                    <a-menu-item key="menu-item-user-guide">
                        <UserOutlined />
                        <span>User Guide</span>
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
                        style="display: flex; justify-content: end"
                    >
                        <!-- <p>
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
                        </p> -->
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
                            <!-- <Link
                                class="text-white"
                                :href="route('custodian.dashboard')"
                            >
                                <HomeOutlined />
                                Custodian Dashboard
                            </Link>
                            <Link
                                class="text-white"
                                :href="route('treasury.dashboard')"
                            >
                                <HomeOutlined />
                                Treasury Dashboard
                            </Link> -->
                            <Link
                                class="text-white"
                                :href="route(dashboardRoute + '.dashboard')"
                            >
                                <HomeOutlined />
                                Home
                            </Link>
                            <a-button
                                class="text-white"
                                type="ghost"
                                @click="() => $inertia.post(route('logout'))"
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
