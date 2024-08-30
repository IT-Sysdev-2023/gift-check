<script setup>
import { highlighten } from "@/../../resources/js/Mixin/UiUtilities";
import { ref } from "vue";

const { highlightText } = highlighten();

// const currentTab = ref("Description");

// const tabs = {
//     Description,
// };
</script>
<template>

    <Head :title="title" />
    <a-breadcrumb style="margin: 15px 0">
        <a-breadcrumb-item>
            <Link :href="route(dashboardRoute)">Home</Link>
        </a-breadcrumb-item>
        <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
    </a-breadcrumb>
    <a-card>

        <div class="flex justify-between mb-5">
            <div>
                <a-range-picker v-model:value="form.date" />
            </div>
            <div>
                <a-input-search class="mr-1" v-model:value="form.search" placeholder="Search here..."
                    style="width: 300px" />
               
            </div>
        </div>
        <a-table :data-source="data.data" :columns="columns" bordered size="small" :pagination="false">
            <template #title>
                <a-typography-title :level="4">{{ title }}</a-typography-title>
            </template>
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex">
                    <span v-html="highlightText(record[column.dataIndex], form.search)
                        ">
                    </span>
                </template>

                <template v-if="column.dataIndex === 'action'">
                    <a-button type="primary" size="small" @click="viewRecord(record.id)">
                        <template #icon>
                            <FileSearchOutlined />
                        </template>
                        View
                    </a-button>
                </template>
            </template>
        </a-table>
    
        <!-- <pagination-resource class="mt-5" :datarecords="data" /> -->
    </a-card>
</template>
<script>
import AuthenticatedLayout from "@/../../resources/js/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import _ from "lodash";
import ProgressBar from "@/Components/Finance/ProgressBar.vue";

export default {
    layout: AuthenticatedLayout,
    props: {
        desc: String,
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
        async viewRecord($id) {
            try {
                const { data } = await axios.get(
                    route("treasury.view.approved.budget.ledger", $id)
                );
                this.descriptionRecord = data;
            } finally {
                this.showModal = true;
            }
        },

        start() {
            this.$inertia.get(route('start.budget.ledger'), {
                date: this.filters.date ? [dayjs(this.filters.date[0]).format('YYYY-MM-DD'), dayjs(this.filters.date[1]).format('YYYY-MM-DD')]
                    : []
            });
        }
    },

    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
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
            }, 150),
        },
    },
  
};
</script>
