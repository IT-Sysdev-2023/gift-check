<template>
    <a-card>
        <span style="margin-left: 45%; font-weight: bold; font-size: 15px">
            RELEASE GC REPORTS
        </span>
        <a-card style="width: 85%; margin-left: 16%; border: 1px solid #dcdcdc">
            <a-tabs type="card">
                <a-tab-pane key="1">
                    <template #tab>
                        <span style="font-weight: bold">
                            <FilePdfOutlined />
                            Pdf per Customer
                        </span>
                    </template>

                    <a-card style="margin-top: 5px">
                        <div v-if="isloading">
                            <div>
                                <div id="page">
                                    <div id="container">
                                        <div id="ring"></div>
                                        <div id="ring"></div>
                                        <div id="ring"></div>
                                        <div id="ring"></div>
                                        <div style="font-weight: bold" id="h3">
                                            Generating PDF please wait...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <span style="font-weight: bold; margin-left: 60%">
                            <a-input-search allow-clear v-model:value="pdfPerCustomerSearch" enter-button
                                placeholder="Input search here!" style="width: 35%" />
                        </span>
                        <div style="margin-top: 10px; margin-left: 30px">
                            <span style="color: red; font-style: oblique">
                                <span v-if="message">
                                    <WarningOutlined />
                                </span>
                                {{ this.message }}
                            </span>
                        </div>

                        <div>
                            <span style="font-weight: bold; margin-left: 30%">
                                Table showing PDF per customer
                            </span>
                        </div>
                        <div style="margin-top: 20px" v-if="data.pdfPerCustomer.data.length > 0">
                            <a-table :columns="pdfPerCustomer" :data-source="data.pdfPerCustomer.data"
                                :pagination="false" size="small">
                            </a-table>
                            <pagination :datarecords="data.pdfPerCustomer" class="mt-5" />
                        </div>
                        <div v-else>
                            <a-empty />
                        </div>
                    </a-card>
                    <span style="font-weight: bold; margin-left: 80%" v-if="data.pdfPerCustomer.data.length > 0">
                        <a-button @click="perCustumerPdf" style="background-color: #b22222; color: white">
                            <FilePdfOutlined />
                            Generate PDF
                        </a-button>
                    </span>
                </a-tab-pane>
                <!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                <a-tab-pane key="2">
                    <template #tab>
                        <span style="font-weight: bold">
                            <FilePdfOutlined />
                            Pdf per Barcode
                        </span>
                    </template>

                    <a-card style="margin-top: 5px">
                        <div v-if="isloading">
                            <div>
                                <div id="page">
                                    <div id="container">
                                        <div id="ring"></div>
                                        <div id="ring"></div>
                                        <div id="ring"></div>
                                        <div id="ring"></div>
                                        <div style="font-weight: bold" id="h3">
                                            Generating PDF please wait...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <span style="font-weight: bold; margin-left: 60%">
                            <a-input-search allow-clear v-model:value="pdfPerBarcodeSearch" enter-button
                                placeholder="Input search here!" style="width: 35%" />
                        </span>
                        <div style="margin-top: 10px; margin-left: 30px">
                            <span style="color: red; font-style: oblique">
                                <span v-if="message">
                                    <WarningOutlined />
                                </span>
                                {{ this.message }}
                            </span>
                        </div>
                        <div>
                            <span style="font-weight: bold; margin-left: 30%">
                                Table showing PDF per barcode
                            </span>
                        </div>
                        <div style="margin-top: 20px" v-if="data.pdfPerBarcode.data.length > 0">
                            <a-table :columns="pdfPerBarcode" :data-source="data.pdfPerBarcode.data" :pagination="false"
                                size="small">
                            </a-table>
                            <pagination :datarecords="data.pdfPerBarcode" class="mt-5" />
                        </div>
                        <div v-else>
                            <a-empty />
                        </div>
                    </a-card>
                    <span style="font-weight: bold; margin-left: 80%" v-if="data.pdfPerBarcode.data.length > 0">
                        <a-button @click="perCustumerPdf" style="background-color: #b22222; color: white">
                            <FilePdfOutlined />
                            Generate PDF
                        </a-button>
                    </span>
                </a-tab-pane>
                <!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                <a-tab-pane key="3">
                    <template #tab>
                        <span style="font-weight: bold">
                            <FileExcelOutlined />
                            Excel per Customer
                        </span>
                    </template>
                    <a-card style="margin-top: 5px">
                        <div v-if="isloadingExcel">
                            <div>
                                <div id="page">
                                    <div id="container">
                                        <div id="ring"></div>
                                        <div id="ring"></div>
                                        <div id="ring"></div>
                                        <div id="ring"></div>
                                        <div style="font-weight: bold" id="h3">
                                            Generating EXCEL please wait...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="input-wrapper">
                            <input
                                type="search"
                                placeholder="Input search here..."
                                name="text"
                                class="input"
                                v-model="spgcApprovedSearch"
                            />
                        </div> -->
                        <span style="font-weight: bold; margin-left: 60%">
                            <a-input-search allow-clear v-model:value="spgcApprovedSearch"
                                placeholder="Input search here!" enter-button style="width: 35%" />
                        </span>
                        <div style="margin-top: 10px; margin-left: 30px">
                            <span style="color: red; font-style: oblique">
                                <span v-if="message">
                                    <WarningOutlined />
                                </span>
                                {{ this.message }}
                            </span>
                        </div>
                        <div>
                            <span style="font-weight: bold; margin-left: 30%">
                                Table showing EXCEL per customer
                            </span>
                        </div>
                        <div style="margin-top: 20px" v-if="data.dataCustomer.data.length > 0">
                            <a-table :columns="perCustomerReleaseTable" :data-source="data.dataCustomer.data"
                                :pagination="false" size="small">
                            </a-table>
                            <pagination :datarecords="data.dataCustomer" class="mt-5" />
                        </div>
                        <div v-else>
                            <a-empty />
                        </div>
                    </a-card>
                    <span style="font-weight: bold; margin-left: 80%" v-if="data.dataCustomer.data.length > 0">
                        <a-button @click="perCustomerExcel" style="background-color: green; color: white">
                            <FileExcelOutlined />
                            Generate EXCEL
                        </a-button>
                    </span>
                </a-tab-pane>
                <!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                <a-tab-pane key="4">
                    <template #tab>
                        <span style="font-weight: bold">
                            <FileExcelOutlined />
                            Excel per Barcode
                        </span>
                    </template>

                    <a-card style="margin-top: 5px">
                        <div v-if="isloadingExcel">
                            <div>
                                <div id="page">
                                    <div id="container">
                                        <div id="ring"></div>
                                        <div id="ring"></div>
                                        <div id="ring"></div>
                                        <div id="ring"></div>
                                        <div style="font-weight: bold" id="h3">
                                            Generating EXCEL please wait...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="input-wrapper">
                            <input
                                type="search"
                                placeholder="Input search here..."
                                name="text"
                                class="input"
                                v-model="spgcApprovedSearchPerBarcode"
                            />
                        </div> -->

                        <span style="font-weight: bold; margin-left: 60%">
                            <a-input-search allow-clear v-model:value="spgcApprovedSearchPerBarcode" enter-button
                                placeholder="Input search here!" style="width: 35%" />
                        </span>
                        <div style="margin-top: 10px; margin-left: 30px">
                            <span style="color: red; font-style: oblique">
                                <span v-if="message">
                                    <WarningOutlined />
                                </span>
                                {{ this.message }}
                            </span>
                        </div>
                        <div>
                            <span style="font-weight: bold; margin-left: 30%">
                                Table showing EXCEL per barcode
                            </span>
                        </div>
                        <div style="margin-top: 20px" v-if="data.dataBarcode.data.length > 0">
                            <a-table :columns="perBarcodeReleaseTable" :data-source="data.dataBarcode.data"
                                :pagination="false" size="small">
                            </a-table>
                            <pagination :datarecords="data.dataBarcode" class="mt-5" />
                        </div>
                        <div v-else>
                            <a-empty />
                        </div>
                    </a-card>
                    <span style="font-weight: bold; margin-left: 80%" v-if="data.dataBarcode.data.length > 0">
                        <a-button @click="perCustomerExcel" style="background-color: green; color: white">
                            <FileExcelOutlined />
                            Generate EXCEL
                        </a-button>
                    </span>
                </a-tab-pane>
            </a-tabs>
        </a-card>

        <a-card style="
                width: 15%;
                position: absolute;
                top: 48px;
                border: 1px solid #dcdcdc;
            ">
            <div style="font-weight: bold; font-size: small">
                <span>
                    <ExportOutlined />
                </span>
                Release GC Reports
            </div>
            <div style="font-weight: bold; margin-top: 30px">Start Date:</div>
            <div>
                <a-form-item :validate-status="spgcData.errors.startDate ? 'error' : ''"
                    :help="spgcData.errors.startDate">
                    <a-date-picker allow-clear v-model:value="spgcData.startDate" style="width: 100%">
                    </a-date-picker>
                </a-form-item>
            </div>
            <div style="font-weight: bold">End Date:</div>
            <div>
                <a-form-item :validate-status="spgcData.errors.endDate ? 'error' : ''" :help="spgcData.errors.endDate">
                    <a-date-picker allow-clear v-model:value="spgcData.endDate" style="width: 100%">
                    </a-date-picker>
                </a-form-item>
            </div>
            <div style="margin-top: 15px">
                <a-button style="
                        background-color: #1e90ff;
                        color: white;
                        width: 100%;
                        font-size: 1em;
                    " @click="submitReleaseButton">
                    <SendOutlined /> Submit
                </a-button>
            </div>
            <div v-if="data.fromDate" style="margin-top: 15%">
                <div style="font-weight: bold">Date Selected:</div>
                <div style="margin-top: 5px">
                    <span style="color: red">
                        <div>
                            <span style="color: green; font-weight: bold">
                                FROM:
                            </span>
                            <span style="margin-left: 5px">
                                {{ data.fromDate }}
                            </span>
                        </div>
                        <span style="color: green; font-weight: bold">
                            TO:
                        </span>
                        <span style="margin-left: 25px">
                            {{ data.endDate }}
                        </span>
                    </span>
                </div>
            </div>
            <div v-if="!data.fromDate" style="margin-top: 15%">
                <span style="color: red"> No Date Selected ! </span>
            </div>
        </a-card>
    </a-card>

    <!-- {{ dataCustomer }} -->
    <!-- {{ dataBarcode }} -->
