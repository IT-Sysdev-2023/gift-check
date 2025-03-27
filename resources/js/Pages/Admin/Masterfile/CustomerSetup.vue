<template>
    <a-card>
        <div>
            <a-button class="back-button" @click="backButton" style="font-weight: bold;">
                <RollbackOutlined />Back
            </a-button>
        </div>
        <div style="margin-top: 20px;">
            <h2>Customer Setup</h2>
        </div>
        <div style="margin-left: 70%;">
            <a-input-search enter-button allow-clear placeholder="input search text" v-model:value="searchTerm"
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

        <a-tabs id="tabs" v-model:value="dataFortabs.tabs" @change="tabIndentifier" style="margin-top: 10px">
            <!-- -------------------------------------------------------------------store customer---------------------------------------------------------------- -->
            <a-tab-pane key="store_customer">
                <template #tab>
                    <span style="font-weight: bold">
                        <DashOutlined />
                        Regular Customer
                    </span>
                </template>
                <a-table :columns="columns" bordered :data-source="data.data" :pagination="false" size="small">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'action'">
                            <a-button @click="updateStoreCustomer(record)" title="Update" class="me-2 me-sm-5"
                                style="color: white; background-color: green">
                                <EditOutlined />
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <pagination :datarecords="data" class="mt-5" />
            </a-tab-pane>
            <!-- ------------------------------------------------------------------institutional customer------------------------------------------------->
            <a-tab-pane key="institutional_customer">
                <template #tab>
                    <span style="font-weight: bold">
                        <DashOutlined />
                        Institutional Customer
                    </span>
                </template>
                <a-table :columns="institutionalColumns" bordered :data-source="data.data" :pagination="false"
                    size="small">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'action'">
                            <a-button @click="updateInstitutional(record)" title="Update" class="me-2 me-sm-5"
                                style="color: white; background-color: green">
                                <EditOutlined />
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <pagination :datarecords="data" class="mt-5" />
            </a-tab-pane>
            <!-- --------------------------------------------------------------------special customer---------------------------------------------------->
            <a-tab-pane key="special_customer">
                <template #tab>
                    <span style="font-weight: bold">
                        <DashOutlined />
                        Special Customer
                    </span>
                </template>
                <a-table :columns="specialColumns" bordered :data-source="data.data" :pagination="false" size="small">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'action'">
                            <a-button @click="updateSpecialCustomer(record)" title="Update" class="me-2 me-sm-5"
                                style="color: white; background-color: green">
                                <EditOutlined />
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <pagination :datarecords="data" class="mt-5" />
            </a-tab-pane>
        </a-tabs>
    </a-card>

    <a-modal v-model:open="modalForStoreCustomer" @ok="storeCustomerUpdate">
        <header style="font-weight: bold; font-size: large;">
            <EditOutlined /> Update Customer
        </header>
        <div style="margin-top: 2rem; font-weight: bold;">
            <a-form-item for="firstname" :validate-status="dataForStoreCustomer.errors?.cus_fname ? 'error' : ''
                " :help="dataForStoreCustomer.errors?.cus_fname" style="margin-top: 10px">
                Firstname:
                <a-input allow-clear v-model:value="dataForStoreCustomer.cus_fname" placeholder="Firstname" />
            </a-form-item>
            <a-form-item for="lastname" :validate-status="dataForStoreCustomer.errors?.cus_lname ? 'error' : ''
                " :help="dataForStoreCustomer.errors?.cus_lname">
                Lastname:
                <a-input allow-clear v-model:value="dataForStoreCustomer.cus_lname" placeholder="Lastname" />
            </a-form-item>
            <a-form-item for="store_register" :validate-status="dataForStoreCustomer.errors?.cus_store_register ? 'error' : ''
                " :help="dataForStoreCustomer.errors?.cus_store_register">
                Customer Store Register:
                <a-select v-model:value="dataForStoreCustomer.cus_store_register" placeholder="Customer store register">
                    <a-select-option v-for="item in store" :key="item.store_id" :value="item.store_id">
                        {{ item.store_name }}
                    </a-select-option>
                </a-select>
            </a-form-item>
        </div>
    </a-modal>

    <a-modal v-model:open="modalForInstitutionalCustomer" @ok="institutionalCustomerUpdate">
        <header style="font-weight: bold; font-size: large; ">
            <EditOutlined /> Update Institute Customer
        </header>
        <div style="margin-top: 2rem; font-weight: bold;">
            <a-form-item for="institute_name" :validate-status="dataForInstituteCustomer.errors?.ins_name ? 'error' : ''
                " :help="dataForInstituteCustomer.errors?.ins_name" style="margin-top: 10px">
                Name:
                <a-input allow-clear v-model:value="dataForInstituteCustomer.ins_name" placeholder="Name" />
            </a-form-item>

            <a-form-item for="institute_customer_type" :validate-status="dataForInstituteCustomer.errors?.ins_custype ? 'error' : ''
                " :help="dataForInstituteCustomer.errors?.ins_custype">
                Institute Customer Type:
                <a-select v-model:value="dataForInstituteCustomer.ins_custype" placeholder="Institute Customer Type">
                    <a-select-option value="internal">INTERNAL </a-select-option>
                    <a-select-option value="external">EXTERNAL </a-select-option>
                </a-select>
            </a-form-item>

            <a-form-item for="institute_gctype" :validate-status="dataForInstituteCustomer.errors?.ins_gctype ? 'error' : ''
                " :help="dataForInstituteCustomer.errors?.ins_gctype"
                v-if="dataForInstituteCustomer.ins_custype === 'internal'">
                Institute GC Type:
                <a-select v-model:value="dataForInstituteCustomer.ins_gctype" placeholder="Institute GC Type">
                    <a-select-option value="1">REGULAR</a-select-option>
                    <a-select-option value="4">PROMO</a-select-option>
                </a-select>
            </a-form-item>

            <a-form-item for="institute_status" :validate-status="dataForInstituteCustomer.errors?.ins_status ? 'error' : ''
                " :help="dataForInstituteCustomer.errors?.ins_status">
                Status:
                <a-select v-model:value="dataForInstituteCustomer.ins_status" placeholder="Institute Status">
                    <a-select-option value="active">ACTIVE</a-select-option>
                    <a-select-option value="inactive">INACTIVE</a-select-option>
                </a-select>
            </a-form-item>
        </div>
    </a-modal>

    <a-modal v-model:open="modalForSpecialCustomer" @ok="specialCustomerUpdate">
        <header style="font-weight: bold; font-size: large;">
            <EditOutlined /> Update Special Customer
        </header>
        <div style="margin-top: 2rem; font-weight: bold;">

            <a-form-item for="sp_customer_company_name" :validate-status="dataForSpecialCustomer.errors?.spcus_companyname ? 'error' : ''
                " :help="dataForSpecialCustomer.errors?.spcus_companyname" style="margin-top: 10px">
                SP Company Name:
                <a-input allow-clear v-model:value="dataForSpecialCustomer.spcus_companyname"
                    placeholder="SP Company Name" />
            </a-form-item>

            <a-form-item for="sp_customer_account_name" :validate-status="dataForSpecialCustomer.errors?.spcus_acctname ? 'error' : ''
                " :help="dataForSpecialCustomer.errors?.spcus_acctname">
                SP Account Name:
                <a-input allow-clear v-model:value="dataForSpecialCustomer.spcus_acctname"
                    placeholder="SP Company Name" />
            </a-form-item>

            <a-form-item for="sp_customer_address" :validate-status="dataForSpecialCustomer.errors?.spcus_address ? 'error' : ''
                " :help="dataForSpecialCustomer.errors?.spcus_address">
                SP Customer Type:
                <a-input allow-clear v-model:value="dataForSpecialCustomer.spcus_address"
                    placeholder="SP Customer Address" />
            </a-form-item>

            <a-form-item for="sp_contact_person" :validate-status="dataForSpecialCustomer.errors?.spcus_cperson ? 'error' : ''
                " :help="dataForSpecialCustomer.errors?.spcus_cperson">
                SP Contact Person:
                <a-input allow-clear v-model:value="dataForSpecialCustomer.spcus_cperson"
                    placeholder="SP Contact Person" />
            </a-form-item>

            <a-form-item for="sp_contact_number" :validate-status="dataForSpecialCustomer.errors?.spcus_cnumber ? 'error' : ''
                " :help="dataForSpecialCustomer.errors?.spcus_cnumber">
                SP Contact Number:
                <a-input allow-clear v-model:value="dataForSpecialCustomer.spcus_cnumber"
                    placeholder="SP Contact Number" />
            </a-form-item>

            <a-form-item for="sp_customer_type" :validate-status="dataForSpecialCustomer.errors?.spcus_type ? 'error' : ''
                " :help="dataForSpecialCustomer.errors?.spcus_type">
                SP Customer Type:
                <a-select v-model:value="dataForSpecialCustomer.spcus_type" placeholder-="SP Customer Type">
                    <a-select-option value="1">INTERNAL</a-select-option>
                    <a-select-option value="2">EXTERNAL</a-select-option>
                </a-select>
            </a-form-item>
        </div>
    </a-modal>

    <!-- {{ data }} -->
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { notification } from "ant-design-vue";
import { route } from 'ziggy-js';

