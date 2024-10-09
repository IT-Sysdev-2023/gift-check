<script setup>
import { highlighten } from "@/Mixin/UiUtilities";
import { ref } from "vue";

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
    <a-card>

        <ProgressBar :progressBar="progressBar" v-if="isGenerating"/>

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
                 <template v-if="column.key === 'requestedBy'">
                    {{record.user}}
                </template>
                <template v-if="column.key === 'customer'">
                    {{record.specialExternalCustomer?.spcus_acctname}}
                </template>
                <template v-if="column.key === 'dateReleased'">
                    {{record.approvedRequest?.reqap_date}}
                </template>
                <template v-if="column.key === 'releasedBy'">
                    {{record.approvedRequest?.user.full_name}}
                </template>

                <template v-if="column.key === 'action'">
                    <a-button type="primary" size="small" @click="viewRecord(record.spexgc_id)">
                        <template #icon>
                            <FileSearchOutlined />
                        </template>
                        View
                    </a-button>
                </template>
            </template>
        </a-table>

        
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
import { router } from "@inertiajs/core";

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
        async viewRecord(id) {
            router.get(route('treasury.special.gc.viewReleasedGc', id));
            // alert(id);
            // try {
            //     const { data } = await axios.get(
            //         route("treasury.view.approved.budget.ledger", $id)
            //     );
            //     this.descriptionRecord = data;
            // } finally {
            //     this.showModal = true;
            // }
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
