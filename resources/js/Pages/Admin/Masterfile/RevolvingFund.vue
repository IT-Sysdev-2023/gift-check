<template>
    <a-card>
        <a-card title="SETUP STORE REVOLVING FUND"></a-card>

        <div style="margin-left: 70%; margin-top: 10px">
            <a-input-search
                size="medium"
                placeholder=" Search User"
                v-model:value="searchTerm"
                style="width: 80%"
            />
        </div>
        <div style="margin-top: 10px">
            <a-table
                :dataSource="data.data"
                :columns="columns"
                :pagination="false"
                size="small"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-button
                            title="Update User"
                            @click="updateFund(record)"
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
    <!-- <a-title style="font-size: 20px; display: flex; align-items: center; color: #0286df">
        <FundFilled style="margin-right: 8px; color:#0286df" />
        Setup Store Revolving Fund
    </a-title>
    <span style="font-weight: bold;">
        Show
        <a-select id="select_entries" v-model:value="dataForSelectEntries.select_entries"
            style="margin-top: 10px; background-color: #0286df; border: 1px solid #0286df" placeholder="10"
            @change="changeSelectEntries">
            <a-select-option value="10">10</a-select-option>
            <a-select-option value="25">25</a-select-option>
            <a-select-option value="50">50</a-select-option>
            <a-select-option value="100">100</a-select-option>
        </a-select>
        entries
    </span> -->

    <a-modal
        v-model:open="modalForUpdateFund"
        @ok="updatupdateRevolvingFundeFund"
    >
        <span style="color: #0286df; font-size: 17px">
            <BarChartOutlined style="margin-right: 8px; color: #0286df" />
            Update Fund
        </span>

        <a-form-item
            for="r_fund"
            :validate-status="form.errors?.r_fund ? 'error' : ''"
            :help="form.errors?.r_fund"
        >
            Revolving Fund:
            <a-input
                allow-clear
                v-model:value="form.r_fund"
                placeholder="Revolving Fund"
            />
        </a-form-item>

        <a-form-item
            for="store_status"
            :validate-status="form.errors?.store_status ? 'erros' : ''"
            :help="form.errors?.store_status"
        >
            Store Status:
            <a-select
                id="store_status"
                v-model:value="form.store_status"
                placeholder="Store Status"
            >
                <a-select-option value="active">ACTIVE</a-select-option>
                <a-select-option value="inactive">INACTIVE</a-select-option>
            </a-select>
        </a-form-item>
    </a-modal>
    <!-- {{ data }} -->
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { notification } from "ant-design-vue";
import { FundTwoTone } from "@ant-design/icons-vue";
export default {
    layout: AuthenticatedLayout,
    components: { FundTwoTone },
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
                                    message: props.flash.error,
                                    description:
                                        "No changes happen, update data first before submitting!",
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
</style>