export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        search: String,
        value: String,
        institutional: Object,
        special: Object,
        store: Object,
    },
    data() {
        return {
            loading: false,
            modalForStoreCustomer: false,
            dataForStoreCustomer: this.$inertia.form({
                cus_fname: "",
                cus_lname: "",
                cus_store_register: "",
                errors: {},
            }),
            dataForInstituteCustomer: this.$inertia.form({
                ins_name: "",
                ins_status: "",
                ins_custype: "",
                ins_gctype: "",
                errors: {},
            }),
            dataForSpecialCustomer: this.$inertia.form({
                spcus_companyname: "",
                spcus_acctname: "",
                spcus_address: "",
                spcus_cperson: "",
                spcus_cnumber: "",
                spcus_type: "",
                errors: {},
            }),
            modalForInstitutionalCustomer: false,
            modalForSpecialCustomer: false,
            dataForSelectEntries: {
                select_entries: this.value,
            },
            dataFortabs: {
                tabs: "",
            },
            searchTerm: this.search,
            columns: [
                {
                    title: "Firstname",
                    dataIndex: "cus_fname",
                    sorter: (a, b) => {
                        return a.cus_fname
                            .charAt(0)
                            .toUpperCase()
                            .localeCompare(b.cus_fname.charAt(0).toUpperCase());
                    },
                },
                {
                    title: "Lastname",
                    dataIndex: "cus_lname",
                    sorter: (a, b) => {
                        return a.cus_lname
                            .charAt(0)
                            .toUpperCase()
                            .localeCompare(b.cus_lname.charAt(0).toUpperCase());
                    },
                },
                {
                    title: "Customer Store Register",
                    dataIndex: "store_name",
                    filters: [
                        {
                            text: "Alturas Mall",
                            value: "Alturas Mall",
                        },
                        {
                            text: "Alturas Talibon",
                            value: "Alturas Talibon",
                        },
                        {
                            text: "Island City Mall",
                            value: "Island City Mall",
                        },
                        {
                            text: "Plaza Marcela",
                            value: "Plaza Marcela",
                        },
                        {
                            text: "Alturas Tubigon",
                            value: "Alturas Tubigon",
                        },
                        {
                            text: "Colonade Colon",
                            value: "Colonade Colon",
                        },
                        {
                            text: "Colonade Mandaue",
                            value: "Colonade Mandaue",
                        },
                        {
                            text: "Alta Citta",
                            value: "Alta Citta",
                        },
                        {
                            text: "Farmers Market",
                            value: "Farmers Market",
                        },
                        {
                            text: "Ubay Distribution Center",
                            value: "Ubay Distribution Center",
                        },
                        {
                            text: "Screenville",
                            value: "Screenville",
                        },
                        {
                            text: "Asc Tech",
                            value: "Asc Tech",
                        },
                    ],
                    onFilter: (value, record) => record.store_name === value,
                },
                {
                    title: "Customer Register At",
                    dataIndex: "cus_register_at",
                },
                {
                    title: "Customer Register By",
                    dataIndex: "fullname",
                },
                {
                    title: "Action",
                    dataIndex: "action",
                },
            ],
            institutionalColumns: [
                {
                    title: "Name",
                    dataIndex: "ins_name",
                    sorter: (a, b) => {
                        return a.ins_name
                            .charAt(0)
                            .toUpperCase()
                            .localeCompare(b.ins_name.charAt(0).toUpperCase());
                    },
                },

                {
                    title: "Customer Type",
                    dataIndex: "ins_custype",
                    filters: [
                        {
                            text: "INTERNAL",
                            value: "internal",
                        },
                        {
                            text: "EXTERNAL",
                            value: "external",
                        },
                    ],
                    onFilter: (value, record) => record.ins_custype === value,
                },
                {
                    title: "GC Type",
                    dataIndex: "ins_gctype",
                    filters: [
                        {
                            text: "REGULAR",
                            value: 1,
                        },
                        {
                            text: "PROMO",
                            value: 4,
                        },
                    ],
                    onFilter: (value, record) => record.ins_gctype === value,
                },
                {
                    title: "Date Created",
                    dataIndex: "ins_date_created",
                },
                {
                    title: "Created By",
                    dataIndex: "fullname",
                },
                {
                    title: "Status",
                    dataIndex: "ins_status",
                },
                {
                    title: "Action",
                    dataIndex: "action",
                },
            ],
            specialColumns: [
                {
                    title: "SP Customer Company Name",
                    dataIndex: "spcus_companyname",
                    sorter: (a, b) => {
                        return a.spcus_companyname
                            .charAt(0)
                            .toUpperCase()
                            .localeCompare(
                                b.spcus_companyname.charAt(0).toUpperCase(),
                            );
                    },
                },
                {
                    title: "SP Customer Account Name",
                    dataIndex: "spcus_acctname",
                    sorter: (a, b) => {
                        return a.spcus_acctname
                            .charAt(0)
                            .toUpperCase()
                            .localeCompare(
                                b.spcus_acctname.charAt(0).toUpperCase(),
                            );
                    },
                },
                {
                    title: "SP Customer Address",
                    dataIndex: "spcus_address",
                },
                {
                    title: "SP Contact Person",
                    dataIndex: "spcus_cperson",
                },
                {
                    title: "SP Contact Number",
                    dataIndex: "spcus_cnumber",
                },
                {
                    title: "SP Customer Type",
                    dataIndex: "spcus_type",
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
            console.log(newVal);
            this.$inertia.get(
                route("admin.masterfile.customer.setup"),
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
                    preserveState: true,
                },
            );
        },
    },
    methods: {
        tabIndentifier(tabs) {
            console.log(tabs);
            this.$inertia.get(
                route("admin.masterfile.customer.setup"),
                {
                    tabs: tabs,
                },
                {
                    preserveScroll: true,
                    preserveState: true,
                },
            );
        },
        changeSelectEntries(value) {
            console.log(value);
            this.$inertia.get(
                route("admin.masterfile.customer.setup"),
                {
                    value: value,
                },
                {
                    preserveScroll: true,
                    preserveState: true,
                },
            );
        },
        updateStoreCustomer(data) {
            this.modalForStoreCustomer = true;
            this.dataForStoreCustomer.cus_id = data.cus_id;
            this.dataForStoreCustomer.cus_fname = data.cus_fname;
            this.dataForStoreCustomer.cus_lname = data.cus_lname;
            this.dataForStoreCustomer.cus_store_register = data.store_name;
            this.dataForStoreCustomer.cus_store_register =
                data.cus_store_register;
        },
        storeCustomerUpdate() {
            this.dataForStoreCustomer.errors = {};
            if (!this.dataForStoreCustomer.cus_fname) {
                this.dataForStoreCustomer.errors.cus_fname =
                    "The firstname field is required";
            }
            if (!this.dataForStoreCustomer.cus_lname) {
                this.dataForStoreCustomer.errors.cus_lname =
                    "The lastname field is required";
            }
            this.$inertia.post(
                route("admin.masterfile.updateCustomerStoreRegister"),
                {
                    ...this.dataForStoreCustomer,
                },
                {
                    onSuccess: ({ props }) => {
                        if (props.flash.success) {
                            notification.success({
                                message: props.flash.success,
                                description: "Data updated successfully",
                            });
                            this.modalForStoreCustomer = false;
                        } else if (props.flash.error) {
                            notification.warning({
                                message: props.flash.error,
                                description: `${this.dataForStoreCustomer.cus_fname} data has no changes, please update first before submitting`,
                            });
                        }
                    },
                },
            );
        },

        updateInstitutional(data) {
            this.modalForInstitutionalCustomer = true;
            this.dataForInstituteCustomer.ins_id = data.ins_id;
            this.dataForInstituteCustomer.ins_name = data.ins_name;
            this.dataForInstituteCustomer.ins_custype = data.ins_custype;
            this.dataForInstituteCustomer.ins_status = data.ins_status;
            this.dataForInstituteCustomer.ins_gctype = data.ins_gctype;
        },
        institutionalCustomerUpdate() {
            this.dataForInstituteCustomer.errors = {};
            if (!this.dataForInstituteCustomer.ins_name) {
                this.dataForInstituteCustomer.errors.ins_name =
                    "The name field is required";
            }
            this.$inertia.post(
                route("admin.masterfile.UpdateInstituteCustomer"),
                {
                    ...this.dataForInstituteCustomer,
                },
                {
                    onSuccess: ({ props }) => {
                        if (props.flash.success) {
                            notification.success({
                                message: props.flash.success,
                                description: "Data updated successfully!",
                            });
                            this.modalForInstitutionalCustomer = false;
                        } else if (props.flash.error) {
                            notification.warning({
                                message: props.flash.error,
                                description: `${this.dataForInstituteCustomer.ins_name} data has no changes, please update first before submitting!`,
                            });
                        }
                    },
                },
            );
        },

        updateSpecialCustomer(data) {
            this.modalForSpecialCustomer = true;
            this.dataForSpecialCustomer.spcus_id = data.spcus_id;
            this.dataForSpecialCustomer.spcus_companyname =
                data.spcus_companyname;
            this.dataForSpecialCustomer.spcus_acctname = data.spcus_acctname;
            this.dataForSpecialCustomer.spcus_address = data.spcus_address;
            this.dataForSpecialCustomer.spcus_cperson = data.spcus_cperson;
            this.dataForSpecialCustomer.spcus_cnumber = data.spcus_cnumber;
            this.dataForSpecialCustomer.spcus_type = data.spcus_type;
        },
        specialCustomerUpdate() {
            this.dataForSpecialCustomer.errors = {};
            if (!this.dataForSpecialCustomer.spcus_companyname) {
                this.dataForSpecialCustomer.errors.spcus_companyname =
                    "The company name field is required ";
            }
            if (!this.dataForSpecialCustomer.spcus_acctname) {
                this.dataForSpecialCustomer.errors.spcus_acctname =
                    "The SP account name field is required ";
            }
            this.$inertia.post(
                route("admin.masterfile.updateSpecialCustomer"),
                {
                    ...this.dataForSpecialCustomer,
                },
                {
                    onSuccess: ({ props }) => {
                        if (props.flash.success) {
                            notification.success({
                                message: props.flash.success,
                                description: "Data updated successfully!",
                            });
                            this.modalForSpecialCustomer = false;
                        } else if (props.flash.error) {
                            notification.warning({
                                message: props.flash.error,
                                description: `${this.dataForSpecialCustomer.spcus_companyname} data has no changes, please update first before submitting!`,
                            });
                        }
                    },
                },
            );
        },
        backButton() {
            this.$inertia.get(route('admin.dashboard'))
        }
    },
};
</script>
<style scoped>
.customer-search-button {
    text-align: right;
    font-weight: bold;
}

.customer-search-input {
    margin-right: 8%;
    width: 20%;
    min-width: 120px;
    border: 1px solid #0286df;
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
