<template>
    <a-card>
        <span style="margin-left: 45%; font-weight: bold; font-size: 15px">
            APPROVED GC REPORTS
        </span>
        <div>
            <a-card
                style="width: 85%; margin-left: 16%; border: 1px solid #dcdcdc"
            >
                <a-tabs>
                    <a-tab-pane key="1">
                        <template #tab>
                            <span style="font-weight: bold">
                                <FilePdfOutlined />
                                Pdf per Customer
                            </span>
                        </template>
                        <a-card>
                            <div v-if="isloading">
                                <div>
                                    <div id="page">
                                        <div id="container">
                                            <div id="ring"></div>
                                            <div id="ring"></div>
                                            <div id="ring"></div>
                                            <div id="ring"></div>
                                            <div
                                                style="font-weight: bold"
                                                id="h3"
                                            >
                                                Generating PDF please wait...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="input-wrapper">
                                <input
                                    type="search"
                                    placeholder="Input search here..."
                                    name="text"
                                    class="input"
                                    v-model="pdfPerCustomerSearch"
                                />
                            </div>

                            <div style="margin-top: 10px; margin-left: 30px">
                                <span style="color: red; font-style: oblique">
                                    <span v-if="message">
                                        <WarningOutlined />
                                    </span>
                                    {{ this.message }}
                                </span>
                            </div>
                            <div>
                                <span
                                    style="font-weight: bold; margin-left: 30%"
                                >
                                    Table showing PDF per customer
                                </span>
                            </div>
                            <div style="margin-top: 20px">
                                <a-table
                                    :columns="pdfPerCustomer"
                                    :data-source="records.pdfPerCustomer.data"
                                    :pagination="false"
                                    size="small"
                                >
                                </a-table>
                            </div>
                            <pagination
                                :datarecords="records.pdfPerCustomer"
                                class="mt-5"
                            />
                        </a-card>
                        <div style="margin-left: 80%">
                            <span style="font-weight: bold; margin-left: 3%">
                                <a-button
                                    @click="generatePdf"
                                    style="
                                        background-color: #b22222;
                                        color: white;
                                    "
                                >
                                    <FilePdfOutlined />
                                    Generate PDF
                                </a-button>
                            </span>
                        </div>
                    </a-tab-pane>
                    <!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

                    <a-tab-pane key="2">
                        <template #tab>
                            <span style="font-weight: bold">
                                <FilePdfOutlined />
                                Pdf per Barcode
                            </span>
                        </template>
                        <a-card>
                            <div v-if="isloading">
                                <div>
                                    <div id="page">
                                        <div id="container">
                                            <div id="ring"></div>
                                            <div id="ring"></div>
                                            <div id="ring"></div>
                                            <div id="ring"></div>
                                            <div
                                                style="font-weight: bold"
                                                id="h3"
                                            >
                                                Generating PDF please wait...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="input-wrapper">
                                <input
                                    type="search"
                                    placeholder="Input search here..."
                                    name="text"
                                    class="input"
                                    v-model="pdfPerBarcodeSearch"
                                />
                            </div>
                            <!-- <span style="font-weight: bold; margin-left: 60%;">
                                <a-input-search allow-clear v-model:value="pdfPerBarcodeSearch" enter-button
                                    placeholder="Input search here!" style="width: 35%;" />
                            </span> -->
                            <div style="margin-top: 10px; margin-left: 30px">
                                <span style="color: red; font-style: oblique">
                                    <span v-if="message">
                                        <WarningOutlined />
                                    </span>
                                    {{ this.message }}
                                </span>
                            </div>
                            <div>
                                <span
                                    style="font-weight: bold; margin-left: 30%"
                                >
                                    Table showing PDF per barcode
                                </span>
                            </div>
                            <div style="margin-top: 20px">
                                <!-- {{ dataCustomer }}  -->
                                <a-table
                                    :columns="pdfPerBarcode"
                                    :data-source="records.pdfPerBarcode.data"
                                    :pagination="false"
                                    size="small"
                                >
                                </a-table>
                            </div>
                            <pagination
                                :datarecords="records.pdfPerBarcode"
                                class="mt-5"
                            />
                        </a-card>
                        <div style="margin-left: 80%">
                            <span style="font-weight: bold; margin-left: 3%">
                                <a-button
                                    @click="generatePdf"
                                    style="
                                        background-color: #b22222;
                                        color: white;
                                    "
                                >
                                    <FilePdfOutlined />
                                    Generate PDF
                                </a-button>
                            </span>
                        </div>
                    </a-tab-pane>
                    <!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

                    <a-tab-pane key="3">
                        <template #tab>
                            <span style="font-weight: bold">
                                <FileExcelOutlined />
                                Excel per Customer
                            </span>
                        </template>
                        <a-card>
                            <div v-if="isloadingExcel">
                                <div>
                                    <div id="page">
                                        <div id="container">
                                            <div id="ring"></div>
                                            <div id="ring"></div>
                                            <div id="ring"></div>
                                            <div id="ring"></div>
                                            <div
                                                style="font-weight: bold"
                                                id="h3"
                                            >
                                                Generating EXCEL please wait...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="input-wrapper">
                                <input
                                    type="search"
                                    placeholder="Input search here..."
                                    name="text"
                                    class="input"
                                    v-model="spgcApprovedSearchPerCustomer"
                                />
                            </div>

                            <!-- <span style="font-weight: bold; margin-left: 60%;">
                                <a-input-search allow-clear v-model:value="spgcApprovedSearchPerCustomer" enter-button
                                    placeholder="Input search here!" style="width: 35%;" />
                            </span> -->
                            <div style="margin-top: 10px; margin-left: 30px">
                                <span style="color: red; font-style: oblique">
                                    <span v-if="message">
                                        <WarningOutlined />
                                    </span>
                                    {{ this.message }}
                                </span>
                            </div>

                            <div>
                                <span
                                    style="font-weight: bold; margin-left: 30%"
                                >
                                    Table showing EXCEL per customer
                                </span>
                            </div>
                            <div style="margin-top: 20px">
                                <!-- {{ dataCustomer }}  -->
                                <a-table
                                    :columns="perCustomerTable"
                                    :data-source="records.dataCustomer.data"
                                    :pagination="false"
                                    size="small"
                                >
                                </a-table>
                            </div>
                            <pagination
                                :datarecords="records.dataCustomer"
                                class="mt-5"
                            />
                        </a-card>
                        <span style="font-weight: bold; margin-left: 80%">
                            <a-button
                                @click="generateExcel"
                                style="background-color: green; color: white"
                            >
                                <FileExcelOutlined />
                                Generate EXCEL
                            </a-button>
                        </span>
                    </a-tab-pane>
                    <!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

                    <a-tab-pane key="4">
                        <template #tab>
                            <span style="font-weight: bold">
                                <FileExcelOutlined />
                                Excel per Barcode
                            </span>
                        </template>
                        <a-card>
                            <div v-if="isloadingExcel">
                                <div>
                                    <div id="page">
                                        <div id="container">
                                            <div id="ring"></div>
                                            <div id="ring"></div>
                                            <div id="ring"></div>
                                            <div id="ring"></div>
                                            <div
                                                style="font-weight: bold"
                                                id="h3"
                                            >
                                                Generating EXCEL please wait...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="input-wrapper">
                                <input
                                    type="search"
                                    placeholder="Input search here..."
                                    name="text"
                                    class="input"
                                    v-model="spgcApprovedSearch"
                                />
                            </div>
                            <!-- <span style="font-weight: bold; margin-left: 60%;">
                                <a-input-search allow-clear v-model:value="spgcApprovedSearch" enter-button
                                    placeholder="Input search here!" style="width: 35%;" />
                            </span> -->
                            <div style="margin-top: 10px; margin-left: 30px">
                                <span style="color: red; font-style: oblique">
                                    <span v-if="message">
                                        <WarningOutlined />
                                    </span>
                                    {{ this.message }}
                                </span>
                            </div>

                            <div>
                                <span
                                    style="font-weight: bold; margin-left: 30%"
                                >
                                    Table showing EXCEL per barcode
                                </span>
                            </div>
                            <div style="margin-top: 20px">
                                <a-table
                                    :columns="perBarcodeTable"
                                    :data-source="records.dataBarcode.data"
                                    :pagination="false"
                                    size="small"
                                >
                                </a-table>
                            </div>
                            <pagination
                                :datarecords="records.dataBarcode"
                                class="mt-5"
                            />
                        </a-card>
                        <span style="font-weight: bold; margin-left: 80%">
                            <a-button
                                @click="generateExcel"
                                style="background-color: green; color: white"
                            >
                                <FileExcelOutlined />
                                Generate EXCEL
                            </a-button>
                        </span>
                    </a-tab-pane>
                </a-tabs>
            </a-card>
        </div>

        <a-card
            style="
                width: 15%;
                border: 1px solid #dcdcdc;
                position: absolute;
                top: 48px;
            "
        >
            <div style="font-weight: bold; font-size: small">
                <span>
                    <LikeOutlined />
                </span>
                Approved GC Reports
            </div>

            <div style="font-weight: bold; margin-top: 30px">Start Date:</div>
            <div>
                <a-form-item
                    for="spgcStartDate"
                    :validate-status="
                        spgcform.errors.spgcStartDate ? 'error' : ''
                    "
                    :help="spgcform.errors.spgcStartDate"
                >
                    <a-date-picker
                        allow-clear
                        v-model:value="spgcform.spgcStartDate"
                        style="width: 100%"
                    />
                </a-form-item>
            </div>
            <div style="font-weight: bold">End Date:</div>
            <div>
                <a-form-item
                    for="spgcEndDate"
                    :validate-status="
                        spgcform.errors.spgcEndDate ? 'error' : ''
                    "
                    :help="spgcform.errors.spgcEndDate"
                >
                    <a-date-picker
                        allow-clear
                        v-model:value="spgcform.spgcEndDate"
                        style="width: 100%"
                    />
                </a-form-item>
            </div>

            <div style="margin-top: 15px">
                <a-button
                    style="
                        background-color: #1e90ff;
                        color: white;
                        width: 100%;
                        font-size: 1em;
                    "
                    @click="spgcSubmit"
                >
                    <SendOutlined /> Submit
                </a-button>
            </div>

            <div v-if="records.fromDate" style="margin-top: 15%">
                <div style="font-weight: bold">Date Selected:</div>
                <div style="margin-top: 5px">
                    <span style="color: red">
                        <div>
                            <span style="color: green; font-weight: bold">
                                FROM:
                            </span>
                            <span style="margin-left: 5px">
                                {{ records.fromDate }}
                            </span>
                        </div>
                        <span style="color: green; font-weight: bold">
                            TO:
                        </span>
                        <span style="margin-left: 25px">
                            {{ records.toDate }}
                        </span>
                    </span>
                </div>
            </div>
            <div v-if="!records.fromDate" style="margin-top: 15%">
                <span style="color: red"> No Date Selected ! </span>
            </div>
        </a-card>
        <!-- <a-button @click="sample">
            modal
        </a-button> -->
    </a-card>

    <!-- {{ records. }} -->
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
// import dayjs from 'dayjs';
import Pagination from "@/Components/Pagination.vue";
import {
    ExclamationCircleOutlined,
    WindowsFilled,
} from "@ant-design/icons-vue";
import { createVNode } from "vue";
import { message, Modal } from "ant-design-vue";
import { notification } from "ant-design-vue";
import axios from "axios";

