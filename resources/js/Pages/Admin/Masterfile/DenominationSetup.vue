<template>
    <a-card>
        <div>
            <a-button class="back-button" @click="backButton" style="font-weight: bold;">
                <RollbackOutlined />Back
            </a-button>
        </div>
        <div style="margin-left: 77.5%">
            <a-button style="background-color: #1b76f8; color: white" @click="() => (addDenomination = true)">
                <PlusOutlined /> Add New Denomination
            </a-button>
        </div>
        <div>
            <h2>Denomination Setup</h2>
        </div>

        <div style="margin-left: 70%">
            <a-input-search allow-clear enter-button v-model:value="searchTerm" placeholder="Input search here"
                size="medium" style="width: 80%" />
        </div>
        <div v-if="loading" style="position: absolute; z-index: 1000; right: 0; left: 0; top: 3rem">
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
        <div style="margin-top: 10px">
            <a-table :columns="columns" :dataSource="data.data" :pagination="false" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-button @click="updateDenominationData(record)" title="Update" class="me-2 me-sm-5"
                            style="color: white; background-color: green">
                            <FormOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>
    </a-card>

    <!-- Add New Denomination Modal  -->
    <a-modal v-model:open="addDenomination" @ok="handleOk">
        <div style="font-family: sans-serif; font-weight: bold; font-size: 1rem; color: #1b76f8">
            <EditOutlined /> Add Denomination
        </div>
        <div style="margin-top: 2rem; font-weight: bold;">

            <a-form-item for="denomination" :validate-status="form.errors.denomination ? 'error' : ''"
                :help="form.errors.denomination" style="margin-top: 10px; font-weight: bold">Denomination:
                <a-input allow-clear type="number" v-model:value="form.denomination" placeholder="Denomination" />
            </a-form-item>

            <a-form-item for="barcodeNumStart" :validate-status="form.errors.barcodeNumStart ? 'error' : ''"
                :help="form.errors.barcodeNumStart" style="margin-top: 10px; font-weight: bold">Barcode # Start:
                <a-input allow-clear type="number" v-model:value="form.barcodeNumStart" placeholder="Barcode # start" />
            </a-form-item>
        </div>
    </a-modal>

    <!-- Update Denomination Modal  -->
    <a-modal v-model:open="updateDenominationModal" @ok="updateDenomination">
        <div style="font-family: sans-serif; font-weight: bold; font-size: 1rem; color: #1b76f8">
            <EditOutlined /> Update Denomination
        </div>
        <div style="margin-top: 2rem; font-weight: bold;">

            <a-form-item for="denomination" :validate-status="updateDenom.errors?.denomination ? 'error' : ''"
                :help="updateDenom.errors?.denomination" style="margin-top: 10px; font-weight: bold">Denomination:
                <a-input allow-clear type="number" v-model:value="updateDenom.denomination"
                    placeholder="Denomination" />
            </a-form-item>

            <a-form-item for="denom_barcode_start" :validate-status="updateDenom.errors?.denom_barcode_start ? 'error' : ''
                " style="margin-top: 10px; font-weight: bold" :help="updateDenom.errors?.denom_barcode_start">Barcode #
                Start:
                <a-input allow-clear type="number" v-model:value="updateDenom.denom_barcode_start"
                    placeholder="Barcode # start" />
            </a-form-item>

            <a-form-item for="denom_fad_item_number" :validate-status="updateDenom.errors?.denom_fad_item_number ? 'error' : ''
                " :help="updateDenom.errors?.denom_fad_item_number" style="margin-top: 10px; font-weight: bold">
                FAD Item #:
                <a-input allow-clear type="number" v-model:value="updateDenom.denom_fad_item_number"
                    placeholder="FAD Item #" />
            </a-form-item>
        </div>
    </a-modal>
    <!-- {{ data }} -->
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { FormOutlined } from "@ant-design/icons-vue";
import { notification } from "ant-design-vue";

