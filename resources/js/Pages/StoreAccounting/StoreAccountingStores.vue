<template>
    <a-card>
        <div>
            <h2>STORE SALES</h2>
        </div>

        <div style="margin-left: 70%; font-weight: bold; margin-top: 10px">
            <a-input-search
                allow-clear
                style="width: 90%"
                v-model:value="storeSearchBox"
                enter-button
                placeholder="Input search here!"
            />
        </div>

        <div style="margin-top: 10px">
            <a-table
                :data-source="data.data"
                :columns="storeColumns"
                :pagination="false"
                size="small"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button
                            @click="viewStore(record)"
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
    <!-- {{ data }} -->
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { notification } from "ant-design-vue";
export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        search: String,
        pagination: Number,
    },

    data() {
        return {
            storeDataForSelectEntries: {
                select_entries: this.pagination,
            },
            storeSearchBox: this.search,
            storeTransNumber: "",
            storeColumns: [
                {
                    title: "Transactions #",
                    dataIndex: "trans_number",
                    sorter: (a, b) =>
                        (a.trans_number || "").localeCompare(
                            b.trans_number || "",
                            undefined,
                            { sensitivity: "base" },
                        ),
                },
                {
                    title: "Store",
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
                    title: "Date",
                    dataIndex: "trans_date",
                    sorter: (a, b) =>
                        (a.trans_date || "").localeCompare(
                            b.trans_date || "",
                            undefined,
                            { sensitivity: "base" },
                        ),
                },
                {
                    title: "Time",
                    dataIndex: "trans_time",
                    sorter: (a, b) =>
                        (a.trans_time || "").localeCompare(
                            b.trans_time || "",
                            undefined,
                            { sensitivity: "base" },
                        ),
                },
                {
                    title: "GC pc(s)",
                    dataIndex: "totalCount",
                    sorter: (a, b) =>
                        (a.totalCount || "").localeCompare(
                            b.totalCount || "",
                            undefined,
                            { sensitivity: "base" },
                        ),
                },
                {
                    title: "Total denom",
                    dataIndex: "totalAmount",
                    sorter: (a, b) =>
                        (a.totalAmount || "").localeCompare(
                            b.totalAmount || "",
                            undefined,
                            { sensitivity: "base" },
                        ),
                },
                {
                    title: "Payment Type",
                    dataIndex: "payment_type",
                    sorter: (a, b) =>
                        (a.payment_type || "").localeCompare(
                            b.payment_type || "",
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
        storeSearchBox(searchData) {
            const searchValidation =
                /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}]/u;
            if (searchValidation.test(searchData)) {
                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: "Invalid input",
                        description:
                            "Search contains invalid symbols or emojis",
                        placement: "topRight",
                    });
                };
                openNotificationWithIcon("warning");
                return;
            }
            this.$inertia.get(
                route("storeaccounting.store"),
                {
                    search: searchData,
                },
                {
                    preserveState: true,
                },
            );
        },
    },
    methods: {
        viewStore(rec) {
            this.storeTransNumber = rec.trans_number;
            this.$inertia.get(
                route(
                    "storeaccounting.storeAccountingViewStore",
                    rec.trans_sid,
                ),
                {
                    transNumber: this.storeTransNumber,
                },
            );
        },

        storeSelectEntries(entries) {
            console.log(entries);
            this.$inertia.get(
                route("storeaccounting.store"),
                {
                    pagination: entries,
                },
                {
                    preserveState: true,
                },
            );
        },
    },
};
</script>
<style scoped>
/* From Uiverse.io by adamgiebl */
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
