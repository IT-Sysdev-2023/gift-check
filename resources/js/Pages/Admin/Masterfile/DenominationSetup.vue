<template>
    <a-card>
        <div>
            <a-button
                @click="backButton"
                style="color: red; border: 1px solid whitesmoke"
                ><RollbackOutlined />Back</a-button
            >
        </div>
        <div style="margin-left: 77.5%">
            <a-button
                style="background-color: #1b76f8; color: white"
                @click="() => (addDenomination = true)"
            >
                <PlusOutlined /> Add New Denomination
            </a-button>
        </div>
        <div>
            <h2>Denomination Setup</h2>
        </div>

        <div style="margin-left: 70%">
            <a-input-search
                allow-clear
                enter-button
                v-model:value="searchTerm"
                placeholder="Input search here"
                size="medium"
                style="width: 80%"
            />
        </div>
        <div style="margin-top: 10px">
            <a-table
                :columns="columns"
                :dataSource="data.data"
                :pagination="false"
                size="small"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-button
                            @click="updateDenominationData(record)"
                            title="Update"
                            class="me-2 me-sm-5"
                            style="color: white; background-color: green"
                        >
                            <FormOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>
    </a-card>

    <!-- <a-title style="font-size: 20px; display: flex; align-items: center; color:#0286df">
        <BarcodeOutlined style=" margin-right: 8px; color:#0286df" />
        Denomination Setup
    </a-title>
    <span style="font-weight: bold;">
        Show
        <a-select id="select_entries" v-model:value="dataForSelectEntries.select_entries"
            style=" margin-top: 10px;background-color: #0286df; border: 1px solid #0286df" placeholder="10"
            @change="handleSelectChange">
            <a-select-option value="10">10</a-select-option>
            <a-select-option value="25">25</a-select-option>
            <a-select-option value="50">50</a-select-option>
            <a-select-option value="100">100</a-select-option>
        </a-select>
        entries
    </span> -->

    <a-modal v-model:open="addDenomination" @ok="handleOk">
        <span style="color: #0286df; font-size: 17px">
            <BarcodeOutlined style="margin-right: 8px" />
            Add New Denomination
        </span>

        <a-form-item
            for="denomination"
            :validate-status="form.errors.denomination ? 'error' : ''"
            :help="form.errors.denomination"
            style="margin-top: 10px"
            >Denomination:
            <a-input
                allow-clear
                type="number"
                v-model:value="form.denomination"
                placeholder="Denomination"
            />
        </a-form-item>

        <a-form-item
            for="barcodeNumStart"
            :validate-status="form.errors.barcodeNumStart ? 'error' : ''"
            :help="form.errors.barcodeNumStart"
            >Barcode # Start:
            <a-input
                allow-clear
                type="number"
                v-model:value="form.barcodeNumStart"
                placeholder="Barcode # start"
            />
        </a-form-item>
    </a-modal>

    <a-modal v-model:open="updateDenominationModal" @ok="updateDenomination">
        <span style="color: #0286df; font-size: 17px">
            <BarcodeOutlined style="margin-right: 8px" />
            Update Denomination
        </span>

        <a-form-item
            for="denomination"
            :validate-status="updateDenom.errors?.denomination ? 'error' : ''"
            :help="updateDenom.errors?.denomination"
            style="margin-top: 10px"
            >Denomination:
            <a-input
                allow-clear
                type="number"
                v-model:value="updateDenom.denomination"
                placeholder="Denomination"
            />
        </a-form-item>

        <a-form-item
            for="denom_barcode_start"
            :validate-status="
                updateDenom.errors?.denom_barcode_start ? 'error' : ''
            "
            :help="updateDenom.errors?.denom_barcode_start"
            >Barcode # Start:
            <a-input
                allow-clear
                type="number"
                v-model:value="updateDenom.denom_barcode_start"
                placeholder="Barcode # start"
            />
        </a-form-item>

        <a-form-item
            for="denom_fad_item_number"
            :validate-status="
                updateDenom.errors?.denom_fad_item_number ? 'error' : ''
            "
            :help="updateDenom.errors?.denom_fad_item_number"
        >
            FAD Item #:
            <a-input
                allow-clear
                type="number"
                v-model:value="updateDenom.denom_fad_item_number"
                placeholder="FAD Item #"
            />
        </a-form-item>
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
                    preserveState: true,
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
                                "Denomination already exist, please input another denomination!",
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
                                    "No changes happen, update first before submitting!",
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
<style>
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
</style>
