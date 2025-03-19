<template>
    <a-card>
        <a-card style="width: 59%; margin-left: 42%">
            <a-tabs type="card">
                <a-tab-pane key="1">
                    <template #tab>
                        <span style="font-weight: bold">
                            <DashOutlined />
                            Tagbilaran
                        </span>
                    </template>
                    <div v-if="isloading">
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
                    <span style="margin-left: 60%; font-weight: bold">
                        <a-input-search allow-clear v-model:value="tagbilaranSearch" placeholder="Input search here!"
                            enter-button style="width: 40%" />
                    </span>
                    <div style="font-weight: bold; margin-top: 20px">
                        <span style="margin-left: 40%">Table Showing Tagbilaran</span>
                    </div>
                    <div style="margin-top: 10px" v-if="variance.tagbilaranData.length > 0">
                        <a-table :columns="varianceTable" :data-source="variance.tagbilaranData"
                            size="small">
                        </a-table>
                    </div>
                    <div v-else>
                        <a-empty />
                    </div>
                </a-tab-pane>
                <a-tab-pane key="2">
                    <template #tab>
                        <span style="font-weight: bold">
                            <DashOutlined />
                            Talibon
                        </span>
                    </template>
                    <div v-if="isloading">
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

                    <span style="margin-left: 60%; font-weight: bold">
                        <a-input-search allow-clear v-model:value="talibonSearch" placeholder="Input search here!"
                            enter-button style="width: 40%" />
                    </span>
                    <div style="font-weight: bold; margin-top: 20px">
                        <span style="margin-left: 40%">Table Showing Talibon</span>
                    </div>
                    <div v-if="variance.talibonData.length > 0" style="margin-top: 10px">
                        <a-table :columns="talibonData" :data-source="variance.talibonData"
                            size="small">
                        </a-table>
                    </div>

                    <div v-else>
                        <a-empty />
                    </div>

                </a-tab-pane>
            </a-tabs>
        </a-card>
        <a-button style="
                background-color: green;
                color: white;
                margin-top: 10px;
                margin-left: 85%;
            " @click="SelectCustomerName">
            <FileExcelOutlined />
            Generate Excel
        </a-button>

        <a-card style="width: 40%; position: absolute; top: 25px">
            <span style="margin-left: 35%; font-weight: bold; font-size: 15px">
                <span>
                    <DatabaseOutlined />
                </span>
                CHECK VARIANCE
            </span>
            <div style="font-weight: bold; margin-top: 20px">
                Customer Name:
            </div>

            <a-form-item :validate-status="varianceData.errors.customerName ? 'error' : ''
                " :help="varianceData.errors.customerName">
                <a-select v-model:value="varianceData.customerName">
                    <a-select-option v-for="item in companyNameList" :key="item.spcus_id" :value="item.spcus_id">
                        {{
                            `${item.spcus_companyname} * ${item.spcus_acctname}`
                        }}
                    </a-select-option>
                </a-select>
            </a-form-item>
            <div style="margin-top: 15px">
                <span v-if="formatted" style="color: #1e90ff; font-weight: bold">
                    Select:
                </span>
                <span v-if="formatted" style="margin-left: 10px">
                    {{ this.formatted }}
                </span>
                <span v-if="variance.formatCusName && !formatted" style="color: green; font-weight: bold">
                    Selected Customer Name:
                </span>
                <span v-if="variance.formatCusName && !formatted" style="margin-left: 10px">
                    {{ this.variance.formatCusName }}
                </span>
                <span v-if="!variance.formatCusName && !formatted" style="color: red">
                    No Selected Customer Name !
                </span>
            </div>
            <a-button style="
                    background-color: #1e90ff;
                    color: white;
                    margin-top: 10px;
                " @click="selectButton">
                <FileExcelOutlined />
                Select
            </a-button>
        </a-card>
    </a-card>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Modal, message } from "ant-design-vue";
import { notification } from "ant-design-vue";
import axios from "axios";