</template>
<script>
// import { defineComponent } from '@vue/composition-api'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
// import dayjs from 'dayjs';
import { Modal } from "ant-design-vue";
import Pagination from "@/Components/Pagination.vue";
import { notification } from "ant-design-vue";
import { message } from "ant-design-vue";
import axios from "axios";

export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        data: Object,
    },

    data() {
        return {
            isloading: false,
            isloadingExcel: false,
            message: "",
            pdfPerBarcodeSearch: this.pdfPerBarcode,
            pdfPerCustomerSearch: this.pdfPerCustomer,
            spgcApprovedSearchPerBarcode: "",
            spgcApprovedSearch: this.dataCustomer,
            perCustomerReleaseTable: [
                {
                    title: "Date Requested",
                    dataIndex: "datereq",
                },
                {
                    title: "Company",
                    dataIndex: "spcus_companyname",
                },
                {
                    title: "Releasing #",
                    dataIndex: "spexgc_num",
                },
                {
                    title: "Total Denomination",
                    dataIndex: "totdenom",
                },
            ],
            perBarcodeReleaseTable: [
                {
                    title: "Date Requested",
                    dataIndex: "dateRequest0",
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
                    dataIndex: "barCustomer",
                },
                {
                    title: "Releasing #",
                    dataIndex: "spexgc_num",
                },
                {
                    title: "Date Approved",
                    dataIndex: "dateRequest1",
                },
            ],
            spgcData: {
                startDate: "",
                endDate: "",
                errors: {},
            },
            isGenerating: false,
            pdfPerCustomer: [
                {
                    title: "Date Requested ",
                    dataIndex: "datereq",
                },
                {
                    title: "Company",
                    dataIndex: "spcus_companyname",
                },
                {
                    title: "Releasing Number",
                    dataIndex: "spexgc_num",
                },
                {
                    title: "Denomination",
                    dataIndex: "totdenom",
                },
                {
                    title: "Date Release",
                    dataIndex: "daterel",
                },
            ],
            pdfPerBarcode: [
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
                    dataIndex: "spexgcemp_lname",
                },
                {
                    title: "Releasing #",
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
            // alert(1)
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
            const searchData = {
                perCustomer: search,
                startDate: this.data.fromDate,
                endDate: this.data.endDate,
            };
            this.$inertia.get(
                route("storeaccounting.SPGCRelease", searchData),
                {},
                {
                    preserveState: true,
                },
            );
        },
        spgcApprovedSearchPerBarcode(search) {
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
            const searchData = {
                perBarcode: search,
                startDate: this.data.fromDate,
                endDate: this.data.endDate,
            };
            this.$inertia.get(
                route("storeaccounting.SPGCRelease", searchData),
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
            const pdfPerCustomerData = {
                pdfPerCustomer: search,
                startDate: this.data.fromDate,
                endDate: this.data.endDate,
            };
            this.$inertia.get(
                route("storeaccounting.SPGCRelease", pdfPerCustomerData),
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
            const perBarcodeData = {
                pdfPerBarcode: search,
                startDate: this.data.fromDate,
                endDate: this.data.endDate,
            };
            this.$inertia.get(
                route("storeaccounting.SPGCRelease", perBarcodeData),
                {},
                {
                    preserveState: true,
                },
            );
        },
    },
    methods: {
        submitReleaseButton() {
            this.spgcData.errors = {};
            const { startDate, endDate } = this.spgcData;

            const releasedStartDate = startDate
                ? startDate.format("YYYY-MM-DD")
                : "";
            const releasedEndDate = endDate ? endDate.format("YYYY-MM-DD") : "";

            if (!releasedStartDate)
                this.spgcData.errors.startDate = "Start Date field is required";
            if (!releasedEndDate)
                this.spgcData.errors.endDate = "End Date field is required";

            if (
                this.spgcData.errors.startDate ||
                this.spgcData.errors.endDate
            ) {
                console.error("Form validation failed");
                return;
            }

            const submitData = {
                startDate: releasedStartDate,
                endDate: releasedEndDate,
            };

            console.log("data", submitData);

            this.$inertia.get(route("storeaccounting.SPGCRelease", submitData));
        },
        perCustumerPdf() {
            if (this.data.fromDate === null || this.data.endDate === null) {
                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: "File Selection Required",
                        description: "Please select start and end date first",
                        placement: "topRight",
                    });
                };
                openNotificationWithIcon("warning");
                return;
            }
            Modal.confirm({
                title: "Confirmation",
                content: "Are you sure you want to generate PDF?",
                okText: "Yes",
                cancelText: "No",
                onOk: () => {
                    this.isloading = true;
                    // const hideLoading = message.loading('Generating PDF please wait...', 0);

                    axios({
                        method: "get",
                        url: route("storeaccounting.releasePdf"),
                        responseType: "blob",
                        params: {
                            startDate: this.data.fromDate,
                            endDate: this.data.endDate,
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
                                "Pdf-Release-file.pdf",
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

        perCustomerExcel() {
            if (this.data.fromDate === null || this.data.endDate === null) {
                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: "File Selection Required",
                        description: "Please select start and end date first",
                        placement: "topRight",
                    });
                };
                openNotificationWithIcon("warning");
                return;
            }
            Modal.confirm({
                title: "Confirmation",
                content: "Are you sure you want to generate EXCEL?",
                okText: "Yes",
                cancelText: "No",
                onOk: () => {
                    this.isloadingExcel = true;
                    // const hideLoading = message.loading('Generating EXCEL please wait...', 0);

                    axios({
                        method: "get",
                        url: route("storeaccounting.releaseExcel"),
                        responseType: "blob",
                        params: {
                            startDate: this.data.fromDate,
                            endDate: this.data.endDate,
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
                                "Release-file Excel.xlsx",
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

/* Improving visualization in light mode */
</style>
