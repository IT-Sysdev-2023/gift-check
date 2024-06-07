<script setup lang="ts">
import {
    FundProjectionScreenOutlined,
    QrcodeOutlined,
    RetweetOutlined,
    BarChartOutlined,
    UsergroupAddOutlined,
    DollarOutlined
} from "@ant-design/icons-vue";
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <a-menu-item key="barcodeChecker">
        <QrcodeOutlined />
        <span>
            <Link :href="route('promo.gc.request')">
            </Link>Promo GC Request
        </span>
    </a-menu-item>
    <a-sub-menu key="promo">
        <template #title>
            <span>
                <FundProjectionScreenOutlined />
                <span>Promo</span>
            </span>
        </template>
        <a-menu-item key="addnewpromo">
            <Link :href="route('add.new.promo')">Add New Promo</Link>
        </a-menu-item>
        <a-menu-item key="promolist">
            <Link :href="route('promo.list')">Promo List</Link>
        </a-menu-item>
    </a-sub-menu>

    <a-menu-item key="releasedPromoGC">
        <template #icon>
            <RetweetOutlined />
        </template>
        <span>
            <Link :href="route('released.promo.gc')">Released Promo GC</Link>
        </span>
    </a-menu-item>


    <!-- <a-menu-item key="releasedPromoGC">
        <RetweetOutlined />
        <span><Link :href="route('release.promo.gc')">Released Promo GC</Link></span>
    </a-menu-item> -->

    <a-menu-item key="promoStatus">
        <BarChartOutlined />
        <span>
            <Link :href="route('promo.status')">Promo Status</Link>
        </span>
    </a-menu-item>
    <a-menu-item key="manageSupplier">
        <UsergroupAddOutlined />
        <span>
            <Link :href="route('manage.supplier')">Manage Supplier</Link>
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
            <Link :href="route('sales.treasury.sales')"> Treasury Sales</Link>
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
            <Link :href="route('verified.gc.alturas.mall')">Alturas Mall</Link>
        </a-menu-item>
        <a-menu-item key="atalibon">
            <Link :href="route('verified.gc.alturas.talibon')">Alturas Talibon</Link>
        </a-menu-item>
        <a-menu-item key="icm">
            <Link :href="route('verified.gc.island.city.mall')">Island City Mall</Link>
        </a-menu-item>
        <a-menu-item key="pm">
            <Link :href="route('verified.gc.plaza.marcela')">Plaza Marcela</Link>
        </a-menu-item>
        <a-menu-item key="atubigon">
            <Link :href="route('verified.gc.alturas.tubigon')">Alturas Tubigon</Link>
        </a-menu-item>
        <a-menu-item key="cc">
            <Link :href="route('verified.gc.colonade.colon')">Colonade Colon</Link>
        </a-menu-item>
        <a-menu-item key="cm">
            <Link :href="route('verified.gc.colonade.mandaue')">Colonade Mandaue</Link>
        </a-menu-item>
        <a-menu-item key="ac">
            <Link :href="route('verified.gc.alta.cita')">Alta Citta</Link>
        </a-menu-item>
        <a-menu-item key="fm">
            <Link :href="route('verified.gc.farmers.market')">Farmers Market</Link>
        </a-menu-item>
        <a-menu-item key="udc">
            <Link :href="route('verified.gc.udc')">Ubay Distribution Center</Link>
        </a-menu-item>
        <a-menu-item key="screenville">
            <Link :href="route('verified.gc.screenville')">Screenville</Link>
        </a-menu-item>
        <a-menu-item key="asct">
            <Link :href="route('verified.gc.asctech')">Asc Tech</Link>
        </a-menu-item>
    </a-sub-menu>

</template>
