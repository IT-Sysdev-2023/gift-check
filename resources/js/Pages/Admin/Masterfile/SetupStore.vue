<template>
    <a-card>
        <div>
            <a-button
                @click="backButton"
                style="color: red; border: 1px solid whitesmoke"
                ><RollbackOutlined />Back</a-button
            >
        </div>
        <div style="margin-left: 82%">
            <a-button
                style="background-color: #1b76f8; color: white"
                @click="() => (addStore = true)"
            >
                <PlusOutlined /> Add New Store
            </a-button>
        </div>
        <div>
            <h2>Store Setup</h2>
        </div>

        <div style="margin-left: 70%">
            <a-input-search
                allow-clear
                enter-button
                v-model:value="searchTerm"
                placeholder="Input search here!"
                size="medium"
                style="width: 80%"
            />
        </div>
        <div style="margin-top: 10px">
            <a-table
                :columns="columns"
                :data-source="data.data"
                :pagination="false"
                size="small"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-switch
                            title="Issue Receipt"
                            v-model:checked="record.status"
                            @change="issueReceipt(record)"
                            checked-children="YES"
                            un-checked-children="NO"
                            :style="{
                                backgroundColor: record.status
                                    ? '#1b76f8'
                                    : 'darkgray',
                            }"
                        />
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>
    </a-card>

    <a-modal v-model:open="addStore" @ok="handleOk">
        <span style="color: #0286df; font-size: 17px">
            <AppstoreAddOutlined style="margin-right: 8px" />
            Add New Store
        </span>

        <a-form-item
            for="store_name"
            :validate-status="form.errors.store_name ? 'error' : ''"
            :help="form.errors.store_name"
            style="margin-top: 10px"
            >Store Name:
            <a-input
                allow-clear
                v-model:value="form.store_name"
                placeholder="Store Name"
            />
        </a-form-item>

        <a-form-item
            for="store_code"
            :validate-status="form.errors.store_code ? 'error' : ''"
            :help="form.errors.store_code"
        >
            Store Code:
            <div>
                <a-input-number
                    id="inputNumber"
                    v-model:value="form.store_code"
                    placeholder="Store Code"
                    style="width: 200px"
                />
            </div>
        </a-form-item>

        <a-form-item
            for=" company_code"
            :validate-status="form.errors.company_code ? 'error' : ''"
            :help="form.errors.company_code"
        >
            Company Code:
            <div>
                <a-input-number
                    id="inputNumber"
                    v-model:value="form.company_code"
                    placeholder="Company Code"
                    style="width: 200px"
                />
            </div>
        </a-form-item>

        <a-form-item
            for=" default_password"
            :validate-status="form.errors.default_password ? 'error' : ''"
            :help="form.errors.default_password"
        >
            Default Password:
            <a-input
                allow-clear
                type="password"
                v-model:value="form.default_password"
                placeholder="Default Password"
            />
        </a-form-item>
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
            console.log(newVal);
            this.$inertia.get(
                route("admin.masterfile.setupStore"),
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
            // alert(1)
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
        changeSelectEntries(value) {
            console.log(value);
            this.$inertia.get(
                route("admin.masterfile.setupStore"),
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
<style>
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
</style>
