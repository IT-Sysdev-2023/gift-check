<template>
    <a-menu-item key="retailgroup.dashboard">
        <DashboardOutlined />
        <span>
            <Link :href="route('storeaccounting.dashboard')">
            {{ page.auth.user.usertype == '13' ? 'Dashboard' : 'Store Accounting Dashboard' }}</Link>
        </span>
    </a-menu-item>
    <a-sub-menu key="sales">
        <template #title>
            <span>
                <SwapOutlined />
                <span>Sales</span>
            </span>
        </template>

        <a-menu-item @click="() => $inertia.get(route('storeaccounting.sales'))" key="treasurySales" class="menu-item">
            <MoneyCollectOutlined />
            <span>Treasury Sales</span>
        </a-menu-item>

        <a-menu-item @click="() => $inertia.get(route('storeaccounting.store'))" key="storeSales" class="menu-item">
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

        <a-menu-item @click="() => $inertia.get(route('storeaccounting.alturasMall', { id: 1 }))"
            key="storeaccounting.alturasMall" class="menu-item">
            <ShoppingOutlined />
            <span>
                Alturas Mall
            </span>
        </a-menu-item>
        <a-menu-item @click="() => $inertia.get(route('storeaccounting.alturasTalibon', { id: 2 }))"
            key="storeaccounting.alturasTalibon" class="menu-item">
            <ShoppingOutlined />
            <span>
                Alturas Talibon
            </span>
        </a-menu-item>
        <a-menu-item @click="() => $inertia.get(route('storeaccounting.islandCityMall', { id: 3 }))"
            key="storeaccounting.islandCityMall" class="menu-item">
            <ShoppingOutlined />
            <span>
                Island City Mall
            </span>
        </a-menu-item><a-menu-item @click="() => $inertia.get(route('storeaccounting.plazaMarcela', { id: 4 }))"
            key="storeaccounting.plazaMarcela" class="menu-item">
            <ShoppingOutlined />
            <span>
                Plaza Marcela
            </span>
        </a-menu-item><a-menu-item @click="() => $inertia.get(route('storeaccounting.alturasTubigon', { id: 5 }))"
            key="storeaccounting.alturasTubigon" class="menu-item">
            <ShoppingOutlined />
            <span>
                Alturas Tubigon
            </span>
        </a-menu-item><a-menu-item @click="() => $inertia.get(route('storeaccounting.colonadeColon', { id: 6 }))"
            key="storeaccounting.colonadeColon" class="menu-item">
            <ShoppingOutlined />
            <span>
                Colonade Colon
            </span>
        </a-menu-item><a-menu-item @click="() => $inertia.get(route('storeaccounting.colonadeMandaue', { id: 7 }))"
            key="storeaccounting.colonadeMandaue" class="menu-item">
            <ShoppingOutlined />
            <span>
                Colonade Mandaue
            </span>
        </a-menu-item><a-menu-item @click="() => $inertia.get(route('storeaccounting.altaCitta', { id: 8 }))"
            key="storeaccounting.altaCitta" class="menu-item">
            <ShoppingOutlined />
            <span>
                Alta Citta
            </span>
        </a-menu-item><a-menu-item @click="() => $inertia.get(route('storeaccounting.farmersMarket', { id: 9 }))"
            key="storeaccounting.farmersMarket" class="menu-item">
            <ShoppingOutlined />
            <span>
                Farmers Market
            </span>
        </a-menu-item><a-menu-item @click="() => $inertia.get(route('storeaccounting.ubayDistribution', { id: 10 }))"
            key="storeaccounting.ubayDistribution" class="menu-item">
            <ShoppingOutlined />
            <span style="font-size: small;">
                Ubay Distribution Center
            </span>
        </a-menu-item><a-menu-item @click="() => $inertia.get(route('storeaccounting.screenville', { id: 11 }))"
            key="storeaccounting.screenville" class="menu-item">
            <ShoppingOutlined />
            <span>
                Screenville
            </span>
        </a-menu-item><a-menu-item @click="() => $inertia.get(route('storeaccounting.ascTech', { id: 12 }))"
            key="storeaccounting.ascTech" class="menu-item">
            <ShoppingOutlined />
            <span>
                ASC Tech
            </span>
        </a-menu-item>
    </a-sub-menu>

    <!-- <a-sub-menu key="storeAccounting.promo">
        <template #title>
            <span>
                <AreaChartOutlined />
                <span>
                    Promo
                </span>
            </span>
        </template>
        <a-menu-item class="menu-item">
            <BarChartOutlined />
            <span>
                Promo List
            </span>
        </a-menu-item>
        <a-menu-item class="menu-item">
            <BarChartOutlined />
            <span>
                Promo GC Status
            </span>
        </a-menu-item>
    </a-sub-menu> -->

    <a-sub-menu key="storeAccounting.exportData">
        <template #title>
            <span>
                <ExportOutlined />
                <span>Export Data</span>
            </span>
        </template>
        <a-menu-item class="menu-item" @click="() => $inertia.get(route('storeaccounting.verifiedGCReport'))"
            key="verifiedGCReport">
            <DatabaseOutlined />
            <span style="font-size: small;">
                Verified GC Report
            </span>
        </a-menu-item>
        <a-menu-item class="menu-item" @click="() => $inertia.get(route('storeaccounting.storeGCPurchasedReport'))"
            key="storeGCPurchased">
            <DatabaseOutlined />
            <span style="font-size: x-small;">
                Store GC Purchased Report
            </span>
        </a-menu-item>
        <a-menu-item class="menu-item" @click="() => $inertia.get(route('storeaccounting.redeemReport'))"
            key="redeemReport">
            <DatabaseOutlined />
            <span style="font-size: small;">
                SPGC Redeem Report
            </span>
        </a-menu-item>
        <a-menu-item class="menu-item" @click="() => $inertia.get(route('storeaccounting.verifiedStore'))"
            key="verifiedStore">
            <DatabaseOutlined />
            <span style="font-size: small;">
                Verified Store Purchased
            </span>
        </a-menu-item>
    </a-sub-menu>

    <a-sub-menu key="storeAccounting.reports">
        <template #title>
            <span>
                <ContainerOutlined />
                <span>Reports </span>
            </span>
        </template>
        <a-menu-item class="menu-item" @click="() => $inertia.get(route('storeaccounting.SPGCApproved'))"
            key="SPGCApproved">
            <LikeOutlined />
            <span>
                SPGC Approved
            </span>
        </a-menu-item>
        <a-menu-item class="menu-item" @click="() => $inertia.get(route('storeaccounting.SPGCRelease'))"
            key="SPGCRelease">
            <ExportOutlined />
            <span>
                SPGC Release
            </span>
        </a-menu-item>
        <a-menu-item class="menu-item" @click="() => $inertia.get(route('storeaccounting.DuplicatedBarcodes'))"
            key="DuplicatedBarcodes">
            <TagsOutlined />
            <span>
                Duplicate Barcodes
            </span>
        </a-menu-item>
        <a-menu-item class="menu-item" @click="() => $inertia.get(route('storeaccounting.CheckVariance'))"
            key="CheckVariance">
            <DatabaseOutlined />
            <span>
                Check Variance
            </span>
        </a-menu-item>
    </a-sub-menu>

