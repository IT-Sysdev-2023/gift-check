<template>
    <a-card>
        <div>
            <a-button class="back-button" @click="backButton" style="font-weight: bold;">
                <RollbackOutlined />Back
            </a-button>
        </div>
        <div style="margin-left: 82%">
            <a-button style="background-color: #1b76f8; color: white" @click="() => (addStore = true)">
                <PlusOutlined /> Add New Store
            </a-button>
        </div>
        <div>
            <h2>Store Setup</h2>
        </div>

        <div style="margin-left: 70%">
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
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-switch title="Issue Receipt" v-model:checked="record.status" @change="issueReceipt(record)"
                            checked-children="YES" un-checked-children="NO" :style="{
                                backgroundColor: record.status
                                    ? '#1b76f8'
                                    : 'darkgray',
                            }" />
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>
    </a-card>

    <a-modal v-model:open="addStore" @ok="handleOk">
        <header style="font-weight: bold; font-size: large; ">
            <PlusOutlined /> Add Store
        </header>
        <div style="margin-top: 2rem; font-weight: bold;">

            <a-form-item for="store_name" :validate-status="form.errors.store_name ? 'error' : ''"
                :help="form.errors.store_name" style="margin-top: 10px; font-weight: bold">Store Name:
                <a-input allow-clear v-model:value="form.store_name" placeholder="Store Name" />
            </a-form-item>

            <a-form-item for="store_code" :validate-status="form.errors.store_code ? 'error' : ''"
                :help="form.errors.store_code" style="margin-top: 10px; font-weight: bold">
                Store Code:
                <div>
                    <a-input-number id="inputNumber" v-model:value="form.store_code" placeholder="Store Code"
                        style="width: 200px" />
                </div>
            </a-form-item>

            <a-form-item for=" company_code" :validate-status="form.errors.company_code ? 'error' : ''"
                :help="form.errors.company_code" style="margin-top: 10px; font-weight: bold">
                Company Code:
                <div>
                    <a-input-number id="inputNumber" v-model:value="form.company_code" placeholder="Company Code"
                        style="width: 200px" />
                </div>
            </a-form-item>

            <a-form-item for=" default_password" :validate-status="form.errors.default_password ? 'error' : ''"
                :help="form.errors.default_password" style="margin-top: 10px; font-weight: bold">
                Default Password:
                <a-input allow-clear type="password" v-model:value="form.default_password"
                    placeholder="Default Password" />
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
            searchTerm: this.search,
            dataForSelectEntries: {
                select_entries: this.value,
            },

            form: this.$inertia.form({
                store_name: "",
                store_code: "",
                company_code: "",
                default_password: "GC2015",
            }),
            addStore: false,
            columns: [
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
                    title: "Store Code",
                    dataIndex: "store_code",
                },
                {
                    title: "Company Code",
                    dataIndex: "company_code",
                },
                {
                    title: "Default Password",
                    dataIndex: "default_password",
                },
                {
                    title: "Issue Receipt",
                    dataIndex: "action",
                },
            ],
        };
    },
    watch: {
        searchTerm(newVal) {
            this.$inertia.get(
                route("admin.masterfile.setupStore"),
                {
                    data: newVal,
                },
                {
                    onStart: () => {
                        this.loading = true;
                    },
                    onSuccess: () => {
                        this.loading = false
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
            this.form.get(route("admin.masterfile.saveStore"), {
                preserveState: true,
                onSuccess: ({ props }) => {
                    if (props.flash.success) {
                        notification.success({
                            message: props.flash.success,
                            description: "Successfully adding store!",
                        });
                        this.addStore = false;
                        this.form.store_name = "";
                        this.form.store_code = "";
                        this.form.company_code = "";
                        this.$inertia.get(route("Admin/Masterfile/SetupStore"));
                    } else if (props.flash.error) {
                        notification.error({
                            message: props.flash.error,
                            description: "Failed adding store!",
                        });
                    }
                },
            });
        },
        issueReceipt(store_id) {
            this.store_name = store_id.store_name;
            this.$inertia.get(
                route("admin.masterfile.issueReceipt"),
                {
                    store_id: store_id,
                },
                {
                    onSuccess: ({ props }) => {
                        if (props.flash.success) {
                            notification.success({
                                message: props.flash.success,
                                description: `${this.store_name} issue receipt updated successfully!`,
                            });
                        }
                    },
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
.store-button {
    text-align: right;
}

.store-input {
    background-color: #0286df;
    color: white;
    margin-right: 6%;
}

.store-search-button {
    font-weight: bold;
    text-align: right;
}

.store-search-input {
    border: 1px solid #0286df;
    width: 20%;
    margin-right: 8.2%;
    min-width: 120px;
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