export default {
    layout: AuthenticatedLayout,
    components: {
        FormOutlined,
    },
    props: {
        data: Object,
        search: String,
        value: String,
    },
    data() {
        return {
            loading: false,
            dataForSelectEntries: {
                select_entries: this.value,
            },
            searchTerm: this.search,
            form: this.$inertia.form({
                denomination: "",
                barcodeNumStart: "",
            }),
            updateDenom: this.$inertia.form({
                denomination: "",
                denom_barcode_start: "",
                errors: {},
            }),

            updateDenominationModal: false,
            addDenomination: false,
            columns: [
                {
                    title: "Denomination",
                    dataIndex: "denomination",
                },
                {
                    title: "FAD Item Number",
                    dataIndex: "denom_fad_item_number",
                },
                {
                    title: "Barcode # Start",
                    dataIndex: "denom_barcode_start",
                },
                {
                    title: "Action",
                    dataIndex: "action",
                },
            ],
        };
    },
    watch: {
        searchTerm(newVal) {
            this.$inertia.get(
                route("admin.masterfile.denominationSetup"),
                {
                    data: newVal,
                },
                {
                    onStart: () => {
                        this.loading = true;
                    },
                    onSuccess: () => {
                        this.loading = false;
                    },
                    onError: () => {
                        this.loading = false;
                    },
                    preserveState: true
                },
            );
        },
    },
    methods: {
        handleOk() {
            this.form.get(route("admin.masterfile.saveDenomination"), {
                preserveState: true,
                onSuccess: ({ props }) => {
                    if (props.flash.success) {
                        notification.success({
                            message: props.flash.success,
                            description: "Denomination successfully save!",
                        });
                        this.addDenomination = false;
                        this.form.denomination = "";
                        this.form.barcodeNumStart = "";
                    } else if (props.flash.error) {
                        notification.warning({
                            message: props.flash.error,
                            description:
                                "Denomination already exist, please try another denomination!",
                        });
                    }
                },
            });
        },
        updateDenominationReset() {
            this.form = {
                denomination: "",
                denom_barcode_start: "",
                denom_fad_item_number: "",
            };
        },
        updateDenominationData(data) {
            this.updateDenominationModal = true;
            this.updateDenom = data;
        },
        updateDenomination() {
            this.updateDenom.errors = {};
            if (!this.updateDenom.denomination) {
                this.updateDenom.errors.denomination =
                    "The denomination field is required";
            }
            if (!this.updateDenom.denom_barcode_start) {
                this.updateDenom.errors.denom_barcode_start =
                    "The barcode number field is required ";
            }
            if (!this.updateDenom.denom_fad_item_number) {
                this.updateDenom.errors.denom_fad_item_number =
                    "The Fad Item Number is required ";
            }
            this.$inertia.post(
                route("admin.masterfile.saveUpdateDenomination"),
                { ...this.updateDenom },
                {
                    onSuccess: ({ props }) => {
                        if (props.flash.success) {
                            notification.success({
                                message: props.flash.success,
                                description:
                                    "Denomination updated successfully!",
                            });
                            this.updateDenominationModal = false;
                        } else if (props.flash.error) {
                            notification.warning({
                                message: props.flash.error,
                                description:
                                    "No changes, please update first before submitting!",
                            });
                        }
                    },
                },
            );
        },
        handleSelectChange(value) {
            console.log(value);
            this.$inertia.get(
                route("admin.masterfile.denominationSetup"),
                {
                    value: value,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                },
            );
        },
        backButton() {
            this.$inertia.get(route("admin.dashboard"));
        },
    },
};
</script>
<style scoped>
.denomination-button {
    text-align: right;
}

.denomination-input {
    background-color: #0286df;
    color: white;
    margin-right: 6%;
}

.denomination-search-button {
    font-weight: bold;
    text-align: right;
}

.denomination-search-input {
    border: 1px solid #0286df;
    width: 20%;
    margin-right: 10%;
    min-width: 110px;
    margin-top: 1%;
}

.back-button {
    font-weight: bold;
    font-family: "Poppins", sans-serif;
}

/* loading css  */
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
