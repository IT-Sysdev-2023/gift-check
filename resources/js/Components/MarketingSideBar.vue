<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { PageWithSharedProps } from "@/types";
import { usePage } from "@inertiajs/vue3";
import { route } from 'ziggy-js';
const page = usePage<PageWithSharedProps>().props;

const highlightRoute = ref([route().current()]);
const handleClick = (e: any) => {
    highlightRoute.value = [e.key];
};
const currentActiveMenu = [route().current().split(".")[1]];
</script>

<template>
    <div v-if="page.auth.user.user_role == 0">
        <a-sub-menu
            v-if="page.auth.user.usertype == '1'"
            key="marketing-side-bar"
        >
            <template #title>
                <span>
                    <SwapOutlined />
                    <span>Marketing Sidebar</span>
                </span>
            </template>
            <a-menu
                :openKeys="currentActiveMenu"
                :selectedKeys="highlightRoute"
                theme="dark"
                mode="inline"
                @click="handleClick"
            >
                <a-menu-item key="marketing.dashboard">
                    <QrcodeOutlined />
                    <span>
                        <Link :href="route('marketing.dashboard')">
                            {{
                                page.auth.user.usertype == "1"
                                    ? "MarketingDashboard"
                                    : "Dashboard"
                            }}</Link
                        >
                    </span>
                </a-menu-item>
                <a-menu-item key="marketing.promo.gc.request">
                    <QrcodeOutlined />
                    <span>
                        <Link :href="route('marketing.promo.gc.request')">
                        </Link
                        >Promo GC Request
                    </span>
                </a-menu-item>
                <a-sub-menu key="addPromo">
                    <template #title>
                        <span>
                            <FundProjectionScreenOutlined />
                            <span>Promo</span>
                        </span>
                    </template>
                    <a-menu-item key="marketing.addPromo.add">
                        <Link :href="route('marketing.addPromo.add')"
                            >Add New Promo</Link
                        >
                    </a-menu-item>
                    <a-menu-item key="marketing.addPromo.list">
                        <Link :href="route('marketing.addPromo.list')"
                            >Promo List</Link
                        >
                    </a-menu-item>
                </a-sub-menu>

                <a-menu-item key="releasedPromoGC">
                    <template #icon>
                        <RetweetOutlined />
                    </template>
                    <span>
                        <Link :href="route('marketing.releaseGc.releasegc')"
                            >Released Promo GC</Link
                        >
                    </span>
                </a-menu-item>

                <!-- <a-menu-item key="releasedPromoGC">
        <RetweetOutlined />
        <span><Link :href="route('release.promo.gc')">Released Promo GC</Link></span>
    </a-menu-item> -->

                <a-menu-item key="promoStatus">
                    <BarChartOutlined />
                    <span>
                        <Link
                            :href="route('marketing.promostatus.promo.status')"
                            >Promo Status</Link
                        >
                    </span>
                </a-menu-item>
                <a-menu-item key="manageSupplier">
                    <UsergroupAddOutlined />
                    <span>
                        <Link
                            :href="
                                route(
                                    'marketing.manage-supplier.manage.supplier'
                                )
                            "
                            >Manage Supplier</Link
                        >
                    </span>
                </a-menu-item>
                <a-sub-menu key="sales">
                    <template #title>
                        <span>
                            <DollarOutlined />
                            <span>Sales</span>
                        </span>
                    </template>
                    <a-menu-item key="treasurySales">
                        <Link
                            :href="
                                route(
                                    'marketing.treasurysales.sales.treasury.sales'
                                )
                            "
                        >
                            Treasury Sales</Link
                        >
                    </a-menu-item>
                    <a-menu-item key="storeSales">
                        <Link :href="route('sales.store.sales')">
                            Store Sales</Link
                        >
                    </a-menu-item>
                </a-sub-menu>
                <a-sub-menu key="verifiedGcPerStore">
                    <template #title>
                        <span>
                            <DollarOutlined />
                            <span>Verified GC Per Store</span>
                        </span>
                    </template>
                    <a-menu-item key="amall">
                        <Link :href="route('marketing.verifiedgc.alturas.mall')"
                            >Alturas Mall</Link
                        >
                    </a-menu-item>
                    <a-menu-item key="atalibon">
                        <Link
                            :href="
                                route('marketing.verifiedgc.alturas.talibon')
                            "
                            >Alturas Talibon</Link
                        >
                    </a-menu-item>
                    <a-menu-item key="icm">
                        <Link
                            :href="
                                route('marketing.verifiedgc.island.city.mall')
                            "
                            >Island City Mall</Link
                        >
                    </a-menu-item>
                    <a-menu-item key="pm">
                        <Link
                            :href="route('marketing.verifiedgc.plaza.marcela')"
                            >Plaza Marcela</Link
                        >
                    </a-menu-item>
                    <a-menu-item key="atubigon">
                        <Link
                            :href="
                                route('marketing.verifiedgc.alturas.tubigon')
                            "
                            >Alturas Tubigon</Link
                        >
                    </a-menu-item>
                    <a-menu-item key="cc">
                        <Link
                            :href="route('marketing.verifiedgc.colonade.colon')"
                            >Colonade Colon</Link
                        >
                    </a-menu-item>
                    <a-menu-item key="cm">
                        <Link
                            :href="
                                route('marketing.verifiedgc.colonade.mandaue')
                            "
                            >Colonade Mandaue</Link
                        >
                    </a-menu-item>
                    <a-menu-item key="ac">
                        <Link :href="route('marketing.verifiedgc.alta.cita')"
                            >Alta Citta</Link
                        >
                    </a-menu-item>
                    <a-menu-item key="fm">
                        <Link
                            :href="route('marketing.verifiedgc.farmers.market')"
                            >Farmers Market</Link
                        >
                    </a-menu-item>
                    <a-menu-item key="udc">
                        <Link :href="route('marketing.verifiedgc.udc')"
                            >Ubay Distribution Center</Link
                        >
                    </a-menu-item>
                    <a-menu-item key="screenville">
                        <Link :href="route('marketing.verifiedgc.screenville')"
                            >Screenville</Link
                        >
                    </a-menu-item>
                    <a-menu-item key="asct">
                        <Link :href="route('marketing.verifiedgc.asctech')"
                            >Asc Tech</Link
                        >
                    </a-menu-item>
                </a-sub-menu>
            </a-menu>
        </a-sub-menu>

        <a-menu
            v-else
            :openKeys="currentActiveMenu"
            :selectedKeys="highlightRoute"
            theme="dark"
            mode="inline"
            @click="handleClick"
        >
            <a-menu-item key="marketing.dashboard">
                <QrcodeOutlined />
                <span>
                    <Link :href="route('marketing.dashboard')">
                        {{
                            page.auth.user.usertype == "1"
                                ? "MarketingDashboard"
                                : "Dashboard"
                        }}</Link
                    >
                </span>
            </a-menu-item>
            <a-menu-item key="marketing.promo.gc.request">
                <QrcodeOutlined />
                <span>
                    <Link :href="route('marketing.promo.gc.request')"> </Link
                    >Promo GC Request
                </span>
            </a-menu-item>
            <a-sub-menu key="addPromo">
                <template #title>
                    <span>
                        <FundProjectionScreenOutlined />
                        <span>Promo</span>
                    </span>
                </template>
                <a-menu-item key="marketing.addPromo.add">
                    <Link :href="route('marketing.addPromo.add')"
                        >Add New Promo</Link
                    >
                </a-menu-item>
                <a-menu-item key="marketing.addPromo.list">
                    <Link :href="route('marketing.addPromo.list')"
                        >Promo List</Link
                    >
                </a-menu-item>
            </a-sub-menu>

            <a-menu-item key="releasedPromoGC">
                <template #icon>
                    <RetweetOutlined />
                </template>
                <span>
                    <Link :href="route('marketing.releaseGc.releasegc')"
                        >Released Promo GC</Link
                    >
                </span>
            </a-menu-item>

            <!-- <a-menu-item key="releasedPromoGC">
        <RetweetOutlined />
        <span><Link :href="route('release.promo.gc')">Released Promo GC</Link></span>
    </a-menu-item> -->

            <a-menu-item key="promoStatus">
                <BarChartOutlined />
                <span>
                    <Link :href="route('marketing.promostatus.promo.status')"
                        >Promo Status</Link
                    >
                </span>
            </a-menu-item>
            <a-menu-item key="manageSupplier">
                <UsergroupAddOutlined />
                <span>
                    <Link
                        :href="
                            route('marketing.manage-supplier.manage.supplier')
                        "
                        >Manage Supplier</Link
                    >
                </span>
            </a-menu-item>
            <a-sub-menu key="sales">
                <template #title>
                    <span>
                        <DollarOutlined />
                        <span>Sales</span>
                    </span>
                </template>
                <a-menu-item key="treasurySales">
                    <Link
                        :href="
                            route(
                                'marketing.treasurysales.sales.treasury.sales'
                            )
                        "
                    >
                        Treasury Sales</Link
                    >
                </a-menu-item>
                <a-menu-item key="storeSales">
                    <Link :href="route('sales.store.sales')"> Store Sales</Link>
                </a-menu-item>
            </a-sub-menu>
            <a-sub-menu key="verifiedGcPerStore">
                <template #title>
                    <span>
                        <DollarOutlined />
                        <span>Verified GC Per Store</span>
                    </span>
                </template>
                <a-menu-item key="amall">
                    <Link :href="route('marketing.verifiedgc.alturas.mall')"
                        >Alturas Mall</Link
                    >
                </a-menu-item>
                <a-menu-item key="atalibon">
                    <Link :href="route('marketing.verifiedgc.alturas.talibon')"
                        >Alturas Talibon</Link
                    >
                </a-menu-item>
                <a-menu-item key="icm">
                    <Link :href="route('marketing.verifiedgc.island.city.mall')"
                        >Island City Mall</Link
                    >
                </a-menu-item>
                <a-menu-item key="pm">
                    <Link :href="route('marketing.verifiedgc.plaza.marcela')"
                        >Plaza Marcela</Link
                    >
                </a-menu-item>
                <a-menu-item key="atubigon">
                    <Link :href="route('marketing.verifiedgc.alturas.tubigon')"
                        >Alturas Tubigon</Link
                    >
                </a-menu-item>
                <a-menu-item key="cc">
                    <Link :href="route('marketing.verifiedgc.colonade.colon')"
                        >Colonade Colon</Link
                    >
                </a-menu-item>
                <a-menu-item key="cm">
                    <Link :href="route('marketing.verifiedgc.colonade.mandaue')"
                        >Colonade Mandaue</Link
                    >
                </a-menu-item>
                <a-menu-item key="ac">
                    <Link :href="route('marketing.verifiedgc.alta.cita')"
                        >Alta Citta</Link
                    >
                </a-menu-item>
                <a-menu-item key="fm">
                    <Link :href="route('marketing.verifiedgc.farmers.market')"
                        >Farmers Market</Link
                    >
                </a-menu-item>
                <a-menu-item key="udc">
                    <Link :href="route('marketing.verifiedgc.udc')"
                        >Ubay Distribution Center</Link
                    >
                </a-menu-item>
                <a-menu-item key="screenville">
                    <Link :href="route('marketing.verifiedgc.screenville')"
                        >Screenville</Link
                    >
                </a-menu-item>
                <a-menu-item key="asct">
                    <Link :href="route('marketing.verifiedgc.asctech')"
                        >Asc Tech</Link
                    >
                </a-menu-item>
            </a-sub-menu>
        </a-menu>
    </div>
</template>
