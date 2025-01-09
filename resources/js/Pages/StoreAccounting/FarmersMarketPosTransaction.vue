<template>
    <a-card>
        <a-button
            @click="
                () =>
                    $inertia.get(
                        route('storeaccounting.farmersMarket', { id: 9 }),
                    )
            "
        ><RollbackOutlined />Back</a-button>
        <span
            style="
                font-weight: bold;
                font-family: sans-serif;
                font-size: 1rem;
                margin-left: 50px;
            "
        >
            GC Barcode #{{ barcodeNumber }} POS Transaction
        </span>

        <div style="font-weight: bold; margin-left: 70%; margin-top: 10px">
            <a-input-search
                allow-clear
                v-model:value="alturasSearch"
                style="width: 90%"
                enter-button
            />
        </div>

        <div style="margin-top: 10px">
            <a-table
                :data-source="data.data"
                :columns="alturasPosTransaction"
                :pagination="false"
                size="small"
            >
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>
    </a-card>

    <!-- {{ data }} -->
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { notification } from "ant-design-vue";
export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        barcodeNumber: Number,
        data: Object,
    },
    data() {
        return {
            alturasSearch: "",
            alturasPosTransaction: [
                {
                    title: "Textfile Line",
                    dataIndex: "seodtt_line",
                },
                {
                    title: "Credit Limit",
                    dataIndex: "seodtt_creditlimit",
                },
                {
                    title: "Cred. Pur. Amt + Add-on",
                    dataIndex: "seodtt_credpuramt",
                },
                {
                    title: "Add-on Amt",
                    dataIndex: "seodtt_addonamt",
                },
                {
                    title: "Remaining Balance",
                    dataIndex: "seodtt_balance",
                },
                {
                    title: "Transaction #",
                    dataIndex: "seodtt_transno",
                },
                {
                    title: "Time of Cred Tranx",
                    dataIndex: "seodtt_timetrnx",
                },
                {
                    title: "Bus. Unit",
                    dataIndex: "seodtt_bu",
                },
                {
                    title: "Terminal #",
                    dataIndex: "seodtt_terminalno",
                },
                {
                    title: "Ackslip #",
                    dataIndex: "seodtt_ackslipno",
                },
            ],
        };
    },
    watch: {
        alturasSearch(search) {
            const searchValidation =
                /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}]/u;
            if (searchValidation.test(search)) {
                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: "Invalid input",
                        description: "Search contains invalid symbol or emojis",
                        placement: "topRight",
                    });
                };
                openNotificationWithIcon("warning");
                return;
            }
            this.$inertia.get(
                route(
                    "storeaccounting.farmersMarketPosTransaction",
                    this.barcodeNumber,
                ),
                {
                    search: search,
                },
                {
                    preserveState: true,
                },
            );
        },
    },
};
</script>
