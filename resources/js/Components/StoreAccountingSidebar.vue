<template>
    <a-menu-item key="retailgroup.dashboard">
        <DashboardOutlined />
        <span>
            <Link :href="route('storeaccounting.dashboard')">
            {{
                page.auth.user.usertype == "13"
                    ? "Dashboard"
                    : "Store Accounting Dashboard"
            }}</Link>
        </span>
    </a-menu-item>
    <a-sub-menu key="sales">
        <template #title>
            <span>
                <SwapOutlined />
                <span>Sales</span>
            </span>
        </template>

        <a-menu-item @click="() => router.get(route('storeaccounting.sales'))" key="treasurySales" class="menu-item">
            <MoneyCollectOutlined />
            <span>Treasury Sales</span>
        </a-menu-item>

        <a-menu-item @click="() => router.get(route('storeaccounting.store'))" key="storeSales" class="menu-item">
            <AppstoreAddOutlined />
            <span>Store Sales</span>
        </a-menu-item>
    </a-sub-menu>

    <a-sub-menu key="storeAccounting.verifiedGCPerStore">
        <template #title>
            <span>
                <AppstoreOutlined />
                <span>Verified GC Per Store</span>
            </span>
        </template>

        <a-menu-item v-for="store in stores" :key="store.routeName" @click="
            () => router.get(route(store.routeName, { id: store.id }))
        " class="menu-item">
            <ShoppingOutlined />
            <span>
                {{ store.name }}
            </span>
        </a-menu-item>
    </a-sub-menu>
    <a-sub-menu key="storeAccounting.transaction">
        <template #title>
            <span>
                <AppstoreOutlined />
                <span>DTI Payment SPGC</span>
            </span>
        </template>
        <a-menu-item class="menu-item" @click="
            () => router.get(route('accounting.payment.payment.gc.dti'))
        " key="releasing.dti">
            <DatabaseOutlined />
            <span style="font-size: small">DTI SGC Payment</span>
        </a-menu-item>
        <a-menu-item class="menu-item" @click="
            () => router.get(route('accounting.payment.dti.viewing'))
        " key="releasing.dti.viewing">
            <DatabaseOutlined />
            <span style="font-size: small">DTI Payment Viewing</span>
        </a-menu-item>
    </a-sub-menu>

    <a-sub-menu key="storeAccounting.exportData">
        <template #title>
            <span>
                <ExportOutlined />
                <span>Export Data</span>
            </span>
        </template>
        <a-menu-item class="menu-item" @click="
            () =>
                router.get(
                    route('storeaccounting.storeGCPurchasedReport'),
                )
        " key="storeGCPurchased">
            <DatabaseOutlined />
            <span style="font-size: x-small"> Store GC Purchased Report </span>
        </a-menu-item>
        <a-menu-item class="menu-item" @click="() => router.get(route('storeaccounting.redeemReport'))"
            key="redeemReport">
            <DatabaseOutlined />
            <span style="font-size: small"> SPGC Redeem Report </span>
        </a-menu-item>
        <a-menu-item class="menu-item" @click="() => router.get(route('storeaccounting.verifiedStore'))"
            key="verifiedStore">
            <DatabaseOutlined />
            <span style="font-size: small"> Verified Store Purchased </span>
        </a-menu-item>
    </a-sub-menu>

    <a-sub-menu key="storeAccounting.reports">
        <template #title>
            <span>
                <ContainerOutlined />
                <span>Reports </span>
            </span>
        </template>
        <a-menu-item class="menu-item" @click="() => router.get(route('storeaccounting.SPGCApproved'))"
            key="SPGCApproved">
            <LikeOutlined />
            <span> SPGC Approved </span>
        </a-menu-item>
        <a-menu-item class="menu-item" @click="() => router.get(route('storeaccounting.SPGCRelease'))"
            key="SPGCRelease">
            <ExportOutlined />
            <span> SPGC Release </span>
        </a-menu-item>
        <a-menu-item class="menu-item" @click="
            () => router.get(route('storeaccounting.DuplicatedBarcodes'))
        " key="DuplicatedBarcodes">
            <TagsOutlined />
            <span> Duplicate Barcodes </span>
        </a-menu-item>
        <a-menu-item class="menu-item" @click="() => router.get(route('storeaccounting.CheckVariance'))"
            key="CheckVariance">
            <DatabaseOutlined />
            <span> Check Variance </span>
        </a-menu-item>
    </a-sub-menu>
    <a-menu-item key="generatedReports">
        <DashboardOutlined />
        <span>
            <Link :href="route('storeaccounting.reports.generatedReports')">
            Generated Reports</Link>
        </span>
    </a-menu-item>
    <a-menu-item>
        <MoneyCollectOutlined />
        <span>
            <Link :href="route('storeaccounting.billing_reports')">
            Billing
            </Link>
        </span>
    </a-menu-item>
</template>
<script setup lang="ts">
import { PageWithSharedProps } from "@/types";
import { usePage, router } from "@inertiajs/vue3";

const page = usePage<PageWithSharedProps>().props;

const stores = [
    { name: "Alturas Mall", routeName: "storeaccounting.alturasMall", id: 1 },
    {
        name: "Alturas Talibon",
        routeName: "storeaccounting.alturasTalibon",
        id: 2,
    },
    {
        name: "Island City Mall",
        routeName: "storeaccounting.islandCityMall",
        id: 3,
    },
    { name: "Plaza Marcela", routeName: "storeaccounting.plazaMarcela", id: 4 },
    {
        name: "Alturas Tubigon",
        routeName: "storeaccounting.alturasTubigon",
        id: 5,
    },
    {
        name: "Colonade Colon",
        routeName: "storeaccounting.colonadeColon",
        id: 6,
    },
    {
        name: "Colonade Mandaue",
        routeName: "storeaccounting.colonadeMandaue",
        id: 7,
    },
    { name: "Alta Citta", routeName: "storeaccounting.altaCitta", id: 8 },
    {
        name: "Farmers Market",
        routeName: "storeaccounting.farmersMarket",
        id: 9,
    },
    {
        name: "Ubay Distribution",
        routeName: "storeaccounting.ubayDistribution",
        id: 10,
    },
    { name: "Screenville", routeName: "storeaccounting.screenville", id: 11 },
    { name: "Asc Tech", routeName: "storeaccounting.ascTech", id: 12 },
];
</script>
<style>
.menu-item {
    display: flex;
    align-items: center;
}

.menu-item:hover {
    transform: scale(1.2);
    color: white;
    font-weight: bolder;
}
</style>
