<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <div>
            <a-breadcrumb>
                <a-breadcrumb-item>
                    <Link :href="route('admin.dashboard')">Home</Link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ title }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </div>
        <a-card class="mt-5" title="Block Barcodes Setup">
            <!-- SHOW ALL BARCODES BUTTON  -->
            <div class="flex justify-left">
                <a-button class="bg-blue-500 text-white" type="primary" @click="showBarcodesTable">
                    <EyeOutlined /> Show Blocked/Unblocked Barcodes
                </a-button>
            </div>
            <!-- INPUT BARCODE FORM  -->
            <a-card style="width: 30rem; margin-top: 1rem">
                <div style="margin-top: 1rem">
                    <a-form-item for="barcode" :validate-status="form.errors.barcode ? 'error' : ''"
                        :help="form.errors.barcode">
                        <span style="font-size: large; font-weight: bold">Barcodes to Block:</span>
                        <a-input allow-clear type="number" @keyup.enter="submitBlockedBarcode"
                            v-model:value="form.barcode" placeholder="Scan barcodes here to block..." style="
                                font-size: x-large;
                                width: 25rem;
                                height: 5rem;
                            " />
                    </a-form-item>
                    <div style="margin-left: 19rem">
                        <a-button @click="submitBlockedBarcode" class="bg-blue-500 text-white" type="primary">
                            <CheckOutlined /> Submit
                        </a-button>
                    </div>
                </div>
            </a-card>
            <!-- TABLE PART  -->
            <a-modal v-model:open="blockedBarcodesTable" :footer="false" style="width: 50%">
                <a-card title="Blocked/Unblocked Barcodes Setup">
                    <div class="flex justify-end">
                        <a-input-search v-model:value="searchData" @change="searchBlockedBarcodes" enter-button
                            placeholder="Input search here..." allow-clear class="w-1/3" />
                    </div>
                    <!-- LOADING EFFECT DIVISION  -->
                    <div v-if="loading" style="
                            position: absolute;
                            z-index: 1000;
                            right: 0;
                            left: 0;
                            top: 6rem;
                        ">
                        <div class="spinnerContainer">
                            <div class="spinner"></div>
                            <div class="loader">
                                <p>loading</p>
                                <div class="words">
                                    <span class="word">please wait...</span>
                                    <span class="word">please wait...</span>
                                    <span class="word">please wait...</span>
                                    <span class="word">please wait...</span>
                                    <span class="word">please wait...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TABLE SHOWING BARCODES  -->
                    <div style="margin-top: 1rem">
                        <a-table :columns="columns" :data-source="props.data.data" :pagination="false" size="small">
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.dataIndex === 'action'">
                                    <a-button @click="showBarcode(record)" class="me-2 me-sm-5" title="Show Barcode"
                                        style="
                                            color: white;
                                            background-color: #1b76f8;
                                        ">
                                        <EyeOutlined />
                                    </a-button>
                                    <a-button v-if="record.status === 'blocked'" @click="unblockedBarcode(record)"
                                        class="me-2 me-sm-5" title="Unblocked Barcode" style="
                                            color: white;
                                            background-color: green;
                                        ">
                                        <UnlockOutlined />
                                    </a-button>
                                    <a-button v-if="record.status === 'free'" @click="blockedBarcodes(record)"
                                        class="me-2 me-sm-5" title="Block Barcode" style="
                                            color: red; border:1px solid red
                                        ">
                                        <StopOutlined />
                                    </a-button>
                                </template>
                            </template>
                        </a-table>
                        <pagination :datarecords="data" class="mt-5" />
                    </div>
                </a-card>
            </a-modal>
            <!--SHOWING BY BARCODE PART -->
            <a-modal v-model:open="byBarcodeTable" :footer="false" style="width: 40%">
                <a-table :columns="byBarcode" :data-source="tableData" :pagination="false" style="font-size: large;"
                    size="small">
                </a-table>
            </a-modal>
        </a-card>
        <!-- {{ data }} -->
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, createVNode, reactive } from "vue";
import { router } from "@inertiajs/core";
import { Modal, notification } from "ant-design-vue";
import { ExclamationCircleOutlined } from "@ant-design/icons-vue";

const props = defineProps({
    data: Object,
    searchValue: String,
});
const tableData = ref(props.data);
const form = reactive({
    barcode: "",
    errors: {},
});

const title = ref("Blocked Barcodes Setup");

const loading = ref(false); //LOADING BOOLEAN
const blockedBarcodesTable = ref(false); //BLOCKED BARCODE MODAL TABLE
const byBarcodeTable = ref(false); //BY BARCODE TABLE MODAL
const searchData = ref(props.searchValue); //SEARCH VALUE

// ALL BARCODES COLUMNS
const columns = ref([
    {
        title: "Barcode",
        dataIndex: "barcode",
    },
    {
        title: "Status",
        dataIndex: "status",
    },
    {
        title: "Action",
        dataIndex: "action",
    },
]);
const byBarcode = ref([
    {
        title: "Barcode",
        dataIndex: "barcode",
    },
    {
        title: "Status",
        dataIndex: "status",
    },
    {
        title: "Date Created",
        dataIndex: "created_at",
    },
    {
        title: "Date Updated",
        dataIndex: "updated_at",
    },
]);

const showBarcodesTable = () => {
    blockedBarcodesTable.value = true;
};

