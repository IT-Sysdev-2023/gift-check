<template>
    <a-card>
        <div style="font-weight: bold; font-family: sans-serif; font-size: 1rem">
            <span> {{ storeName }} - Verified GC </span>
        </div>

        <div style="font-weight: bold; margin-left: 70%; margin-top: 10px">
            <a-input-search
                allow-clear
                v-model:value="talibonSearchBox"
                style="width: 90%"
                enter-button
                placeholder="Input search here!"
            />
        </div>
        <div style="margin-top: 10px">
            <a-table
                :data-source="data.data"
                :columns="alturasTalibonColumns"
                :pagination="false"
                size="small"
                style="margin-top: 10px"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button
                            @click="viewAlturasTalibon(record)"
                            class="me-2 me-sm-5"
                            style="color: white; background-color: #1e90ff"
                        >
                            <EyeOutlined />
                        </a-button>
                    </template>
                </template> 
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>
    </a-card>

    <!-- {{ storeName }} -->
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { notification } from "ant-design-vue";

export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        id: String,
        search: String,
        pagination: String,
        storeName: String,
    },

    data() {
        return {
            selectecBarcode: {},
            alturasPagination: {
                select_entries: this.pagination,
            },
            alturasSearchBox: this.search,
            alturasTalibonColumns: [
                {
                    title: "Barcode #",
                    dataIndex: "vs_barcode",
                    sorter: (a, b) =>
                        String(a.vs_barcode || "").localeCompare(
                            String(b.vs_barcode || ""),
                            undefined,
                            { sensitivity: "base" },
                        ),
                },
                {
                    title: "Denomination",
                    dataIndex: "vs_tf_denomination",
                    sorter: (a, b) =>
                        (a.vs_tf_denomination || "").localeCompare(
                            b.vs_tf_denomination || "",
                            undefined,
                            { sensitivity: "base" },
                        ),
                },
                {
                    title: "Date Verified/Reverified",
                    dataIndex: "newDate",
                    sorter: (a, b) =>
                        (a.newDate || "").localeCompare(
                            b.newDate || "",
                            undefined,
                            { sensitivity: "base" },
                        ),
                },
                {
                    title: "Verified/Reverified By",
                    dataIndex: "verby",
                    sorter: (a, b) =>
                        (a.verby || "").localeCompare(
                            b.verby || "",
                            undefined,
                            { sensitivity: "base" },
                        ),
                },
                {
                    title: "Customer",
                    dataIndex: "customer",
                    sorter: (a, b) =>
                        (a.customer || "").localeCompare(
                            b.customer || "",
                            undefined,
                            { sensitivity: "base" },
                        ),
                },
                {
                    title: "Balance",
                    dataIndex: "vs_tf_balance",
                    sorter: (a, b) =>
                        (a.vs_tf_balance || "").localeCompare(
                            b.vs_tf_balance || "",
                            undefined,
                            { sensitivity: "base" },
                        ),
                },
                {
                    title: "View",
                    dataIndex: "view",
                },
            ],
        };
    },
    watch: {
        talibonSearchBox(search) {
            // alert
            const searchValidation =
                /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}]/u;
            if (searchValidation.test(search)) {
                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: "Invalid input",
                        description: "Search contains invalid symbol or emojis",
                        placement: "topRight",
                    });
                };
                openNotificationWithIcon("warning");
                return;
            }
            this.$inertia.get(
                route("storeaccounting.alturasTalibon", this.id),
                {
                    search: search,
                },
                {
                    preserveState: true,
                },
            );
        },
    },
    methods: {
        PaginationEntries(entries) {
            console.log(entries);
            this.$inertia.get(
                route("storeaccounting.alturasTalibon", this.id),
                {
                    pagination: entries,
                },
            );
        },
        viewAlturasTalibon(rec) {
            // this.selectecBarcode = rec.vs_barcode
            this.$inertia.get(
                route("storeaccounting.talibonPosTransaction", rec.vs_barcode),
            );
        },
    },
};
</script>
<style scope>
.input-wrapper input {
    background-color: whitesmoke;
    border: none;
    padding: 1rem;
    font-size: 1rem;
    width: 16em;
    border-radius: 2rem;
    color: black;
    box-shadow: 0 0.4rem #1e90ff;
    cursor: pointer;
    margin-top: 10px;
    margin-left: 70%;
}

.input-wrapper input:focus {
    outline-color: whitesmoke;
}
</style>
