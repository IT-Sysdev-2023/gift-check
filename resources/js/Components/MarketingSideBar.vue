<script setup lang="ts">
import {
    FundProjectionScreenOutlined,
    QrcodeOutlined,
    RetweetOutlined,
    BarChartOutlined,
    UsergroupAddOutlined,
    DollarOutlined
} from "@ant-design/icons-vue";

</script>

<template>
    <a-menu-item key="barcodeChecker">
        <QrcodeOutlined />
        <span>Promo GC Request</span>
    </a-menu-item>
    <a-sub-menu key="promo">
        <template #title>
            <span>
                <FundProjectionScreenOutlined />
                <span>Promo</span>
            </span>
        </template>
        <a-menu-item key="addNewPromo">Add New Promo</a-menu-item>
        <a-menu-item key="promoList">Promo List</a-menu-item>
    </a-sub-menu>
    <a-menu-item key="releasedPromoGC">
        <RetweetOutlined />
        <span><Link :href="route('release.promo.gc')">Released Promo GC</Link></span>
    </a-menu-item>
    <a-menu-item key="promoStatus">
        <BarChartOutlined />
        <span>Promo Status</span>
    </a-menu-item>
    <a-menu-item key="manageSupplier">
        <UsergroupAddOutlined />
        <span>Manage Supplier</span>
    </a-menu-item>
    <a-sub-menu key="sales">
        <template #title>
            <span>
                <DollarOutlined />
                <span>Sales</span>
            </span>
        </template>
        <a-menu-item key="treasurySales">Treasury Sales</a-menu-item>
        <a-menu-item key="storeSales">Store Sales</a-menu-item>
    </a-sub-menu>
    <a-sub-menu key="verifiedGcPerStore">
        <template #title>
            <span>
                <DollarOutlined />
                <span>Verified GC Per Store</span>
            </span>
        </template>
        <a-menu-item key="amall">Alturas Mall</a-menu-item>
        <a-menu-item key="atalibon">Alturas Talibon</a-menu-item>
        <a-menu-item key="icm">Island City Mall</a-menu-item>
        <a-menu-item key="pm">Plaza Marcela</a-menu-item>
        <a-menu-item key="atubigon">Alturas Tubigon</a-menu-item>
        <a-menu-item key="cc">Colonade Colon</a-menu-item>
        <a-menu-item key="cm">Colonade Mandaue</a-menu-item>
        <a-menu-item key="ac">Alta Citta</a-menu-item>
        <a-menu-item key="fm">Farmers Market</a-menu-item>
        <a-menu-item key="udc">Ubay Distribution Center</a-menu-item>
        <a-menu-item key="screenville">Screenville</a-menu-item>
        <a-menu-item key="asct">Asc Tech</a-menu-item>
    </a-sub-menu>

</template>