// SHOW PER BARCODE BUTTON
const showBarcode = (record) => {
    const filteredData = props.data.data.filter(
        (item) => item.id === record.id,
    );
    if (filteredData.length > 0) {
        byBarcodeTable.value = true;
        tableData.value = filteredData;
    } else {
        notification.warning({
            message: "NOT FOUND",
            description: "No data found for this barcode.",
        });
    }
};
// UNBLOCKED BARCODE BUTTON
const unblockedBarcode = (record) => {
    Modal.confirm({
        title: "Confirmation?",
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode(
            "div",
            {
                style: "color:black;",
            },
            [
                "Are you sure you want to",
                createVNode(
                    "span",
                    {
                        style: "color:red; ",
                    },
                    " UNBLOCKED",
                ),
                " this barcode"
            ],
        ),
        onOk: () => {
            try {
                router.get(route('admin.masterfile.unblockedBarcode'), {
                    barcode_id: record.id
                }, {
                    onSuccess: (page) => {
                        if (page.props.flash.success) {
                            notification.success({
                                message: 'SUCCESS',
                                description: page.props.flash.success
                            });

                        } else if (page.props.flash.error) {
                            notification.warning({
                                message: 'Opps',
                                description: page.props.flash.error
                            });
                        };
                    },
                    preserveState: true
                })

            } catch (error) {
                console.error("Failed to add barcode", error);
            }
        },
    })

}
// BLOCKED BARCODE AGAIN
const blockedBarcodes = (record) => {
    Modal.confirm({
        title: "Confirmation",
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode("div", {
            style: "color:black;",
        },
            [
                "Are you sure you want to ",
                createVNode(
                    "span",
                    {
                        style: "color:red;",
                    },
                    " BLOCKED",
                ),
                " this barcode"
            ],
        ),
        onOk: () => {
            try {
                router.get(route("admin.masterfile.blockedAgain"), {
                    barcode_id: record.id
                }, {
                    onSuccess: (page) => {
                        if (page.props.flash.success) {
                            notification.success({
                                message: "SUCCESS",
                                description: page.props.flash.success
                            });
                        } else if (page.props.flash.error) {
                            notification.warning({
                                message: "Opps",
                                description: page.props.flash.error
                            });
                        };
                    },
                    preserveState: true
                })
            } catch (error) {
                console.error("Failed to blocked the barcode", error)
            }
        }
    })
}
// SEARCH BARCODE PART
const searchBlockedBarcodes = () => {
    router.get(
        route("admin.masterfile.blockBarcode"),
        {
            searchBarcode: searchData.value,
        },
        {
            onStart: () => {
                loading.value = true;
            },
            onSuccess: () => {
                loading.value = false;
            },
            onError: () => {
                loading.value = false;
            },
            preserveState: true,
        },
    );
};
// ADDING BARCODE PART
const submitBlockedBarcode = () => {
    form.errors = {};
    if (!form.barcode) {
        form.errors.barcode = "Input barcode first before submitting";
        return;
    }
    try {
        router.get(
            route("admin.masterfile.addBlockedBarcode"),
            {
                ...form,
            },
            {
                onSuccess: (page) => {
                    if (page.props.flash.success) {
                        notification.success({
                            message: "SUCCESS",
                            description: page.props.flash.success,
                        });
                        form.barcode = "";
                    } else if (page.props.flash.error) {
                        notification.warning({
                            message: "Opps",
                            description: page.props.flash.error,
                        });
                    }
                },
            },
        );
    } catch (error) {
        console.error("Failed to add barcode", error);
    }
};
</script>
<style scoped>
.spinnerContainer {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.spinner {
    width: 56px;
    height: 56px;
    display: grid;
    border: 4px solid #0000;
    border-radius: 50%;
    border-right-color: #299fff;
    animation: tri-spinner 1s infinite linear;
}

.spinner::before,
.spinner::after {
    content: "";
    grid-area: 1/1;
    margin: 2px;
    border: inherit;
    border-radius: 50%;
    animation: tri-spinner 2s infinite;
}

.spinner::after {
    margin: 8px;
    animation-duration: 3s;
}

@keyframes tri-spinner {
    100% {
        transform: rotate(1turn);
    }
}

.loader {
    color: #4a4a4a;
    font-family: "Poppins", sans-serif;
    font-weight: 500;
    font-size: 25px;
    -webkit-box-sizing: content-box;
    box-sizing: content-box;
    height: 40px;
    padding: 10px 10px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    border-radius: 8px;
}

.words {
    overflow: hidden;
}

.word {
    display: block;
    height: 100%;
    padding-left: 6px;
    color: #299fff;
    animation: cycle-words 5s infinite;
}

@keyframes cycle-words {
    10% {
        -webkit-transform: translateY(-105%);
        transform: translateY(-105%);
    }

    25% {
        -webkit-transform: translateY(-100%);
        transform: translateY(-100%);
    }

    35% {
        -webkit-transform: translateY(-205%);
        transform: translateY(-205%);
    }

    50% {
        -webkit-transform: translateY(-200%);
        transform: translateY(-200%);
    }

    60% {
        -webkit-transform: translateY(-305%);
        transform: translateY(-305%);
    }

    75% {
        -webkit-transform: translateY(-300%);
        transform: translateY(-300%);
    }

    85% {
        -webkit-transform: translateY(-405%);
        transform: translateY(-405%);
    }

    100% {
        -webkit-transform: translateY(-400%);
        transform: translateY(-400%);
    }
}
</style>
