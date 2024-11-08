<script setup>
import { highlighten } from "@/Mixin/UiUtilities";

const { highlightText } = highlighten();
</script>
<template>
    <Head :title="title" />
    <a-breadcrumb style="margin: 15px 0">
        <a-breadcrumb-item>
            <Link :href="route(dashboardRoute)">Home</Link>
        </a-breadcrumb-item>
        <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
    </a-breadcrumb>
    <a-card :title="title">
        <div class="flex justify-between mb-5">
            <div>
                <a-range-picker v-model:value="form.date" />
            </div>
            <div>
                <a-input-search
                    class="mr-1"
                    v-model:value="form.search"
                    placeholder="Search here..."
                    style="width: 300px"
                />
            </div>
        </div>

        <a-table
            :data-source="data.data"
            :columns="columns"
            bordered
            :pagination="false"
        >
            <!-- <template #title>
                <a-typography-title :level="4">{{ title }}</a-typography-title>
            </template> -->
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex">
                    <span
                        v-html="
                            highlightText(record[column.dataIndex], form.search)
                        "
                    >
                    </span>
                </template>
                <template v-if="column.key === 'ieod_date'">
                    {{
                        dayjs(record.ieod_date).format(
                            "MMM DD, YYYY -  hh:mm a"
                        )
                    }}
                </template>
                <template v-if="column.key === 'eodBy'">
                    {{ record.user?.full_name }}
                </template>

                <template v-if="column.dataIndex === 'action'">
                    <a-space>
                        <!-- <a-button
                            type="primary"
                            size="small"
                            @click="viewRecord(record.ieod_id)"
                        >
                            <template #icon>
                                <FileSearchOutlined />
                            </template>
                            View
                        </a-button> -->

                        <a-button
                            type="primary"
                            size="small"
                            ghost
                            @click="viewRecord(record.ieod_id)"
                        >
                            <template #icon>
                                <AuditOutlined />
                            </template>
                            Reprint
                        </a-button>
                    </a-space>
                </template>
            </template>
        </a-table>

        <pagination-resource class="mt-5" :datarecords="data" />
    </a-card>
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import _ from "lodash";

export default {
    layout: AuthenticatedLayout,
    props: {
        title: String,
        data: Object,
        columns: Array,
        filters: Object,
    },
    data() {
        return {
            descriptionRecord: [],
            showModal: false,
            form: {
                search: this.filters.search,
                date: this.filters.date
                    ? [dayjs(this.filters.date[0]), dayjs(this.filters.date[1])]
                    : [],
            },
        };
    },
    computed: {
        dashboardRoute() {
            const webRoute = route().current();
            const res = webRoute?.split(".")[0];
            return res + ".dashboard";
        },
    },
    methods: {
        async viewRecord(id) {
            const url = route("treasury.transactions.eod.pdf", { id: id });

            axios
                .get(url, { responseType: "blob" })
                .then((response) => {
                    const file = new Blob([response.data], {
                        type: "application/pdf",
                    });
                    const fileURL = URL.createObjectURL(file);
                    window.open(fileURL, "_blank");
                })
                .catch((error) => {
                    if (error.response && error.response.status === 404) {
                        alert("Pdf Not available");
                    } else {
                        console.error(error);
                        alert("An error occurred while generating the PDF.");
                    }
                });
        },
    },

    watch: {
        form: {
            deep: true,
            handler: debounce(function () {
                const formattedDate = this.form.date
                    ? this.form.date.map((date) => date.format("YYYY-MM-DD"))
                    : [];

                this.$inertia.get(
                    route(route().current()),
                    { ...pickBy(this.form), date: formattedDate },
                    {
                        preserveState: true,
                    }
                );
            }, 600),
        },
    },
};
</script>
