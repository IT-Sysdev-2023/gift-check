<script setup lang="ts">
import { PageWithSharedProps } from "@/types";
import {
    FileOutlined,
    SwapOutlined,
    DollarOutlined,
    LineChartOutlined,
    BarcodeOutlined
} from "@ant-design/icons-vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage<PageWithSharedProps>().props;
</script>

<template>
    <a-sub-menu v-if="page.auth.user.usertype  === '1'" key="retail-side-bar">
        <template #title>
            <span>
                <SwapOutlined />
                <span>Retail Sidebar</span>
            </span>
        </template>
        <a-menu-item key="retail.dashboard">
            <BarcodeOutlined />
            <span>
                <Link :href="route('retail.dashboard')">{{ page.auth.user.usertype == '1' ? 'Retail Dashboard' :
                    'Dashboard' }}</Link>
            </span>
        </a-menu-item>
        <a-sub-menu key="transactions">
            <template #title>
                <span>
                    <DollarOutlined />
                    <span>Transactions</span>
                </span>
            </template>
            <!-- <a-menu-item key="BeamAndGoConversion">Beam And Go Conversion</a-menu-item> -->
            <a-menu-item @click="() => $inertia.get(route('retailstore.retailstore.gc.request'))" key="GCRequest">GC Request</a-menu-item>
            <a-menu-item @click="() => $inertia.get(route('retial.'))" key="GCVerification">GC Verification</a-menu-item>
            <a-menu-item key="GCTransfer">GC Transfer</a-menu-item>
            <a-menu-item key="StoreEOD" @click="()=>$inertia.get(route('retail.storeEod'))">Store EOD</a-menu-item>
            <a-menu-item key="LostGC" @click="()=> $inertia.get(route('retail.lostGc'))">Lost GC</a-menu-item>
            <a-menu-item key="SupplierGC">Supplier GC</a-menu-item>
        </a-sub-menu>

        <a-sub-menu key="sales">
            <template #title>
                <span>
                    <SwapOutlined />
                    <span>Sales</span>
                </span>
            </template>
            <a-menu-item key="cashSales">Cash Sales</a-menu-item>
            <a-menu-item key="cardSales">Card Sales</a-menu-item>
            <a-menu-item key="AR">AR (Customer)</a-menu-item>
            <a-menu-item key="report">Report</a-menu-item>
        </a-sub-menu>
        <a-sub-menu key="MasterFile">
            <template #title>
                <span>
                    <FileOutlined />
                    <span>Masterfile</span>
                </span>
            </template>
            <a-menu-item key="customerSetuo">Customer Setup</a-menu-item>
            <a-menu-item key="sgcCompanySetup">SGC Company Setup</a-menu-item>
            <a-menu-item key="sgcItemSetup">SGC Item Setup</a-menu-item>
        </a-sub-menu>
        <a-menu-item key="storeLedger">
            <BarcodeOutlined />
            <span>Store Ledger</span>
        </a-menu-item>
        <a-menu-item key="verifiedGc" @click="()=> $inertia.get(route('retail.verified-gc.list'))">
            <BarcodeOutlined />
            <span>Verified GC</span>
        </a-menu-item>
        <a-menu-item key="soldgc" @click="()=> $inertia.get(route('retail.soldGc'))">
            <BarcodeOutlined />
            <span>Sold GC</span>
        </a-menu-item>
        <a-sub-menu key="reports">
            <template #title>
                <span>
                    <LineChartOutlined />
                    <span>Reports</span>
                </span>
            </template>
            <a-menu-item key="verifiedGCReports">Verified GC Reports</a-menu-item>
            <a-menu-item key="beamAndGoGoReport">Beam and Go GC Report</a-menu-item>
        </a-sub-menu>
    </a-sub-menu>
    <div v-else>
        <a-menu-item key="retail.dashboard">
            <BarcodeOutlined />
            <span>
                <Link :href="route('retail.dashboard')">{{ page.auth.user.usertype == '1' ? 'Retail Dashboard' :
                    'Dashboard' }}</Link>
            </span>
        </a-menu-item>
        <a-sub-menu key="transactions">
            <template #title>
                <span>
                    <DollarOutlined />
                    <span>Transactions</span>
                </span>
            </template>
            <!-- <a-menu-item key="BeamAndGoConversion">Beam And Go Conversion</a-menu-item> -->
            <a-menu-item @click="() => $inertia.get(route('retail.gc.request'))" key="GCRequest">GC Request</a-menu-item>
            <a-menu-item @click="() => $inertia.get(route('retail.verification.index'))" key="GCVerification">GC Verification</a-menu-item>
            <a-menu-item key="GCTransfer">GC Transfer</a-menu-item>
            <a-menu-item key="StoreEOD" @click="()=>$inertia.get(route('retail.storeEod'))">Store EOD</a-menu-item>
            <a-menu-item key="LostGC" @click="()=> $inertia.get(route('retail.lostGc'))">Lost GC</a-menu-item>
            <a-menu-item key="SupplierGC">Supplier GC</a-menu-item>
        </a-sub-menu>

        <a-sub-menu key="sales">
            <template #title>
                <span>
                    <SwapOutlined />
                    <span>Sales</span>
                </span>
            </template>
            <a-menu-item key="cashSales">Cash Sales</a-menu-item>
            <a-menu-item key="cardSales">Card Sales</a-menu-item>
            <a-menu-item key="AR">AR (Customer)</a-menu-item>
            <a-menu-item key="report">Report</a-menu-item>
        </a-sub-menu>
        <a-sub-menu key="MasterFile">
            <template #title>
                <span>
                    <FileOutlined />
                    <span>Masterfile</span>
                </span>
            </template>
            <a-menu-item key="customerSetuo">Customer Setup</a-menu-item>
            <a-menu-item key="sgcCompanySetup">SGC Company Setup</a-menu-item>
            <a-menu-item key="sgcItemSetup">SGC Item Setup</a-menu-item>
        </a-sub-menu>
        <a-menu-item key="storeLedger">
            <BarcodeOutlined />
            <span>Store Ledger</span>
        </a-menu-item>
        <a-menu-item key="verifiedGc" @click="()=> $inertia.get(route('retail.verified-gc.list'))">
            <BarcodeOutlined />
            <span>Verified GC</span>
        </a-menu-item>
        <a-menu-item key="soldgc"  @click="()=> $inertia.get(route('retail.soldGc'))">
            <BarcodeOutlined />
            <span>Sold GC</span>
        </a-menu-item>
        <a-sub-menu key="reports">
            <template #title>
                <span>
                    <LineChartOutlined />
                    <span>Reports</span>
                </span>
            </template>
            <a-menu-item key="verifiedGCReports">Verified GC Reports</a-menu-item>
            <a-menu-item key="beamAndGoGoReport">Beam and Go GC Report</a-menu-item>
        </a-sub-menu>
    </div>

</template>