export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        records: Object,
    },
    data() {
        return {
            isloading: false,
            isloadingExcel: false,
            message: "",
            pdfPerBarcodeSearch: this.pdfPerBarcode,
            pdfPerCustomerSearch: this.pdfPerCustomer,
            messageModal: false,
            spgcApprovedSearch: this.dataBarcode,
            spgcApprovedSearchPerCustomer: this.dataCustomer,
            spgcform: {
                spgcStartDate: "",
                spgcEndDate: "",

                errors: {},
            },
            pdfPerCustomer: [
                {
                    title: "Date Requested",
                    dataIndex: "datereq",
                },
                {
                    title: "Company",
                    dataIndex: "spcus_companyname",
                },
                {
                    title: "Approval #",
                    dataIndex: "spexgc_num",
                },
                {
                    title: "Total Amount",
                    dataIndex: "totdenom",
                },
            ],
            pdfPerBarcode: [
                {
                    title: "Date Requested",
                    dataIndex: "datereq",
                },
                {
                    title: "Denomination",
                    dataIndex: "spexgcemp_denom",
                },
                {
                    title: "Customer",
                    dataIndex: "spexgcemp_lname",
                },
                {
                    title: "Barcode",
                    dataIndex: "spexgcemp_barcode",
                },
                {
                    title: "Approval #",
                    dataIndex: "spexgc_num",
                },
                {
                    title: "Date Release",
                    dataIndex: "daterel",
                },
            ],

            perCustomerTable: [
                {
                    title: "Date Requested",
                    dataIndex: "datereq",
                },
                {
                    title: "Company",
                    dataIndex: "spcus_companyname",
                },
                {
                    title: "Approval #",
                    dataIndex: "spexgc_num",
                },
                {
                    title: "Total Amount",
                    dataIndex: "totdenom",
                },
            ],
            perBarcodeTable: [
                {
                    title: "Date Requested",
                    dataIndex: "datereq",
                },
                {
                    title: "Barcode",
                    dataIndex: "spexgcemp_barcode",
                },
                {
                    title: "Denom",
                    dataIndex: "spexgcemp_denom",
                },
                {
                    title: "Customer",
                    dataIndex: "customer_name",
                },
                {
                    title: "Approval #",
                    dataIndex: "spexgc_num",
                },
                {
                    title: "Date Approved",
                    dataIndex: "daterel",
                },
            ],
        };
    },
    watch: {
        spgcApprovedSearch(search) {
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
            const formData = {
                search: search,
                spgcStartDate: this.records.fromDate,
                spgcEndDate: this.records.toDate,
            };

            this.$inertia.get(
                route("storeaccounting.SPGCApproved", formData),
                {},
                {
                    preserveState: true,
                },
            );
        },
        spgcApprovedSearchPerCustomer(search) {
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
            const perCustomerSearch = {
                customerSearch: search,
                spgcStartDate: this.records.fromDate,
                spgcEndDate: this.records.toDate,
            };
            this.$inertia.get(
                route("storeaccounting.SPGCApproved", perCustomerSearch),
                {},
                {
                    preserveState: true,
                },
            );
        },
        pdfPerCustomerSearch(search) {
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
            const pdfPerCustomer = {
                pdfCustomerSearch: search,
                spgcStartDate: this.records.fromDate,
                spgcEndDate: this.records.toDate,
            };
            this.$inertia.get(
                route("storeaccounting.SPGCApproved", pdfPerCustomer),
                {},
                {
                    preserveState: true,
                },
            );
        },
        pdfPerBarcodeSearch(search) {
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
            const pdfPerBarcode = {
                pdfPerBarcodeSearch: search,
                spgcStartDate: this.records.fromDate,
                spgcEndDate: this.records.toDate,
            };
            this.$inertia.get(
                route("storeaccounting.SPGCApproved", pdfPerBarcode),
                {},
                {
                    preserveState: true,
                },
            );
        },
    },

    methods: {
        spgcSubmit() {
            this.spgcform.errors = {};
            const { spgcStartDate, spgcEndDate } = this.spgcform;

            const startDate = spgcStartDate
                ? spgcStartDate.format("YYYY-MM-DD")
                : "";
            const endDate = spgcEndDate ? spgcEndDate.format("YYYY-MM-DD") : "";

            if (!startDate)
                this.spgcform.errors.spgcStartDate =
                    "Start date field is required.";
            if (!endDate)
                this.spgcform.errors.spgcEndDate =
                    "End date field is required.";

            if (
                this.spgcform.errors.spgcStartDate ||
                this.spgcform.errors.spgcEndDate
            ) {
                return;
            }

            const submitData = {
                spgcStartDate: startDate,
                spgcEndDate: endDate,
            };

            console.log("Submitting with values:", submitData);
            this.$inertia.get(
                route("storeaccounting.SPGCApproved", submitData),
            );
        },

        generatePdf() {
            if (
                this.records.fromDate === null ||
                this.records.toDate === null
            ) {
                notification.warning({
                    message: "File Selection Required",
                    description: "Please select start and end date first",
                    placement: "topRight",
                });
                return;
            }

            Modal.confirm({
                title: "Notification",
                content: "Are you sure you want to generate PDF?",
                okText: "Yes",
                cancelText: "No",
                onOk: () => {
                    this.isloading = true;
                    // const hideLoading = message.loading('Generating PDF please wait...', 0);

                    axios({
                        method: "get",
                        url: route("storeaccounting.pdfApprovedSubmit"),
                        responseType: "blob",
                        params: {
                            startDate: this.records.fromDate,
                            endDate: this.records.toDate,
                        },
                    })
                        .then((response) => {
                            const fileURL = window.URL.createObjectURL(
                                new Blob([response.data]),
                            );
                            const fileLink = document.createElement("a");
                            fileLink.href = fileURL;
                            fileLink.setAttribute(
                                "download",
                                "Approved-file.pdf",
                            );
                            document.body.appendChild(fileLink);
                            fileLink.click();
                            document.body.removeChild(fileLink);

                            // hideLoading();
                            this.isloading = false;
                            message.success("PDF generated successfully!", 5);
                        })
                        .catch((error) => {
                            console.error("Error generating PDF:", error);
                            hideLoading();
                            notification.error({
                                message: "Error",
                                description:
                                    "Failed to generate PDF. Please try again later.",
                                placement: "topRight",
                            });
                        });
                },
                onCancel() {
                    console.log("Cancel");
                },
            });
        },

        generateExcel() {
            if (
                this.records.fromDate === null ||
                this.records.toDate === null
            ) {
                notification.warning({
                    message: "File Selection Required",
                    description: "Please select start and end date first",
                    placement: "topRight",
                });
                return;
            }

            Modal.confirm({
                title: "Confirmation",
                content:
                    "Are you sure you want to generate EXCEL per CUSTOMER?",
                okText: "Yes",
                cancelText: "No",
                onOk: () => {
                    this.isloadingExcel = true;
                    // const hideLoading = message.loading('Generating EXCEL, please wait...', 0);

                    axios({
                        method: "get",
                        url: route("storeaccounting.SPGCApprovedExcel"),
                        responseType: "blob",
                        params: {
                            startDate: this.records.fromDate,
                            endDate: this.records.toDate,
                        },
                    })
                        .then((response) => {
                            const fileURL = window.URL.createObjectURL(
                                new Blob([response.data]),
                            );
                            const fileLink = document.createElement("a");
                            fileLink.href = fileURL;
                            fileLink.setAttribute(
                                "download",
                                "Approved Per Customer Excel.xlsx",
                            );
                            document.body.appendChild(fileLink);
                            fileLink.click();
                            document.body.removeChild(fileLink);

                            // hideLoading();
                            this.isloadingExcel = false;
                            message.success("EXCEL generated successfully!", 5);
                        })
                        .catch((error) => {
                            console.error("Error generating EXCEL:", error);
                            hideLoading();
                            notification.error({
                                message: "Error",
                                description:
                                    "Failed to generate EXCEL. Please try again later.",
                                placement: "topRight",
                            });
                        });
                },
                onCancel() {
                    console.log("Cancel");
                },
            });
        },
    },
};
</script>
<style scope>
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
/* From Uiverse.io by Vazafirst */
#page {
    display: flex;
    justify-content: center;
    align-items: center;
}

