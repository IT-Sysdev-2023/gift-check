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
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex">
                    <span
                        v-html="
                            highlightText(record[column.dataIndex], form.search)
                        "
                    >
                    </span>
                </template>
                <template v-if="column.key === 'store'">
                    {{ record.store?.store_name }}
                </template>
                <template v-if="column.key === 'user'">
                    {{ record.user?.full_name }}
                </template>
                <template v-if="column.key === 'dateCancelled'">
                    {{ record.cancelledStoreGcRequest?.csgr_at }}
                </template>
                <template v-if="column.key === 'cancelledBy'">
                    {{ record.cancelledStoreGcRequest.user.full_name }}
                </template>

                <template v-if="column.key === 'action'">
                    <a-space>
                        <a-button
                            type="primary"
                            size="small"
                            ghost
                            @click="viewRecord(record.sgc_id)"
                        >
                            <template #icon>
                                <AuditOutlined />
                            </template>
                            View
                        </a-button>
                    </a-space>
                </template>
            </template>
        </a-table>

        <pagination-resource class="mt-5" :datarecords="data" />
        
    </a-card>
    <a-modal
            v-model:open="showModal"
            title="More Details"
            style="width: 1000px"
            centered
            :footer="null"
        >
        {{ moreDetails.denomination }}
          
            <a-table
                bordered
                size="small"
                :pagination="false"
                :columns="[
                  
                    {
                        title: 'Denomination',
                        key: 'denom',
                    },
                    {
                        title: 'Quantity',
                        dataIndex: 'qty',
                    },
                ]"
                :data-source="moreDetails?.denomination.data"
            >
                <template #bodyCell="{ column, record }">
                    <!-- <template v-if="column.key == 'denom'">
                        {{ record.denomination.denomination_format }}
                    </template>
                    <template v-if="column.key == 'date'">
                        {{ record.custodianSrrItems.custodiaSsr?.date_rec }}
                    </template>
                    <template v-if="column.key == 'validate'">
                        {{
                            record.custodianSrrItems.custodiaSsr?.user
                                ?.full_name
                        }}
                    </template> -->
                </template>
            </a-table>
            <!-- <pagination-axios
                :datarecords="moreDetails?.denomination"
                @on-pagination="forAllocationPagination"
            /> -->
        </a-modal>
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import _ from "lodash";
import axios from "axios";

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
            moreDetails: {},
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
            const { data } = await axios.get(
                route("treasury.special.gc.viewCancelledRequest", id)
            );
            this.moreDetails = data;
            this.showModal = true;
            // const url = route("treasury.transactions.eod.pdf", { id: id });

            // axios
            //     .get(url, { responseType: "blob" })
            //     .then((response) => {
            //         const file = new Blob([response.data], {
            //             type: "application/pdf",
            //         });
            //         const fileURL = URL.createObjectURL(file);
            //         window.open(fileURL, "_blank");
            //     })
            //     .catch((error) => {
            //         if (error.response && error.response.status === 404) {
            //             alert("Pdf Not available");
            //         } else {
            //             console.error(error);
            //             alert("An error occurred while generating the PDF.");
            //         }
            //     });
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