export default {
    layout: AuthenticatedLayout,
    props: {
        companyNameList: {
            type: Array,
            required: true,
        },
        variance: Object,
    },
    data() {
        return {
            isloading: false,

            talibonSearch: this.variance.talibonSearch,
            tagbilaranSearch: this.variance.tagbSearch,
            varianceData: this.$inertia.form({
                customerName: "",
                errors: {},
            }),
            selectedFormat: "",
            varianceTable: [
                {
                    title: "Barcode",
                    dataIndex: "barcode",
                },
                {
                    title: "Denomination",
                    dataIndex: "denom",
                },
                {
                    title: "Customer Name",
                    dataIndex: "cusname",
                },
                {
                    title: "Verify Date",
                    dataIndex: "verifydate",
                },
                {
                    title: "Store",
                    dataIndex: "store",
                },
                {
                    title: "Transaction No",
                    dataIndex: "transno",
                },
            ],
            talibonData: [
                {
                    title: "Barcode",
                    dataIndex: "barcode",
                },
                {
                    title: "Denomination",
                    dataIndex: "denom",
                },
                {
                    title: "Customer Name",
                    dataIndex: "cusname",
                },
                {
                    title: "Verify Date",
                    dataIndex: "verifydate",
                },
                {
                    title: "Store",
                    dataIndex: "store",
                },
                {
                    title: "Transaction No",
                    dataIndex: "transno",
                },
            ],
        };
    },
    computed: {
        formatted() {
            const selectedCustomer = this.companyNameList.find(
                (item) => item.spcus_id === this.varianceData.customerName,
            );
            return selectedCustomer
                ? `${selectedCustomer.spcus_companyname} - ${selectedCustomer.spcus_acctname}`
                : "";
        },
    },
    watch: {
        "varianceData.customerName"() {
            this.selectedFormat = this.formatted;
        },
        tagbilaranSearch(search) {
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
            const tagbSearch = {
                tagbSearch: search,
                customerName: this.variance.selectedCustomer,
                formatCusName: this.variance.formatCusName,
            };
            this.$inertia.get(
                route("storeaccounting.CheckVariance", tagbSearch),
                {},
                {
                    preserveState: true,
                },
            );
        },
        talibonSearch(search) {
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
            const talibonSearch = {
                talibonSearch: search,
                customerName: this.variance.selectedCustomer,
                formatCusName: this.variance.formatCusName,
            };
            this.$inertia.get(
                route("storeaccounting.CheckVariance", talibonSearch),
                {},
                {
                    preserveState: true,
                },
            );
        },
    },
    methods: {
        selectButton() {
            this.varianceData.errors = {};
            if (!this.varianceData.customerName) {
                this.varianceData.errors.customerName =
                    "Customer Name field is required";
                return;
            }
            const data = {
                customerName: this.varianceData.customerName,
                formatCusName: this.selectedFormat,
            };
            this.$inertia.get(route("storeaccounting.CheckVariance", data));
        },

        SelectCustomerName() {
            if (
                !this.variance.selectedCustomer &&
                !this.variance.formatCusName
            ) {
                notification.warning({
                    message: "Customer field required",
                    description:
                        "Please select customer name first before generating",
                });
                return;
            }
            Modal.confirm({
                title: "Confirmation",
                content: "Are you sure you want to generate EXCEL?",
                okText: "Yes",
                cancelText: "No",
                onOk: () => {
                    this.isloading = true;

                    axios({
                        method: "get",
                        url: route("storeaccounting.varianceExcelExport"),
                        responseType: "blob",
                        params: {
                            customerName: this.variance.selectedCustomer,
                            formatCusName: this.variance.formatCusName,
                            data: this.variance.talibonData
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
                                "CheckVariance-file Excel.xlsx",
                            );
                            document.body.appendChild(fileLink);
                            fileLink.click();
                            document.body.removeChild(fileLink);

                            this.isloading = false;
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
    margin-left: 60%;
}

.input-wrapper input:focus {
    outline-color: whitesmoke;
}

/* loading truck */
.loader {
    width: fit-content;
    height: fit-content;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 80px;
}

.truckWrapper {
    width: 200px;
    height: 100px;
    display: flex;
    flex-direction: column;
    position: relative;
    align-items: center;
    justify-content: flex-end;
    overflow-x: hidden;
}

/* truck upper body */
.truckBody {
    width: 130px;
    height: fit-content;
    margin-bottom: 6px;
    animation: motion 1s linear infinite;
}

/* truck suspension animation*/
@keyframes motion {
    0% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(3px);
    }

    100% {
        transform: translateY(0px);
    }
}

/* truck's tires */
.truckTires {
    width: 130px;
    height: fit-content;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0px 10px 0px 15px;
    position: absolute;
    bottom: 0;
}

.truckTires svg {
    width: 24px;
}

.road {
    width: 100%;
    height: 1.5px;
    background-color: #282828;
    position: relative;
    bottom: 0;
    align-self: flex-end;
    border-radius: 3px;
}

.road::before {
    content: "";
    position: absolute;
    width: 20px;
    height: 100%;
    background-color: #282828;
    right: -50%;
    border-radius: 3px;
    animation: roadAnimation 1.4s linear infinite;
    border-left: 10px solid white;
}

.road::after {
    content: "";
    position: absolute;
    width: 10px;
    height: 100%;
    background-color: #282828;
    right: -65%;
    border-radius: 3px;
    animation: roadAnimation 1.4s linear infinite;
    border-left: 4px solid white;
}

.lampPost {
    position: absolute;
    bottom: 0;
    right: -90%;
    height: 90px;
    animation: roadAnimation 1.4s linear infinite;
}

@keyframes roadAnimation {
    0% {
        transform: translateX(0px);
    }

    100% {
        transform: translateX(-350px);
    }
}
</style>
