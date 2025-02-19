<template>
    <a-card>
        <a-button @click="backToDashboard" style="font-weight: bold;">
            <RollbackOutlined /> Back
        </a-button>
        <span style="font-family: sans-serif; font-size: 1rem; margin-left: 50px">
            <span style="font-weight: bold">Customer:</span>
            {{ salesCustomer }}
        </span>

        <div style="font-weight: bold; margin-left: 70%; margin-top: 10px">
            <a-input-search allow-clear v-model:value="salesSearchBox" placeholder="Input search here!" enter-button
                style="width: 90%" />
        </div>

        <div style="margin-top: 10px">
            <a-table :data-source="data.data" :columns="viewSalesColumns" :pagination="false" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'viewSales'">
                        <a-button title="search" @click="viewTreasurySales(record)" class="me-2 me-sm-5"
                            style="color: white; background-color: #1e90ff">
                            <SearchOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>
    </a-card>

    <a-modal v-model:open="salesViewModal" @ok="handleSalesView" style="width: 100%" :footer="false">
        <a-card>
            <div style="font-family: sans-serif; font-size: 1rem">
                <span style="font-weight: bold"> Post Transaction:</span>
                {{ selectedBarcode }}
            </div>

            <div style="margin-top: 20px">
                <a-table :data-source="POStransactionData.data" :columns="salesViewColumns" size="small"
                    :pagination="false" />
                <pagination :datarecords="POStransactionData" class="mt-5" />
            </div>
        </a-card>
    </a-modal>
    <!-- {{ data }} -->
    <!-- {{ salesCustomerID }} -->
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import axios from "axios";
import { notification } from "ant-design-vue";
export default {
    components: { Pagination },

    layout: AuthenticatedLayout,
    props: {
        salesCustomer: String,
        data: Array,
        search: String,
        salesCustomerID: String,
        viewSalesData: String,
    },
    data() {
        return {
            salesSearchBox: this.search,

            selectedBarcode: {},

            salesViewModal: false,

            POStransactionData: {},

            salesViewColumns: [
                {
                    title: "Textfile Line",
                    dataIndex: "seodtt_line",
                },
                {
                    title: "Credit Limit",
                    dataIndex: "seodtt_creditlimit",
                },
                {
                    title: "Cred.Pur.Amt + Add - on",
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
            viewSalesColumns: [
                {
                    title: "Barcode #",
                    dataIndex: "barcode",
                },
                {
                    title: "GC Type",
                    dataIndex: "type",
                },
                {
                    title: "Denomination",
                    dataIndex: "denomination",
                },
                {
                    title: "Date Verified",
                    dataIndex: "dateverify",
                },
                {
                    title: "Store Verified",
                    dataIndex: "store",
                },
                {
                    title: "Verified By",
                    dataIndex: "verifyby",
                },
                {
                    title: "Customer Name",
                    dataIndex: "customer",
                },
                {
                    title: "Balance",
                    dataIndex: "balanced",
                },
                {
                    title: "View",
                    dataIndex: "viewSales",
                },
            ],
        };
    },
    watch: {
        salesSearchBox(salesViewSearch) {
            const searchValidation =
                /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}]/u;
            if (searchValidation.test(salesViewSearch)) {
                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: "Invalid input",
                        description:
                            "Search contains invalid symbols or emojis",
                        placement: "topRight",
                    });
                };
                openNotificationWithIcon("warning");
                return;
            }

            this.$inertia.get(
                route(
                    "storeaccounting.storeAccountingViewSales",
                    this.salesCustomerID,
                ),
                {
                    search: salesViewSearch,
                },
                {
                    preserveState: true,
                },
            );
        },
    },
    methods: {
        async viewTreasurySales(rec) {
            try {
                const response = await axios.get(
                    route(
                        "storeaccounting.storeAccountingPOStransaction",
                        rec.barcode,
                    ),
                );
                this.POStransactionData = response.data;
                this.selectedBarcode = rec.barcode;
                this.salesViewModal = true;
            } catch (error) {
                console.error("Error fetching sales transaction data:", error);
                this.errorMessage = "Failed to fetch sales transaction data.";
            }
        },
        handleSalesView() {
            this.salesViewModal = false;
        },
        backToDashboard() {
            this.$inertia.get(route("storeaccounting.sales"));
        },
    },
};
</script>
<style scoped>
.input-wrapper input {
    background-color: whitesmoke;
    border: none;
    padding: 1rem;
    font-size: 1rem;
    width: 16em;
    border-radius: 2rem;
    color: black;
    box-shadow: 0 0.4rem #1e90ff;
    cursor: pointer;
    margin-top: 10px;
    margin-left: 70%;
}

.input-wrapper input:focus {
    outline-color: whitesmoke;
}
</style>