</template>
<script setup lang="ts">
import { PageWithSharedProps } from "@/types";
import { usePage } from "@inertiajs/vue3";

const page = usePage<PageWithSharedProps>().props;


const stores = [
    { name: "Alturas Mall", routeName: "storeaccounting.alturasMall", id: 1 },
    { name: "Alturas Talibon", routeName: "storeaccounting.alturasTalibon", id: 2 },
    { name: "Island City Mall", routeName: "storeaccounting.islandCityMall", id: 3 },
    { name: "Plaza Marcela", routeName: "storeaccounting.plazaMarcela", id: 4 },
    { name: "Alturas Tubigon", routeName: "storeaccounting.alturasTubigon", id: 5 },
    { name: "Colonade Colon", routeName: "storeaccounting.colonadeColon", id: 6 },
    { name: "Colonade Mandaue", routeName: "storeaccounting.colonadeMandaue", id: 7 },
    { name: "Alta Citta", routeName: "storeaccounting.altaCitta", id: 8 },
    { name: "Farmers Market", routeName: "storeaccounting.farmersMarket", id: 9 },
    { name: "Ubay Distribution", routeName: "storeaccounting.ubayDistribution", id: 10 },
    { name: "Screenville", routeName: "storeaccounting.screenville", id: 11 },
    { name: "Asc Tech", routeName: "storeaccounting.ascTech", id: 12 }
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