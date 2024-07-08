<script setup>
import { highlighten } from "@/Mixin/UiUtilities";
import Description from "./Description.vue";
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

        <ProgressBar :progressBar="progressBar" v-if="isGenerating"/>

        <div class="flex justify-between mb-5">
            <div>
                <a-range-picker v-model:value="form.date" />
            </div>
            <div>
                <a-input-search class="mr-1" v-model:value="form.search" placeholder="Search here..."
                    style="width: 300px" />
                <a-button type="primary" @click="start" :loading="isGenerating">
                    <template #icon>
                        <FileExcelOutlined />
                    </template>
                    {{ isGenerating ? 'Generating Excel on Progress...' :'Generate Budget Legder Excel' }}
                </a-button>
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
        <a-modal v-model:open="showModal" width="1000px">
            <!-- <component :is="tabs[currentTab]" /> -->

            <Description :data="descriptionRecord" />
        </a-modal>

        <div class="flex justify-end p-2 mt-2" v-if="remainingBudget">
            <p >Remaining Budget:</p>
            &nbsp;
            <span>
                <a-typography-text keyboard  style="font-size: 13px; letter-spacing: 1px; color: blue;">{{ remainingBudget }}</a-typography-text>
                <!-- <a-tag color="blue" style="font-size: 13px; letter-spacing: 1px">{{ remainingBudget }}</a-tag> -->
            </span>
        </div>
        <pagination-resource class="mt-5" :datarecords="data" />
    </a-card>
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
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
        remainingBudget: String,
        filters: Object,
    },
    data() {
        return {
            descriptionRecord: [],
            showModal: false,
            isGenerating: false,
            form: {
                search: this.filters.search,
                date: this.filters.date
                    ? [dayjs(this.filters.date[0]), dayjs(this.filters.date[1])]
                    : [],
            },
            progressBar: {
                percentage: 0,
                message: "",
                totalRows: 0,
                currentRow: 0,
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
    mounted() {
        this.$ws.private(`generating-excel-events.${this.$page.props.auth.user.user_id}`)
            .listen(".generate-excel-ledger", (e) => {
                this.progressBar = e;
                this.isGenerating = true;
                console.log('hello');
            });
    }
};
</script>