#container {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

#h3 {
    color: rgb(82, 79, 79);
}

#ring {
    width: 190px;
    height: 190px;
    border: 1px solid transparent;
    border-radius: 50%;
    position: absolute;
}

#ring:nth-child(1) {
    border-bottom: 8px solid rgb(240, 42, 230);
    animation: rotate1 2s linear infinite;
}

@keyframes rotate1 {
    from {
        transform: rotateX(50deg) rotateZ(110deg);
    }

    to {
        transform: rotateX(50deg) rotateZ(470deg);
    }
}

#ring:nth-child(2) {
    border-bottom: 8px solid rgb(240, 19, 67);
    animation: rotate2 2s linear infinite;
}

@keyframes rotate2 {
    from {
        transform: rotateX(20deg) rotateY(50deg) rotateZ(20deg);
    }

    to {
        transform: rotateX(20deg) rotateY(50deg) rotateZ(380deg);
    }
}

#ring:nth-child(3) {
    border-bottom: 8px solid rgb(3, 170, 170);
    animation: rotate3 2s linear infinite;
}

@keyframes rotate3 {
    from {
        transform: rotateX(40deg) rotateY(130deg) rotateZ(450deg);
    }

    to {
        transform: rotateX(40deg) rotateY(130deg) rotateZ(90deg);
    }
}

#ring:nth-child(4) {
    border-bottom: 8px solid rgb(207, 135, 1);
    animation: rotate4 2s linear infinite;
}

@keyframes rotate4 {
    from {
        transform: rotateX(70deg) rotateZ(270deg);
    }

    to {
        transform: rotateX(70deg) rotateZ(630deg);
    }
}
</style>
