<template>
    <a-card>
        <div style="font-weight: bold; font-family: sans-serif; font-size: 1rem">
            <span> {{ storeName }} - Verified GC </span>
        </div>

        <div style="font-weight: bold; margin-left: 70%; margin-top: 10px">
            <a-input-search allow-clear v-model:value="alturasSearchBox" style="width: 90%" enter-button
                placeholder="Input search here!" />
        </div>
        <div style="margin-top: 10px">
            <a-table :data-source="data.data" :columns="alturasMallColumns" :pagination="false" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button @click="viewAlturasMall(record)" class="me-2 me-sm-5"
                            style="color: white; background-color: #1e90ff">
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
            alturasMallColumns: [
                {
                    title: "Barcode #",
                    dataIndex: "vs_barcode",
                },
                {
                    title: "Denomination",
                    dataIndex: "vs_tf_denomination",
                },
                {
                    title: "Date Verified/Reverified",
                    dataIndex: "newDate",
                },
                {
                    title: "Verified/Reverified By",
                    dataIndex: "verby",
                },
                {
                    title: "Customer",
                    dataIndex: "customer",
                },
                {
                    title: "Balance",
                    dataIndex: "vs_tf_balance",
                },
                {
                    title: "View",
                    dataIndex: "view",
                },
            ],
        };
    },
    watch: {
        alturasSearchBox(search) {
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
                route("storeaccounting.altaCitta", this.id),
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
            this.$inertia.get(route("storeaccounting.altaCitta", this.id), {
                pagination: entries,
            });
        },
        viewAlturasMall(rec) {
            // this.selectecBarcode = rec.vs_barcode
            this.$inertia.get(
                route(
                    "storeaccounting.altaCittaPosTransaction",
                    rec.vs_barcode,
                ),
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
