<template>
    <a-card>
        <div>
            <a-button @click="backButton" style="font-weight: bold;">
                <RollbackOutlined />Back
            </a-button>
        </div>
        <div style="margin-left: 79%">
            <a-button style="background-color: #1b76f8; color: white" @click="() => (addCreditCard = true)">
                <PlusOutlined /> Add New Credit Card
            </a-button>
        </div>
        <div>
            <h2>Credit Card Setup</h2>
        </div>

        <div style="margin-left: 70%; margin-top: 10px">
            <a-input-search allow-clear enter-button v-model:value="searchTerm" placeholder="Input search here!"
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
            <a-table :columns="columns" :data-source="data.data" :pagination="false" size="small">
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>
    </a-card>

    <a-modal v-model:open="addCreditCard" @ok="handleOk">
        <div style="font-family: sans-serif; font-weight: bold; font-size: 1rem; color: #1b76f8">
            <EditOutlined /> Add Credit Card
        </div>
        <div style="margin-top: 2rem; font-weight: bold;">

            <a-form-item for="ccard_name" :validate-status="form.errors.ccard_name ? 'error' : ''"
                :help="form.errors.ccard_name" style="margin-top: 10px; font-weight: bold;">
                Credit Name:
                <a-input allow-clear v-model:value="form.ccard_name" placeholder="Credit Card Name" />
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
                ccard_name: "",
            }),

            addNewCustomerModal: false,
            addCreditCard: false,
            columns: [
                {
                    title: "Credit Card",
                    dataIndex: "ccard_name",
                    sorter: (a, b) => {
                        return a.ccard_name
                            .charAt(0)
                            .toUpperCase()
                            .localeCompare(
                                b.ccard_name.charAt(0).toUpperCase(),
                            );
                    },
                },
                {
                    title: "Date Created",
                    dataIndex: "ccard_created_formatted",
                },
                {
                    title: "Created By",
                    dataIndex: "ccard_by",
                },
                {
                    title: "Status",
                    dataIndex: "ccard_status",
                },
            ],
        };
    },
    watch: {
        searchTerm(newVal) {
            this.$inertia.get(
                route("admin.masterfile.creditCardSetup"),
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
        handleOk() {
            // alert(1)
            this.form.get(route("admin.masterfile.saveCreditCard"), {
                preserveState: true,
                onSuccess: ({ props }) => {
                    if (props.flash.success) {
                        notification.success({
                            message: props.flash.success,
                            description: "Added successfully!",
                        });
                        this.addCreditCard = false;
                        this.form.ccard_name = "";
                        this.$inertia.get(
                            route("Admin/Masterfile/CreditCardSetup"),
                        );
                    } else if (props.flash.error) {
                        notification.error({
                            message: props.flash.error,
                            description: "Failed adding Credit Card!",
                        });
                    }
                },
            });
        },
        changeSelectEntries(value) {
            console.log(value);
            this.$inertia.get(
                route("admin.masterfile.creditCardSetup"),
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
.creditcard-button {
    text-align: right;
}

.creditcard-input {
    background-color: #1e90ff;
    color: white;
    margin-right: 6%;
}

.creditcard-search-button {
    font-weight: bold;
    text-align: right;
}

.creditcard-search-input {
    border: 1px solid #1e90ff;
    width: 20%;
    margin-right: 10%;
    min-width: 110px;
    margin-top: 1%;
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
