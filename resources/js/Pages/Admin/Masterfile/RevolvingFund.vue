<template>
    <a-card>
        <div>
            <a-button class="back-button" @click="backButton" style="font-weight: bold;">
                <RollbackOutlined />Back
            </a-button>
        </div>
        <div style="margin-top: 20px;">
            <h2>Revolving Fund Setup</h2>
        </div>

        <div style="margin-left: 70%">
            <a-input-search size="medium" enter-button placeholder=" Search User" v-model:value="searchTerm" allow-clear
                style="width: 80%" />
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
            <a-table :dataSource="data.data" :columns="columns" :pagination="false" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-button title="Update User" @click="updateFund(record)" class="me-2 me-sm-5"
                            style="color: white; background-color: green">
                            <EditOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>
    </a-card>

    <a-modal v-model:open="modalForUpdateFund" @ok="updatupdateRevolvingFundeFund">
        <div style="font-family: sans-serif; font-weight: bold; font-size: 1rem; color: #1b76f8">
            <EditOutlined /> Update Revolving Fund
        </div>
        <div style="margin-top: 2rem; font-weight: bold;">

            <a-form-item for="r_fund" :validate-status="form.errors?.r_fund ? 'error' : ''" :help="form.errors?.r_fund">
                Revolving Fund:
                <a-input allow-clear v-model:value="form.r_fund" placeholder="Revolving Fund" />
            </a-form-item>

            <a-form-item for="store_status" :validate-status="form.errors?.store_status ? 'erros' : ''"
                :help="form.errors?.store_status">
                Store Status:
                <a-select id="store_status" v-model:value="form.store_status" placeholder="Store Status">
                    <a-select-option value="active">ACTIVE</a-select-option>
                    <a-select-option value="inactive">INACTIVE</a-select-option>
                </a-select>
            </a-form-item>
        </div>
    </a-modal>
    <!-- {{ data }} -->
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { notification } from "ant-design-vue";
export default {
    layout: AuthenticatedLayout,
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
                r_fund: "",
                store_status: "",
                errors: {},
            }),
            modalForUpdateFund: false,
            columns: [
                {
                    title: "Store Code",
                    dataIndex: "store_code",
                },
                {
                    title: "Store Name",
                    dataIndex: "store_name",
                    sorter: (a, b) => {
                        return a.store_name
                            .charAt(0)
                            .toUpperCase()
                            .localeCompare(
                                b.store_name.charAt(0).toUpperCase(),
                            );
                    },
                },
                {
                    title: "Store Status",
                    dataIndex: "store_status",
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
                route("admin.masterfile.revolvingFund"),
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
        updateFund(data) {
            this.modalForUpdateFund = true;
            this.form = data;
        },
        updatupdateRevolvingFundeFund() {
            this.form.errors = {};
            if (!this.form.r_fund) {
                this.form.errors.r_fund =
                    "This revolving fund field is required";
            }
            this.$inertia.post(
                route("admin.masterfile.updateRevolvingFund"),
                {
                    ...this.form,
                },
                {
                    onSuccess: ({ props }) => {
                        if (props.flash.success) {
                            notification.success({
                                message: props.flash.success,
                                description: "Fund updated successfully!",
                            });
                            this.modalForUpdateFund = false;
                            this.$inertia.get(
                                route("Admin/Masterfile/RevolvingFund"),
                            );
                        } else if (
                            props.flash.error(
                                notification.warning({
                                    message: 'Opps',
                                    description:
                                        "No changes, please update first before submitting!",
                                }),
                            )
                        );
                    },
                },
            );
        },
        changeSelectEntries(value) {
            console.log(value);
            this.$inertia.get(
                route("admin.masterfile.revolvingFund"),
                {
                    value: value,
                },
                {
                    preserveScroll: true,
                    preserveState: true,
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
.revolvingfund-search-button {
    text-align: right;
    font-weight: bold;
}

.revolvingfund-search-input {
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
