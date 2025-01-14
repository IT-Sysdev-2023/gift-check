<template>
    <a-card>
        <div>
            <a-button
                @click="backButton"
                style="border: 1px solid whitesmoke"
                ><RollbackOutlined />Back</a-button
            >
        </div>
        <div style="margin-left: 79%">
            <a-button
                style="background-color: #1b76f8; color: white"
                @click="() => (addCreditCard = true)"
            >
                <PlusOutlined /> Add New Credit Card
            </a-button>
        </div>
        <div>
            <h2>Credit Card Setup</h2>
        </div>

        <div style="margin-left: 70%; margin-top: 10px">
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
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>
    </a-card>

    <a-modal v-model:open="addCreditCard" @ok="handleOk">
        <span style="color: #0286df; font-size: 17px">
            <CreditCardOutlined style="margin-right: 8px" />
            Add New Credit Card
        </span>

        <a-form-item
            for="ccard_name"
            :validate-status="form.errors.ccard_name ? 'error' : ''"
            :help="form.errors.ccard_name"
            style="margin-top: 10px; font-weight: bold;"
        >
            Credit Name:
            <a-input
                allow-clear
                v-model:value="form.ccard_name"
                placeholder="Credit Card Name"
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
                    dataIndex: "ccard_created",
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
            console.log(newVal);
            this.$inertia.get(
                route("admin.masterfile.creditCardSetup"),
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
<style>
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
</style>
