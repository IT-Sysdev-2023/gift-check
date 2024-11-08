<template>
    <Head title="Store Sales" />
    <a-card title="Treasury Sales">
        <div class="flex justify-end">
            <a-input-search
                class="mb-2"
                v-model:value="search"
                placeholder="Input transaction # here."
                style="width: 300px"
                @search="onSearch"
            />
        </div>

        <a-table
            :dataSource="data.data"
            :columns="columns"
            size="small"
            :pagination="false"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex == 'View'">
                    <a-button @click="viewDetails(record)">
                        <template #icon>
                            <EyeOutlined />
                        </template>
                    </a-button>
                </template>
            </template>
        </a-table>

        <a-modal
            v-model:open="open"
            width="80%"
            style="top: 65px"
            :title="title"
            :confirm-loading="confirmLoading"
            @ok="handleOk"
        >
            <a-table :dataSource="selectedData.data" :columns="selectedcolumns"/>
        </a-modal>

        <pagination class="mt-5" :datarecords="data" />
    </a-card>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import debounce from "lodash/debounce";
import { PlusOutlined } from "@ant-design/icons-vue";

export default {
    layout: AuthenticatedLayout,
    PlusOutlined,
    props: {
        data: Array,
        columns: Array,
    },
    data() {
        return {
            search: "",
            open: false,
            selectedData: [],
            selectedcolumns: [
                {
                    title: "Barcode #",
                    dataIndex: "instituttritems_barcode",
                },
                {
                    title: "GC Type",
                    dataIndex: "gcType",
                },
                {
                    title: "Denomination",
                    dataIndex: "denomination",
                },
                {
                    title: "Date Verified",
                    dataIndex: "vs_date",
                },
                {
                    title: "Store Verified",
                    dataIndex: "store_name",
                },
                {
                    title: "Verified By",
                    dataIndex: "verby",
                },
                {
                    title: "Customer Name",
                    dataIndex: "customer",
                },
                {
                    title: "Balance",
                    dataIndex: "vs_tf_balance",
                },
            ],
        };
    },
    methods: {
        showModal() {
            this.open = true;
        },
        handleOk() {
            this.open = false;
        },
        handleCancel() {
            this.open = false;
        },
        viewDetails(data) {
            axios
                .get(route("marketing.treasurysales.view.treasury.sales"), {
                    params: {
                        id: data.insp_trid,
                        data: data,
                    },
                })
                .then((response) => {
                    this.open = true;
                    this.selectedData = response.data;
                });
        },
    },
    watch: {
        search: {
            deep: true,
            handler: debounce(function () {
                this.$inertia.get(
                    route("sales.treasury.sales"),
                    {
                        search: this.search,
                    },
                    {
                        preserveState: true,
                    }
                );
            }, 600),
        },
    },
};
</script>
