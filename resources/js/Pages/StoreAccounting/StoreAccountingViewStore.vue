<template>
    <a-card>
        <a-button @click="backToDashboard" style="font-weight: bold">
            <RollbackOutlined /> Back
        </a-button>
        <span style="font-family: sans-serif; font-size: 1rem; margin-left: 50px">
            <span style="font-weight: bold">Barcode #</span>
            {{ selectecTransNumber }}
        </span>

        <div style="margin-top: 10px; margin-left: 70%; font-weight: bold">
            <a-input-search allow-clear v-model:value="storeSearchBox" placeholder="Input search here!" enter-button
                style="width: 90%" />
        </div>

        <div style="margin-top: 10px">
            <a-table :data-source="viewStoreSalesData.data" :columns="viewStoreColumns" size="small"
                :pagination="false">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button @click="storeModalButton(record)" style="background-color: #1e90ff; color: white"
                            class="me-2 me-sm-5">
                            <SearchOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="viewStoreSalesData" class="mt-5" />
        </div>
    </a-card>

    <a-modal v-model:open="storeModal" style="width: 100%" @ok="storeOkButton" :footer="false">
        <a-card>
            <div style="font-family: sans-serif; font-size: 1rem">
                <span style="font-weight: bold">GC Barcode #</span>
                {{ salesBarcode }}
            </div>

            <div style="margin-top: 10px">
                <a-table :data-source="storeModalData.data" :columns="storeModalColumns" size="small"
                    :pagination="false">
                </a-table>
                <pagination :datarecords="storeModalData" class="mt-5" />
            </div>
        </a-card>
    </a-modal>
    <!-- {{ viewStoreSalesData }} -->
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { notification } from "ant-design-vue";
import axios from "axios";
export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        transnumber: String,
        viewStoreData: Object,
        viewStoreSalesData: Object,
        modalBarcode: Number,
        storeID: String,
        search: String,
    },
    data() {
        return {
            storeSearchBox: "",
            barcodeModal: this.modalBarcode,
            storeModalData: {},
            storeModal: false,
            salesBarcode: {},
            selectecTransNumber: this.transnumber,
            viewStoreColumns: [
                {
                    title: "Barcode",
                    dataIndex: "sales_barcode",
                },
                {
                    title: "Denomination",
                    dataIndex: "denomination",
                },
                {
                    title: "Store Verified",
                    dataIndex: "store_name",
                },
                {
                    title: "Date Verified",
                    dataIndex: "vs_date",
                },
                {
                    title: "Verified By",
                    dataIndex: "verby",
                },
                {
                    title: "Customer",
                    dataIndex: "customer",
                },
                {
                    title: "Balance",
                    dataIndex: "vs_tf_balance",
                },
                {
                    title: "View",
                    dataIndex: "view",
                },
            ],
            storeModalColumns: [
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
        storeSearchBox(search) {
            const searchValidation =
                /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}]/u;
            if (searchValidation.test(search)) {
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
                route("storeaccounting.storeAccountingViewStore", {
                    id: this.storeID,
                }),
                {
                    search: search,
                },
                {
                    preserveState: true,
                },
            );
        },
    },
    methods: {
        async storeModalButton(rec) {
            try {
                const { data } = await axios.get(
                    route("storeaccounting.storeAccountingViewModal", {
                        barcode: rec.sales_barcode,
                    }),
                );
                this.storeModal = true;
                this.salesBarcode = rec.sales_barcode;
                this.storeModalData = data;
            } catch (error) {
                console.error("Error fetching store transaction data", error);
                this.errorMessage = "Error fetching store transaction data";
            }
        },
        storeOkButton() {
            this.storeModal = false;
        },
        backToDashboard() {
            this.$inertia.get(route("storeaccounting.store"));
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
