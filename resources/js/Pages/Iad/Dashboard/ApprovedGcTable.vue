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
    <a-card>

        <div class="flex justify-between mb-5">
            <div>
                <a-range-picker v-model:value="form.date" />
            </div>

        </div>
        <a-input-search enter-button allow-clear placeholder="Input search here..." v-model:value="approvedGcSearch" style="width:25%; margin-left:75%;"/>
        <a-table :data-source="data.data" :columns="columns" bordered size="small" :pagination="false" style="margin-top:10px">
            <template #title>
                <a-typography-title :level="4">{{ title }}</a-typography-title>
            </template>
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex">
                    <span v-html="highlightText(record[column.dataIndex], form.search)
                        ">
                    </span>
                </template>
                <template v-if="column.key">
                    <span>
                        <!-- for the dynamic implementation of object properties, just add a key in column-->
                        {{ getValue(record, column.dataIndex) }}
                    </span>
                </template>

                <template v-if="column.dataIndex === 'action'">
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
            approvedGcSearch: '',
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
        getValue(record, dataIndex) {
            return dataIndex.reduce((acc, index) => acc[index], record);
        },
        async viewRecord($id) {

            this.$inertia.get(route("iad.special.external.viewApprovedGc", $id)
            );

        },
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
        approvedGcSearch(search){
            console.log(search);
            this.$inertia.get(route('iad.special.external.approvedGc'),{
                search:search
            },{
                preserveState: true
            })
        }
    },

};
</script>
